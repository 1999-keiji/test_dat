<?php

declare(strict_types=1);

namespace App\Services\Plan;

use Exception;
use PDOException;
use Illuminate\Database\Connection;
use Illuminate\Auth\AuthManager;
use Illuminate\Pagination\LengthAwarePaginator;
use Cake\Chronos\Chronos;
use Maatwebsite\Excel\Excel;
use App\Exceptions\PageOverException;
use App\Models\Master\FactorySpecies;
use App\Models\Master\Collections\FactoryGrowingStageCollection;
use App\Models\Plan\ArrangementDetailState;
use App\Models\Plan\BedState;
use App\Repositories\Plan\ArrangementDetailStateRepository;
use App\Repositories\Plan\ArrangementStateRepository;
use App\Repositories\Plan\BedStateRepository;
use App\Repositories\Plan\CultivationStateRepository;
use App\Repositories\Plan\PanelStateRepository;
use App\Repositories\Plan\SeedingPlanRepository;
use App\ValueObjects\Date\WorkingDate;

class BedStateService
{
    /**
     * @var \Illuminate\Database\Connection
     */
    private $db;

    /**
     * @var \Illuminate\Auth\AuthManager
     */
    private $auth;

    /**
     * @var \Maatwebsite\Excel\Excel
     */
    private $excel;

    /**
     * @var \App\Repositories\Plan\BedStateRepository
     */
    private $bed_state_repo;

    /**
     * @var \App\Repositories\Plan\SeedingPlanRepository
     */
    private $seeding_plan_repo;

    /**
     * @var \App\Repositories\Plan\CultivationStateRepository $cultivation_state_repo
     */
    private $cultivation_state_repo;

    /**
     * @var \App\Repositories\Plan\ArrangementStateRepository $arrangement_state_repo
     */
    private $arrangement_state_repo;

    /**
     * @var \App\Repositories\Plan\ArrangementDetailStateRepository $arrangement_detail_state_repo
     */
    private $arrangement_detail_state_repo;

    /**
     * @var \App\Repositories\Plan\PanelStateRepository
     */
    private $panel_state_repo;

    /**
     * @var string
     */
    private const EXCEL_FONT_FAMILY = 'MS PGothic';

    /**
     * @var int
     */
    private const EXCEL_ZOOM_SCALE = 40;

    /**
     * @param  \Illuminate\Database\Connection $db
     * @param  \Illuminate\Auth\AuthManager $auth
     * @param  \Maatwebsite\Excel\Excel $excel
     * @param  \App\Repositories\Plan\BedStateRepository $bed_state_repo
     * @param  \App\Repositories\Plan\SeedingPlanRepository $seeding_plan_repo
     * @param  \App\Repositories\Plan\CultivationStateRepository $cultivation_state_repo
     * @param  \App\Repositories\Plan\ArrangementStateRepository $arrangement_state_repo
     * @param  \App\Repositories\Plan\ArrangementDetailStateRepository $arrangement_detail_state_repo
     * @param  \App\Repositories\Plan\PanelStateRepository $panel_state_repo
     * @return void
     */
    public function __construct(
        Connection $db,
        AuthManager $auth,
        Excel $excel,
        BedStateRepository $bed_state_repo,
        SeedingPlanRepository $seeding_plan_repo,
        CultivationStateRepository $cultivation_state_repo,
        ArrangementStateRepository $arrangement_state_repo,
        ArrangementDetailStateRepository $arrangement_detail_state_repo,
        PanelStateRepository $panel_state_repo
    ) {
        $this->db = $db;
        $this->auth = $auth;
        $this->excel = $excel;

        $this->bed_state_repo = $bed_state_repo;
        $this->seeding_plan_repo = $seeding_plan_repo;
        $this->cultivation_state_repo = $cultivation_state_repo;
        $this->arrangement_state_repo = $arrangement_state_repo;
        $this->arrangement_detail_state_repo = $arrangement_detail_state_repo;
        $this->panel_state_repo = $panel_state_repo;
    }

    /**
     * ベッド状況を条件に応じて検索
     *
     * @param  array $params
     * @param  int $page
     * @param  array $order
     * @return \Illuminate\Pagination\LengthAwarePaginator
     * @throws \App\Exceptions\PageOverException
     */
    public function searchBedStates(array $params, int $page, array $order): LengthAwarePaginator
    {
        $bed_states = $this->bed_state_repo->searchBedStates($params, $order);
        if ($page > $bed_states->lastPage() && $bed_states->lastPage() !== 0) {
            throw new PageOverException('target page does not exist.');
        }

        return $bed_states;
    }

    /**
     * ベッド状況の登録
     *
     * @param  \App\Models\Master\FactorySpecies $factory_species
     * @param  \App\ValueObjects\Date\WorkingDate $start_of_week
     * @return void
     */
    public function createBedState(FactorySpecies $factory_species, WorkingDate $start_of_week): void
    {
        try {
            $bed_state = $this->bed_state_repo->findBedState($factory_species, $start_of_week);
            if (! is_null($bed_state)) {
                $this->deleteBedState($bed_state);
            }

            $bed_state = $this->bed_state_repo->create($factory_species, $start_of_week);
        } catch (PDOException $e) {
            report($e);
            return;
        }

        try {
            ini_set('max_execution_time', '900');

            $this->db->transaction(function () use ($bed_state, $factory_species, $start_of_week) {
                $user_code = $this->auth->user()->user_code ?? '';

                $factory = $factory_species->factory;
                $seeding_stage = $factory_species->factory_growing_stages->first();
                $working_date = $start_of_week;

                $seeding_plans = [];
                while ($working_date->lte($start_of_week->endOfWeek())) {
                    if (! $working_date->isWorkingDay($factory)) {
                        $working_date = $working_date->addDay();
                        continue;
                    }

                    $panel_state = $this->panel_state_repo
                        ->getPanelCountAfterSeeding($factory_species, $working_date->format('Y-m-d'));
                    if (is_null($panel_state)) {
                        $working_date = $working_date->addDay();
                        continue;
                    }

                    $multiplicated = $panel_state->panel_count * $panel_state->number_of_holes;
                    $number_of_trays = (int)(
                        ceil(ceil($multiplicated / $seeding_stage->yield_rate) / $seeding_stage->number_of_holes)
                    );

                    $seeding_plans[] = [
                        'factory_code' => $bed_state->factory_code,
                        'factory_species_code' => $bed_state->factory_species_code,
                        'start_of_week' => $bed_state->start_of_week,
                        'working_date' => $working_date->format('Y-m-d'),
                        'number_of_trays' => $number_of_trays,
                        'created_by' => $user_code,
                        'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                        'updated_by' => $user_code,
                        'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
                    ];

                    $working_date = $working_date->addDay();
                }

                $this->seeding_plan_repo->createSeedingPlans($seeding_plans);

                $panel_states = $this->panel_state_repo
                    ->getPanelStatesByFactoryAndWorkingDate($factory, $start_of_week);

                $cultivation_states = [];
                foreach ($panel_states->filterByFactorySpecies($factory_species)
                    ->filterJustDropped()
                    ->filterFrontestPanels()
                    ->groupByGrowingStageSequenceNumber() as $growing_stage_sequence_number => $grouped) {
                    $factory_growing_stage = $factory_species->factory_growing_stages
                        ->findBySequenceNumber($growing_stage_sequence_number);

                    foreach ($grouped->groupByDate() as $date => $grouped_per_date) {
                        $working_date = WorkingDate::parse($date);
                        if (! $working_date->isWorkingDay($factory)) {
                            continue;
                        }

                        $factory_cycle_pattern_items = $factory_growing_stage->factory_cycle_pattern
                            ->factory_cycle_pattern_items
                            ->filetrByDayOfTheWeek($working_date->dayOfWeek % WorkingDate::DAYS_PER_WEEK);

                        $cultivation_state = [
                            'factory_code' => $bed_state->factory_code,
                            'factory_species_code' => $bed_state->factory_species_code,
                            'start_of_week' => $bed_state->start_of_week,
                            'growing_stage_sequence_number' => $growing_stage_sequence_number,
                            'working_date' => $working_date->format('Y-m-d'),
                            'day_of_the_week' => $working_date->dayOfWeek % WorkingDate::DAYS_PER_WEEK,
                            'number_of_holes' => $factory_growing_stage->number_of_holes,
                            'patterns_number' => $factory_cycle_pattern_items->count(),
                            'floor_number' => $factory->number_of_floors,
                            'created_by' => $user_code,
                            'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                            'updated_by' => $user_code,
                            'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
                        ];

                        foreach (range(1, 10) as $idx) {
                            $cultivation_state["moving_panel_count_pattern_{$idx}"] = null;
                        }

                        foreach ($factory_cycle_pattern_items->values() as $idx => $fcpi) {
                            $idx = $idx + 1;
                            $cultivation_state["moving_panel_count_pattern_{$idx}"] = $fcpi->number_of_panels;
                        }

                        foreach (range(1, $factory->number_of_floors) as $floor) {
                            $filterd = $grouped_per_date->filterByFloor($floor);
                            $cultivation_state["moving_bed_count_floor_{$floor}_sum"] = $filterd->count();

                            foreach (range(1, 10) as $idx) {
                                $cultivation_state["moving_bed_count_floor_{$floor}_pattern_{$idx}"] = null;
                            }

                            foreach ($factory_cycle_pattern_items->values() as $idx => $fcpi) {
                                $idx = $idx + 1;
                                $cultivation_state["moving_bed_count_floor_{$floor}_pattern_{$idx}"] =
                                    $filterd->filterByPattern($fcpi->pattern)->count();
                            }
                        }

                        $cultivation_states[] = $cultivation_state;
                    }
                }

                $this->cultivation_state_repo->createCultivationStates($cultivation_states);

                $arrangement_states = [];
                foreach ($panel_states->filterFrontestPanels()->groupByDate() as $date => $grouped) {
                    foreach ($grouped->groupByColumn() as $column => $grouped_per_column) {
                        $arrangement_state = [
                            'factory_code' => $bed_state->factory_code,
                            'factory_species_code' => $bed_state->factory_species_code,
                            'start_of_week' => $bed_state->start_of_week,
                            'working_date' => $date,
                            'bed_column' => $column,
                            'bed_row_number' => $factory->number_of_rows,
                            'created_by' => $user_code,
                            'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                            'updated_by' => $user_code,
                            'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
                        ];

                        foreach (range(1, $factory->number_of_rows) as $row) {
                            $arrangement_state['growing_stages_count_'.$row] = null;
                            $arrangement_state['pattern_row_count_'.$row] = null;

                            $ps = $grouped_per_column->where('bed_row', $row)->first();
                            if (! is_null($ps)) {
                                $arrangement_state["growing_stages_count_{$row}"] =
                                    $bed_state->factory_species_code === $ps->factory_species_code ?
                                        $ps->growing_stage_sequence_number :
                                        BedState::DUMMY_SEQ_NUM_OF_OTHER_SPECIES;

                                $arrangement_state['pattern_row_count_'.$row] =
                                    $bed_state->factory_species_code === $ps->factory_species_code ?
                                        $ps->cycle_pattern :
                                        null;
                            }
                        }

                        $arrangement_states[] = $arrangement_state;
                    }
                }

                $this->arrangement_state_repo->createArrangementStates($arrangement_states);

                $base_panel_statuses = array_fill_keys(array_map(function ($idx) {
                    return 'panel_status_'.$idx;
                }, range(1, ArrangementDetailState::NUMBER_OF_PANEL_STATE_COLUMN)), null);

                $arrangement_detail_states = [];
                foreach ($panel_states->groupByDate() as $date => $grouped) {
                    foreach ($grouped->groupByColumn() as $column => $grouped_per_column) {
                        foreach ($grouped_per_column->groupByRow() as $row => $grouped_per_row) {
                            $arrangement_detail_state = [
                                'factory_code' => $bed_state->factory_code,
                                'factory_species_code' => $bed_state->factory_species_code,
                                'start_of_week' => $bed_state->start_of_week,
                                'working_date' => $date,
                                'bed_row' => $row,
                                'bed_column' => $column,
                                'created_by' => $user_code,
                                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                                'updated_by' => $user_code,
                                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
                            ];

                            foreach ($grouped_per_row as $ps) {
                                $panel_idx = $ps->x_coordinate_panel *
                                    ($ps->y_current_bed_position - 1) +
                                    $ps->x_current_bed_position;

                                $arrangement_detail_state["panel_status_{$panel_idx}"] =
                                    $bed_state->factory_species_code === $ps->factory_species_code ?
                                        $ps->growing_stage_sequence_number :
                                        BedState::DUMMY_SEQ_NUM_OF_OTHER_SPECIES;
                            }

                            $arrangement_detail_states[] = array_merge($base_panel_statuses, $arrangement_detail_state);
                        }
                    }
                }

                $this->arrangement_detail_state_repo->createArrangementDetailStates($arrangement_detail_states);

                $bed_state->completed_preparation_at = Chronos::now()->format('Y-m-d H:i:s');
                $bed_state->save();
            });
        } catch (Exception $e) {
            report($e);
            $bed_state->delete();
        }
    }

    /**
     * ベッド状況の削除
     *
     * @param  \App\Models\Plan\BedState $bed_state
     * @return void
     */
    public function deleteBedState(BedState $bed_state): void
    {
        $this->db->transaction(function () use ($bed_state) {
            $this->seeding_plan_repo->deleteSeedingPlans($bed_state);
            $this->cultivation_state_repo->deleteCultivationStates($bed_state);
            $this->arrangement_state_repo->deleteArrangementStates($bed_state);
            $this->arrangement_detail_state_repo->deleteArrangementDetailStates($bed_state);
            $bed_state->delete();
        });
    }

    /**
     * 各階栽培株数を帳票出力する
     *
     * @param \App\Models\Plan\BedState $bed_state
     */
    public function exportCultivationStates(BedState $bed_state)
    {
        $working_dates = $bed_state->getStartOfWeek()
            ->getWorkingDatesExceptFactoryRest(
                $bed_state->getStartOfWeek()->endOfWeek(),
                $bed_state->factory_species->factory
            );

        $config = config('settings.data_link.plan.planned_cultivation_status_work');
        $file_name = generate_file_name($config['file_name'], [
            $bed_state->factory_species->factory->factory_abbreviation,
            $bed_state->factory_species->factory_species_name,
            implode('～', [
                head($working_dates)->format('Ymd'),
                last($working_dates)->format('Ymd')
            ])
        ]);

        $this->excel->create($file_name, function ($excel) use ($bed_state, $working_dates, $config) {
            // 各階栽培株数一覧表
            $excel->sheet($config['floor_cultivation_stock']['sheet_title'], function ($sheet) use (
                $bed_state,
                $working_dates,
                $config
            ) {
                $factory_growing_stages = $bed_state
                    ->factory_species
                    ->factory_growing_stages
                    ->exceptSeeding()
                    ->reverse();

                $base_patterns = $factory_growing_stages
                    ->sortByDesc(function ($fgs) {
                        return $fgs->factory_cycle_pattern->factory_cycle_pattern_items->count();
                    })
                    ->first()
                    ->factory_cycle_pattern
                    ->factory_cycle_pattern_items
                    ->groupByPattern();

                $sheet->loadView('plan.bed_states.template_floor_cultivation_stock')
                    ->with(compact(
                        'bed_state',
                        'factory_growing_stages',
                        'base_patterns',
                        'working_dates',
                        'config'
                    ));

                $hole_column = 1;
                foreach ($factory_growing_stages as $fgs) {
                    $row = $config['floor_cultivation_stock']['base_row'];
                    $number_of_patterns = $base_patterns->count();

                    $sheet->mergeCells(
                        get_excel_column_str($hole_column).$row.':'.
                        get_excel_column_str($hole_column).($row + $number_of_patterns)
                    );

                    $row = $row + $number_of_patterns + 2;
                    foreach (range(1, $bed_state->factory_species->factory->number_of_floors) as $floor) {
                        $sheet->mergeCells(
                            get_excel_column_str($hole_column).$row.':'.
                            get_excel_column_str($hole_column).($row + $number_of_patterns)
                        );

                        $row = $row + $number_of_patterns + 1;
                    }

                    $hole_column = $hole_column + 3 + count($working_dates);
                }
            });

            // 各階栽培株数合計表
            $excel->sheet($config['floor_cultivation_stock_sum']['sheet_title'], function ($sheet) use (
                $bed_state,
                $working_dates,
                $config
            ) {
                $factory_growing_stages = $bed_state
                    ->factory_species
                    ->factory_growing_stages
                    ->exceptSeeding()
                    ->reverse();
                $sheet->loadView('plan.bed_states.template_stage_in_out_balance')
                    ->with(compact(
                        'bed_state',
                        'factory_growing_stages',
                        'base_patterns',
                        'working_dates',
                        'config'
                    ));
            });

            // 栽培シュミレーション
            $excel->sheet($config['cultivation_simulation']['sheet_title'], function ($sheet) use (
                $bed_state,
                $working_dates,
                $config
            ) {
                    $factory_growing_stages = $bed_state
                        ->factory_species
                        ->factory_growing_stages
                        ->exceptSeeding()
                        ->reverse();

                    $seeding_stage = $bed_state->factory_species->factory_growing_stages->first();
                    $seeding_plans = array_map(function ($wd, $idx) use ($bed_state, $seeding_stage) {
                        $seeding_plan = $bed_state->seeding_plans[$idx] ?? null;
                        $seeding_date = $seeding_stage->getGrowingStage()->getStageChangedDate(
                            $bed_state->factory_species->factory,
                            $wd->format('Y-m-d'),
                            $seeding_stage->growing_term
                        );

                        return (object)[
                            'tray' => $seeding_plan->number_of_trays ?? 0,
                            'seed' => ($seeding_plan->number_of_trays ?? 0) * $seeding_stage->number_of_holes,
                            'working_date' => $wd,
                            'seeding_date' => WorkingDate::parse($seeding_date)
                        ];
                    }, $working_dates, array_keys($working_dates));

                    $sheet->loadView('plan.bed_states.template_floor_in_out_balance')
                        ->with(compact(
                            'bed_state',
                            'factory_growing_stages',
                            'working_dates',
                            'seeding_stage',
                            'seeding_plans',
                            'config'
                        ));
            });
        })
            ->download();
    }

    /**
     * 栽培パネル配置図の帳票出力
     *
     * @param  \App\Models\Plan\BedState $bed_state
     * @param  \App\ValueObjects\Date\WorkingDate $working_date
     * @param  \App\Models\Master\Collections\FactoryGrowingStageCollection $factory_growing_stages
     * @param  array $factory_layout
     * @param  string $label_of_bed
     */
    public function exportArrangementStates(
        BedState $bed_state,
        WorkingDate $working_date,
        FactoryGrowingStageCollection $factory_growing_stages,
        array $factory_layout,
        string $label_of_bed
    ) {
        $sheet_name = trans('view.plan.planned_arrangement_status_work.index');
        $file_name = generate_file_name($sheet_name, [
            $bed_state->factory_species->factory->factory_abbreviation,
            $bed_state->factory_species->species->species_name,
            $working_date->format('Ymd')
        ]);

        return $this->excel
            ->create($file_name, function ($excel) use (
                $bed_state,
                $working_date,
                $factory_growing_stages,
                $factory_layout,
                $label_of_bed,
                $sheet_name
            ) {
                $excel->sheet($sheet_name, function ($sheet) use (
                    $bed_state,
                    $working_date,
                    $factory_growing_stages,
                    $factory_layout,
                    $label_of_bed
                ) {
                    $sheet->setFontFamily(self::EXCEL_FONT_FAMILY);
                    $sheet->getSheetView()->setZoomScale(self::EXCEL_ZOOM_SCALE);

                    $sheet->loadView('plan.bed_states.export_arrangement_states')
                        ->with(compact(
                            'bed_state',
                            'working_date',
                            'factory_growing_stages',
                            'factory_layout',
                            'label_of_bed'
                        ));
                });
            })
            ->download();
    }

    /**
     * 栽培パネル配置図詳細の帳票出力
     *
     * @param  \App\Models\Plan\BedState $bed_state
     * @param  \App\ValueObjects\Date\WorkingDate $working_date
     * @param  \App\Models\Master\Collections\FactoryGrowingStageCollection $factory_growing_stages
     * @param  array $factory_layout
     */
    public function exportArrangementDetailStates(
        BedState $bed_state,
        WorkingDate $working_date,
        FactoryGrowingStageCollection $factory_growing_stages,
        array $factory_layout
    ) {
        $sheet_name = trans('view.plan.planned_arrangement_status_work.detail');
        $file_name = generate_file_name($sheet_name, [
            $bed_state->factory_species->factory->factory_abbreviation,
            $bed_state->factory_species->species->species_name,
            $working_date->format('Ymd')
        ]);

        return $this->excel
            ->create($file_name, function ($excel) use (
                $bed_state,
                $working_date,
                $factory_growing_stages,
                $factory_layout,
                $sheet_name
            ) {
                $excel->sheet($sheet_name, function ($sheet) use (
                    $bed_state,
                    $working_date,
                    $factory_growing_stages,
                    $factory_layout
                ) {
                    $sheet->setFontFamily(self::EXCEL_FONT_FAMILY);
                    $sheet->getSheetView()->setZoomScale(self::EXCEL_ZOOM_SCALE);

                    $sheet->loadView('plan.bed_states.export_arrangement_detail_states')
                        ->with(compact(
                            'bed_state',
                            'working_date',
                            'factory_growing_stages',
                            'factory_layout'
                        ));
                });
            })
            ->download();
    }
}

<?php

declare(strict_types=1);

namespace App\Services\Plan;

use Exception;
use Illuminate\Auth\AuthManager;
use Illuminate\Database\Connection;
use Illuminate\Pagination\LengthAwarePaginator;
use Cake\Chronos\Chronos;
use App\Exceptions\OptimisticLockException;
use App\Exceptions\PageOverException;
use App\Extension\Logger\ApplicationLogger;
use App\Models\Plan\GrowthSimulation;
use App\Models\Plan\GrowthSimulationItem;
use App\Models\Plan\PlannedArrangementDetailStatusWork;
use App\Models\Plan\Collections\GrowthSimulationItemCollection;
use App\Repositories\Plan\GrowthSimulationRepository;
use App\Repositories\Plan\GrowthSimulationItemRepository;
use App\Repositories\Plan\PanelStateRepository;
use App\Repositories\Plan\PlannedArrangementDetailStatusWorkRepository;
use App\Repositories\Plan\PlannedArrangementStatusWorkRepository;
use App\Repositories\Plan\PlannedCultivationStatusWorkRepository;
use App\ValueObjects\Date\SimulationDate;
use App\ValueObjects\Enum\DisplayKubun;
use App\ValueObjects\Enum\GrowingStage;
use App\ValueObjects\Enum\PanelStatus;

class GrowthSimulationService
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
     * @var \App\Extension\Logger\ApplicationLogger
     */
    private $application_logger;

    /**
     * @var \App\Repositories\Plan\GrowthSimulationRepository
     */
    private $growth_simulation_repo;

    /**
     * @var \App\Repositories\Plan\GrowthSimulationItemRepository
     */
    private $growth_simulation_item_repo;

    /**
     * @var \App\Repositories\Plan\PlannedCultivationStatusWorkRepository
     */
    private $planned_cultivation_status_work_repo;

    /**
     * @var \App\Repositories\Plan\PlannedArrangementStatusWorkRepository
     */
    private $planned_arrangement_status_work_repo;

    /**
     * @var \App\Repositories\Plan\PlannedArrangementDetailStatusWorkRepository
     */
    private $planned_arrangement_detail_status_work_repo;

    /**
     * @var \App\Repositories\Plan\PanelStateRepository
     */
    private $panel_state_repo;

    /**
     * @param  \Illuminate\Database\Connection $db
     * @param  \Illuminate\Auth\AuthManager $auth
     * @param  \App\Extension\Logger\ApplicationLogger $application_logger
     * @param  \App\Repositories\Plan\GrowthSimulationRepository $growth_simulation_repo
     * @param  \App\Repositories\Plan\GrowthSimulationItemRepository $growth_simulation_item_repo
     * @param  \App\Repositories\Plan\PlannedCultivationStatusWorkRepository $planned_cultivation_status_work_repo
     * @param  \App\Repositories\Plan\PlannedArrangementStatusWorkRepository $planned_arrangement_status_work_repo
     * @param  \App\Repositories\Plan\PlannedArrangementDetailStatusWorkRepository
     *          $planned_arrangement_detail_status_work_repo
     * @param  \App\Repositories\Plan\PanelStateRepository $panel_state_repo
     * @return void
     */
    public function __construct(
        Connection $db,
        AuthManager $auth,
        ApplicationLogger $application_logger,
        GrowthSimulationRepository $growth_simulation_repo,
        GrowthSimulationItemRepository $growth_simulation_item_repo,
        PlannedCultivationStatusWorkRepository $planned_cultivation_status_work_repo,
        PlannedArrangementStatusWorkRepository $planned_arrangement_status_work_repo,
        PlannedArrangementDetailStatusWorkRepository $planned_arrangement_detail_status_work_repo,
        PanelStateRepository $panel_state_repo
    ) {
        $this->db = $db;
        $this->auth = $auth;
        $this->application_logger = $application_logger;

        $this->growth_simulation_repo = $growth_simulation_repo;
        $this->growth_simulation_item_repo = $growth_simulation_item_repo;
        $this->planned_cultivation_status_work_repo = $planned_cultivation_status_work_repo;
        $this->planned_arrangement_status_work_repo = $planned_arrangement_status_work_repo;
        $this->planned_arrangement_detail_status_work_repo = $planned_arrangement_detail_status_work_repo;
        $this->panel_state_repo = $panel_state_repo;
    }

    /**
     * 生産シミュレーションを条件に応じて検索
     *
     * @param  array $params
     * @param  int $page
     * @param  array $order
     * @return \Illuminate\Pagination\LengthAwarePaginator
     * @throws \App\Exceptions\PageOverException
     */
    public function searchGrowthSimulations(array $params, int $page, array $order): LengthAwarePaginator
    {
        $growth_simulations = $this->growth_simulation_repo->search($params, $order);
        if ($page > $growth_simulations->lastPage() && $growth_simulations->lastPage() !== 0) {
            throw new PageOverException('target page does not exist.');
        }

        return $growth_simulations;
    }

    /**
     * 生産シミュレーション確定を条件に応じて検索
     *
     * @param  array $params
     * @param  int $page
     * @param  array $order
     * @return \Illuminate\Pagination\LengthAwarePaginator
     * @throws \App\Exceptions\PageOverException
     */
    public function searchFixedGrowthSimulations(array $params, int $page, array $order): LengthAwarePaginator
    {
        $growth_simulations = $this->growth_simulation_repo->searchFixed($params, $order);
        if ($page > $growth_simulations->lastPage() && $growth_simulations->lastPage() !== 0) {
            throw new PageOverException('target page does not exist.');
        }

        return $growth_simulations;
    }

    /**
     * 生産シミュレーションのロック更新
     *
     * @param  \App\Models\Plan\GrowthSimulation $growth_simulation
     * @return void
     * @throws \App\Exceptions\OptimisticLockException
     */
    public function lockGrowthSimulation(GrowthSimulation $growth_simulation): void
    {
        if (! $growth_simulation->canSimulate()) {
            throw new OptimisticLockException('disabled to lock the simulation.');
        }

        $growth_simulation->work_by = $this->auth->user()->user_code;
        $growth_simulation->work_at = Chronos::now()->format('Y-m-d H:i:s');
        $growth_simulation->save();
    }

    /**
     * 生産シミュレーションのロック解除
     *
     * @param  \App\Models\Plan\GrowthSimulation $growth_simulation
     * @return void
     * @throws \App\Exceptions\OptimisticLockException
     */
    public function unlockGrowthSimulation(GrowthSimulation $growth_simulation): void
    {
        if (! $growth_simulation->canSimulate()) {
            throw new OptimisticLockException('disabled to unlock the simulation.');
        }

        $growth_simulation->work_by = null;
        $growth_simulation->work_at = null;
        $growth_simulation->save();
    }

    /**
     * シミュレーション名変更処理
     *
     * @param  \App\Models\Plan\GrowthSimulation $growth_simulation
     * @param  string $simulation_name
     * @return void
     */
    public function changeSimulationName(GrowthSimulation $growth_simulation, string $simulation_name): void
    {
        if ($growth_simulation->canNotBeEdited()) {
            throw new OptimisticLockException('disabled to chenge name of the simulation.');
        }

        $growth_simulation->simulation_name = $simulation_name;
        $growth_simulation->save();
    }

    /**
     * 生産シミュレーションの登録
     *
     * @param  array $params
     * @return void
     */
    public function createGrowthSimulation(array $params): void
    {
        $growth_simulation = $this->db->transaction(function () use ($params) {
            return $this->growth_simulation_repo->create([
                'factory_code' => $params['factory_code'],
                'factory_species_code' => $params['factory_species_code'],
                'simulation_name' => $params['simulation_name'],
                'detail_number' => $params['detail_number'],
                'simulation_preparation_start_at' => Chronos::now()->format('Y-m-d H:i:s')
            ]);
        });

        try {
            ini_set('max_execution_time', '6000');

            $this->db->transaction(function () use ($params, $growth_simulation) {
                $this->createSimulationData($params, $growth_simulation);

                $growth_simulation->simulation_preparation_comp_at = Chronos::now()->format('Y-m-d H:i:s');
                $growth_simulation->save();
            });
        } catch (Exception $e) {
            report($e);
            $growth_simulation->delete();
        }
    }

    /**
     * 生産シミュレーションの修正
     *
     * @param  \App\Models\Plan\GrowthSimulation $growth_simulation
     * @param  array $params
     * @return void
     */
    public function updateGrowthSimulation(GrowthSimulation $growth_simulation, array $params): void
    {
        $prev_simulation_preparation_start_at = $growth_simulation->simulation_preparation_start_at;
        $prev_simulation_preparation_comp_at = $growth_simulation->simulation_preparation_comp_at;

        $growth_simulation->simulation_preparation_start_at = Chronos::now()->format('Y-m-d H:i:s');
        $growth_simulation->simulation_preparation_comp_at = null;
        $growth_simulation->save();

        try {
            ini_set('max_execution_time', '6000');

            $this->db->transaction(function () use ($params, $growth_simulation) {
                $this->createSimulationData($params, $growth_simulation);

                $growth_simulation->simulation_preparation_comp_at = Chronos::now()->format('Y-m-d H:i:s');
                $growth_simulation->save();
            });
        } catch (Exception $e) {
            report($e);

            $growth_simulation->simulation_preparation_start_at = $prev_simulation_preparation_start_at;
            $growth_simulation->simulation_preparation_comp_at = $prev_simulation_preparation_comp_at;
            $growth_simulation->save();
        }
    }

    /**
     * 生産シミュレーション用のデータを生成
     *
     * @param  array $params
     * @param  \App\Models\Plan\GrowthSimulation $simulation_item_params
     * @return void
     */
    private function createSimulationData(array $params, GrowthSimulation $growth_simulation): void
    {
        $log = [
            'factory_code' => $growth_simulation->factory_code,
            'simulation_id' => $growth_simulation->simulation_id
        ];

        $this->logInfo('create growth simulation：start.', $log);

        $user_code = $this->auth->user()->user_code ?? '';

        $growth_simulation_items = new GrowthSimulationItemCollection();
        foreach ($params['growth_simulation_items_list'] as $original_growth_simulation_items) {
            foreach ($original_growth_simulation_items as $gsi) {
                $gsi = array_merge($gsi, [
                    'simulation_id' => $growth_simulation->simulation_id,
                    'created_by' => $user_code,
                    'created_at' => Chronos::now(),
                    'updated_by' => $user_code,
                    'updated_at' => Chronos::now()
                ]);

                $growth_simulation_items->push(array_except($gsi, 'growing_stage_name'));
            }
        }

        $this->growth_simulation_item_repo->deleteGrowthSimulationItems($growth_simulation);
        $this->growth_simulation_item_repo->insertGrowthSimulationItems($growth_simulation_items->all());

        $this->logInfo('create growth simulation：growth simulation items were registered.', $log);

        $factory = $growth_simulation->factory_species->factory;
        $factory_growing_stages = $growth_simulation->factory_species->factory_growing_stages;

        $growth_simulation_items = $growth_simulation_items->mapInto(GrowthSimulationItem::class);
        $first_porting_date = $growth_simulation_items->getFirstPortingDate();
        $last_harvesting_date = $growth_simulation_items->getLastHarvestingDate();

        $planned_cultivation_status_works = [];
        foreach ($growth_simulation_items as $gsi) {
            if ($gsi->growing_stage === GrowingStage::SEEDING || $gsi->growing_stage === GrowingStage::HARVESTING) {
                continue;
            }

            $date = SimulationDate::parse($gsi->date);
            while ($date->lte($last_harvesting_date)) {
                if (! $date->isWorkingDay($factory)) {
                    $date = $date->addDay();
                    continue;
                }

                $growing_stages_sequence_number = $gsi->growing_stages_sequence_number;

                $already_exists =
                    isset($planned_cultivation_status_works[$growing_stages_sequence_number][$date->format('Ymd')]);
                if ($already_exists) {
                    $planned_cultivation_status_works
                        [$growing_stages_sequence_number][$date->format('Ymd')]['bed_number'] += $gsi->bed_number;
                }
                if (! $already_exists) {
                    $planned_cultivation_status_works[$growing_stages_sequence_number][$date->format('Ymd')] = [
                        'factory_code' => $growth_simulation->factory_code,
                        'simulation_id' => $growth_simulation->simulation_id,
                        'factory_species_code' => $growth_simulation->factory_species_code,
                        'display_kubun' => DisplayKubun::PROCESS,
                        'growing_stages_sequence_number' => $growing_stages_sequence_number,
                        'date' => $date->format('Y-m-d'),
                        'day_of_the_week' => $date->format('w'),
                        'number_of_holes' => $factory_growing_stages
                            ->findBySequenceNumber($growing_stages_sequence_number)
                            ->number_of_holes,
                        'bed_number' => $gsi->bed_number,
                        'patterns_number' => $factory->factory_cycle_patterns->count(),
                        'floor_number' => $factory->number_of_floors,
                        'created_by' => $user_code,
                        'created_at' => Chronos::now(),
                        'updated_by' => $user_code,
                        'updated_at' => Chronos::now()
                    ];
                }

                $date = $date->addDay();
            }
        }

        $this->planned_cultivation_status_work_repo->deletePlannedCultivationStatusWorks($growth_simulation);
        $this->planned_cultivation_status_work_repo
            ->createPlannedCultivationStatusWorks($planned_cultivation_status_works);

        $this->logInfo('create growth simulation：planned cultivation status works were regstered.', $log);

        $panel_states = $this->panel_state_repo
            ->getSimulatingPanelStates($growth_simulation->factory_code, [$first_porting_date, $last_harvesting_date]);

        $fixed_planned_arrangement_status_works = [];
        foreach ($panel_states->filterFrontestPanels()->groupByDate() as $date => $grouped) {
            foreach ($grouped->groupByColumn() as $column => $grouped_per_column) {
                $planned_arrangement_status_work = [
                    'factory_code' => $growth_simulation->factory_code,
                    'simulation_id' => $growth_simulation->simulation_id,
                    'factory_species_code' => $growth_simulation->factory_species_code,
                    'display_kubun' => DisplayKubun::FIXED,
                    'date' => $date,
                    'bed_column' => $column,
                    'bed_row_number' => $factory->number_of_rows,
                    'created_by' => $user_code,
                    'created_at' => Chronos::now(),
                    'updated_by' => $user_code,
                    'updated_at' => Chronos::now()
                ];

                foreach (range(1, $factory->number_of_rows) as $row) {
                    $planned_arrangement_status_work['growing_stages_count_'.$row] = null;
                    $planned_arrangement_status_work['pattern_row_count_'.$row] = null;

                    $ps = $grouped_per_column->where('bed_row', $row)->first();
                    if (! is_null($ps)) {
                        $planned_arrangement_status_work['growing_stages_count_'.$row] =
                            $growth_simulation->factory_species_code === $ps->factory_species_code ?
                                $ps->growing_stage_sequence_number :
                                GrowthSimulation::DUMMY_SEQ_NUM_OF_OTHER_SPECIES;

                        $planned_arrangement_status_work['pattern_row_count_'.$row] =
                            $growth_simulation->factory_species_code === $ps->factory_species_code ?
                                $ps->cycle_pattern :
                                null;
                    }
                }

                $fixed_planned_arrangement_status_works[] = $planned_arrangement_status_work;
            }
        }

        $this->planned_arrangement_status_work_repo->deletePlannedArrangementStatusWorks($growth_simulation);
        $this->logInfo('create growth simulation：planned arrangement status works were deleted.', $log);

        $this->planned_arrangement_status_work_repo
            ->createPlannedArrangementStatusWorks($fixed_planned_arrangement_status_works);
        $this->logInfo('create growth simulation：fixed planned arrangement status works were registred.', $log);

        $base_panel_statuses = array_fill_keys(array_map(function ($idx) {
            return 'panel_status_'.$idx;
        }, range(1, PlannedArrangementDetailStatusWork::NUMBER_OF_PANEL_STATE_COLUMN)), null);

        $fixed_planned_arrangement_detail_status_works = [];
        foreach ($panel_states->groupByDate() as $date => $grouped) {
            foreach ($grouped->groupByColumn() as $column => $grouped_per_column) {
                foreach ($grouped_per_column->groupByRow() as $row => $grouped_per_row) {
                    $fixed_planned_arrangement_detail_status_work = [
                        'factory_code' => $growth_simulation->factory_code,
                        'simulation_id' => $growth_simulation->simulation_id,
                        'factory_species_code' => $growth_simulation->factory_species_code,
                        'display_kubun' => DisplayKubun::FIXED,
                        'date' => $date,
                        'bed_row' => $row,
                        'bed_column' => $column,
                        'created_by' => $user_code,
                        'created_at' => Chronos::now(),
                        'updated_by' => $user_code,
                        'updated_at' => Chronos::now()
                    ];

                    foreach ($grouped_per_row as $ps) {
                        $panel_idx = $ps->x_coordinate_panel *
                            ($ps->y_current_bed_position - 1) +
                            $ps->x_current_bed_position;

                        $fixed_planned_arrangement_detail_status_work["panel_status_{$panel_idx}"] =
                            $growth_simulation->factory_species_code === $ps->factory_species_code ?
                                $ps->growing_stage_sequence_number :
                                GrowthSimulation::DUMMY_SEQ_NUM_OF_OTHER_SPECIES;
                    }

                    $fixed_planned_arrangement_detail_status_works[] =
                        array_merge($base_panel_statuses, $fixed_planned_arrangement_detail_status_work);
                }
            }
        }

        $this->planned_arrangement_detail_status_work_repo
            ->deletePlannedArrangementDetailStatusWorks($growth_simulation);
        $this->logInfo('create growth simulation：planned arrangement detail status works were deleted.', $log);

        $this->planned_arrangement_detail_status_work_repo
            ->createPlannedArrangementDetailStatusWorks($fixed_planned_arrangement_detail_status_works);
        $this->logInfo('create growth simulation：fixed planned arrangement detail status works were registered.', $log);

        $planned_arrangement_status_works = $planned_arrangement_detail_status_works = [];

        $date = $first_porting_date->subDay();
        while ($date->lte($last_harvesting_date)) {
            foreach ($factory->factory_columns as $fc) {
                $planned_arrangement_status_works[] = [
                    'factory_code' => $growth_simulation->factory_code,
                    'simulation_id' => $growth_simulation->simulation_id,
                    'factory_species_code' => $growth_simulation->factory_species_code,
                    'display_kubun' => DisplayKubun::PROCESS,
                    'date' => $date,
                    'bed_column' => $fc->column,
                    'bed_row_number' => $factory->number_of_rows,
                    'created_by' => $user_code,
                    'created_at' => Chronos::now(),
                    'updated_by' => $user_code,
                    'updated_at' => Chronos::now()
                ];
            }

            foreach ($factory->factory_beds as $fb) {
                $planned_arrangement_detail_status_works[] = [
                    'factory_code' => $growth_simulation->factory_code,
                    'simulation_id' => $growth_simulation->simulation_id,
                    'factory_species_code' => $growth_simulation->factory_species_code,
                    'display_kubun' => DisplayKubun::PROCESS,
                    'date' => $date,
                    'bed_row' => $fb->row,
                    'bed_column' => $fb->column,
                    'created_by' => $user_code,
                    'created_at' => Chronos::now(),
                    'updated_by' => $user_code,
                    'updated_at' => Chronos::now()
                ];
            }

            $date = $date->addDay();
        }

        $this->planned_arrangement_status_work_repo
            ->createPlannedArrangementStatusWorks($planned_arrangement_status_works);
        $this->logInfo('create growth simulation：planned arrangement status works were registred.', $log);

        $this->planned_arrangement_detail_status_work_repo
            ->createPlannedArrangementDetailStatusWorks($planned_arrangement_detail_status_works);
        $this->logInfo('create growth simulation：planned arrangement detail status works were registered.', $log);

        $this->logInfo('create growth simulation：end.', $log);
    }

    /**
     * 生産シミュレーションの削除
     *
     * @param  \App\Models\Plan\GrowthSimulation $growth_simulation
     * @return void
     */
    public function deleteGrowthSimulation(GrowthSimulation $growth_simulation): void
    {
        $this->planned_arrangement_detail_status_work_repo
            ->deletePlannedArrangementDetailStatusWorks($growth_simulation);
        $this->planned_arrangement_status_work_repo->deletePlannedArrangementStatusWorks($growth_simulation);
        $this->planned_cultivation_status_work_repo->deletePlannedCultivationStatusWorks($growth_simulation);
        $this->growth_simulation_item_repo->deleteGrowthSimulationItems($growth_simulation);

        $growth_simulation->delete();
    }

    /**
     * 残ベッドチェック
     *
     * @param  \App\Models\Master\GrowthSimulation $growth_simulation
     * @return array
     */
    public function checkBedNumber(GrowthSimulation $growth_simulation): array
    {
        $result = ['result' => true, 'date' => null];

        $planned_cultivation_status_works =
            $this->planned_cultivation_status_work_repo->getSumOfMovingBedsPerFloor($growth_simulation);
        $planned_arrangement_status_works =
            $this->planned_arrangement_status_work_repo->getPlannedArrangementStatusWorksWithFixed($growth_simulation);

        foreach ($planned_cultivation_status_works as $pcsw) {
            $count_registered_count_sum = 0;
            foreach ($planned_arrangement_status_works->where('date', $pcsw->date) as $pasw) {
                $count_registered_count_sum +=
                    $pasw->getGrowingStagesCountRegisteredCount($pcsw->growing_stages_sequence_number);
            }

            if ($pcsw->getSumOfMovingBeds() !== $count_registered_count_sum) {
                $result['result'] = false;
                $result['date'] = SimulationDate::parse($pcsw->date)->format('Y/m/d');
                break;
            }
        }

        return $result;
    }

    /**
     * 生産シミュレーション確定処理
     *
     * @param  \App\Models\Master\GrowthSimulation $growth_simulation
     * @return void
     */
    public function fixSimulation(GrowthSimulation $growth_simulation): void
    {
        ini_set('max_execution_time', '300');

        try {
            $this->logInfo('fix simulation：start.', [
                'factory_code' => $growth_simulation->factory_code,
                'simulation_id' => $growth_simulation->simulation_id
            ]);

            $growth_simulation->fixed_by = $this->auth->user()->user_code;
            $growth_simulation->fixed_start_at = Chronos::now()->format('Y-m-d H:i:s');
            $growth_simulation->save();

            $recent_panel_moving_dates = $this->db->transaction(function () use ($growth_simulation) {
                $recent_panel_moving_dates = $this->replacePanels($growth_simulation);

                $this->planned_cultivation_status_work_repo->fixPlannedCultivationStatusWorks($growth_simulation);
                $this->planned_arrangement_status_work_repo->fixPlannedArrangementStatusWorks($growth_simulation);
                $this->planned_arrangement_detail_status_work_repo
                    ->fixPlannedArrangementDetailStatusWorks($growth_simulation);

                $growth_simulation->fixed_comp_at = Chronos::now()->format('Y-m-d H:i:s');
                $growth_simulation->save();

                return $recent_panel_moving_dates;
            });
        } catch (Exception $e) {
            $growth_simulation->fixed_by = null;
            $growth_simulation->fixed_start_at = null;
            $growth_simulation->save();

            $this->logInfo('fix simulation：roll back.', [
                'factory_code' => $growth_simulation->factory_code,
                'simulation_id' => $growth_simulation->simulation_id
            ]);

            report($e);
            return;
        }

        $this->panel_state_repo
            ->replicateFixedPanelStates($growth_simulation->factory_code, $recent_panel_moving_dates);

        $this->logInfo('fix simulation：finish.', [
            'factory_code' => $growth_simulation->factory_code,
            'simulation_id' => $growth_simulation->simulation_id
        ]);
    }

    /**
     * パネル状況登録処理
     *
     * @param  \App\Models\Master\GrowthSimulation $growth_simulation
     * @return array $recent_panel_moving_dates
     */
    private function replacePanels(GrowthSimulation $growth_simulation): array
    {
        $current_panel_id = $this->panel_state_repo->getMaxPanelId($growth_simulation->factory_code);
        $panel_id = $current_panel_id + 1;

        $planned_cultivation_status_works = $growth_simulation->planned_cultivation_status_works;
        $planned_arrangement_status_works =
            $this->planned_arrangement_status_work_repo->getPlannedArrangementStatusWorksWithFixed($growth_simulation);

        $this->logInfo('panel status registration：start.', [
            'factory_code' => $growth_simulation->factory_code,
            'start_sequence' => $current_panel_id,
            'start_date' => $planned_arrangement_status_works->min('date'),
            'end_date' => $planned_arrangement_status_works->max('date')
        ]);

        $factory = $growth_simulation->factory_species->factory;
        $factory_beds = $factory->factory_beds;
        $factory_growing_stages = $growth_simulation->factory_species->factory_growing_stages;
        $factory_cycle_pattern_items = $factory_growing_stages->toStageAndCyclePatternsMap();

        $recent_panel_moving_dates = [];
        foreach ($planned_arrangement_status_works as $pasw) {
            if (! array_key_exists($pasw->bed_column, $recent_panel_moving_dates)) {
                $recent_panel_moving_dates[$pasw->bed_column] = [];
            }

            $panel_states_of_previous_day = $this->panel_state_repo->getPanelStatesOfPreviousDay($pasw);

            $panel_states_list = [];
            foreach (range(1, $pasw->bed_row_number) as $row) {
                if (! array_key_exists($row, $recent_panel_moving_dates[$pasw->bed_column])) {
                    $recent_panel_moving_dates[$pasw->bed_column][$row] = '';
                }

                // ステージが未割当? or 他品種?
                $sequence_number = $pasw["growing_stages_count_{$row}"];
                if (is_null($sequence_number) ||
                    $sequence_number === GrowthSimulation::DUMMY_SEQ_NUM_OF_OTHER_SPECIES) {
                    continue;
                }

                $panel_states_list[$row] = [];
                $recent_panel_moving_dates[$pasw->bed_column][$row] = $pasw->date;

                $number_of_moving_panels = 0;
                if (SimulationDate::parse($pasw->date)->isWorkingDay($factory)) {
                    $factory_bed = $factory_beds->findByPosition($row, $pasw->bed_column);
                    $panel_state_base = [
                        'factory_code' => $pasw->factory_code,
                        'date' => $pasw->date,
                        'number_of_holes' => 0,
                        'bed_row' => $row,
                        'bed_column' => $pasw->bed_column,
                        'x_coordinate_panel' => $factory_bed->x_coordinate_panel,
                        'y_coordinate_panel' => $factory_bed->y_coordinate_panel,
                        'factory_species_code' => null,
                        'stage_start_date' => null,
                        'growing_stage_sequence_number' => null,
                        'cycle_pattern' => null,
                        'current_growth_stage' => null,
                        'next_growing_stage_sequence_number' => null,
                        'next_growth_stage' => null,
                        'next_growth_stage_date' => null,
                        'using_hole_count' => 0,
                        'panel_status' => PanelStatus::EMPTY,
                        'created_by' => $this->auth->user()->user_code,
                        'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                        'updated_by' => $this->auth->user()->user_code,
                        'updated_at' => Chronos::now()->format('Y-m-d H:i:s'),
                    ];

                    // 空ベッドではない?
                    if ($sequence_number !== GrowthSimulation::DUMMY_SEQ_NUM_OF_EMPTY_PANEL) {
                        $panel_state_base['factory_species_code'] = $pasw->factory_species_code;
                        $panel_state_base['stage_start_date'] = $pasw->date;
                        $panel_state_base['panel_status'] = PanelStatus::OPERATION;

                        $fgs = $factory_growing_stages->findBySequenceNumber($sequence_number);
                        $panel_state_base['growing_stage_sequence_number'] = $sequence_number;
                        $panel_state_base['cycle_pattern'] = $pasw["pattern_row_count_{$row}"];
                        $panel_state_base['current_growth_stage'] = $fgs->growing_stage;
                        $panel_state_base['next_growth_stage_date'] = $fgs->getGrowingStage()->getNextGrowthStageDate(
                            $factory,
                            $pasw->date,
                            $fgs->growing_term
                        );

                        // 次のステージが存在する? (次のステージが収穫ではない?)
                        if ($next_fgs = $factory_growing_stages->findNextBySequenceNumber($sequence_number)) {
                            $panel_state_base['next_growing_stage_sequence_number'] = $next_fgs->sequence_number;
                            $panel_state_base['next_growth_stage'] = $next_fgs->growing_stage;
                        }

                        $pcsw = $planned_cultivation_status_works->findByDateAndGrowingStageSequenceNumber(
                            SimulationDate::parse($panel_state_base['date']),
                            $panel_state_base['growing_stage_sequence_number']
                        );

                        $panel_state_base['number_of_holes'] = $pcsw->number_of_holes;
                        $panel_state_base['using_hole_count'] = $pcsw->number_of_holes;

                        $number_of_moving_panels = $pcsw->getNumberOfMovingPanelsByPatternName(
                            $panel_state_base['cycle_pattern'],
                            $factory_cycle_pattern_items
                        );
                    }
                    if ($sequence_number === GrowthSimulation::DUMMY_SEQ_NUM_OF_EMPTY_PANEL) {
                        $pcsw = $planned_cultivation_status_works->findByDateAndGrowingStageSequenceNumber(
                            SimulationDate::parse($panel_state_base['date']),
                            $pasw["fixed_growing_stages_count_{$row}"]
                        );

                        $number_of_moving_panels = $pcsw->getNumberOfMovingPanelsByPatternName(
                            $pasw["fixed_pattern_row_count_{$row}"],
                            $factory_cycle_pattern_items
                        );
                    }

                    // パネル座標はX軸、Y軸ともに1からスタート
                    // サイクルパターンの移動パネル数分押し込む
                    [$x_current_bed_position, $y_current_bed_position] = array_fill(0, 2, 1);
                    $panels = ($number_of_moving_panels > 0) ? range(1, $number_of_moving_panels) : [];
                    foreach ($panels as $panel) {
                        $panel_id = $panel_id + 1;
                        $panel_states_list[$row][] = array_merge(
                            $panel_state_base,
                            compact('panel_id', 'x_current_bed_position', 'y_current_bed_position')
                        );

                        $x_current_bed_position = $x_current_bed_position + 1;
                        if ($x_current_bed_position > $panel_state_base['x_coordinate_panel']) {
                            $x_current_bed_position = 1;
                            $y_current_bed_position = $y_current_bed_position + 1;
                        }
                    }
                }

                // 前日までにパネルが配置されていた場合、
                // 今回配置分のパネル数分、Y軸の座標をスライド
                foreach ($panel_states_of_previous_day->filterByRow($row, $pasw->date) as $ps) {
                    $shifted_ps = $ps->toArray();
                    $shifted_y_current_bed_position =
                        $shifted_ps['y_current_bed_position'] +
                        (int)($number_of_moving_panels / $shifted_ps['x_coordinate_panel']);

                    if ($shifted_y_current_bed_position <= $ps['y_coordinate_panel']) {
                        $shifted_ps['date'] = $pasw->date;
                        $shifted_ps['y_current_bed_position'] = $shifted_y_current_bed_position;
                        $shifted_ps['created_at'] = Chronos::now()->format('Y-m-d H:i:s');
                        $shifted_ps['updated_at'] = Chronos::now()->format('Y-m-d H:i:s');

                        $panel_states_list[$row][] = $shifted_ps;
                    }
                }
            }

            $this->panel_state_repo->replacePanels($pasw, $panel_states_list);
            $this->logInfo('panel status registration.', [
                'processing' => $pasw->date,
                'bed_column' => $pasw->bed_column,
            ]);
        }

        $this->logInfo('panel status registration：end.', ['end_sequence' => $panel_id]);

        return $recent_panel_moving_dates;
    }

    /**
     * INFOログ出力
     */
    public function logInfo($message, $contents = [])
    {
        $this->application_logger->info($message, $contents);
    }
}

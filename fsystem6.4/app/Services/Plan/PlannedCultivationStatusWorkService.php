<?php

declare(strict_types=1);

namespace App\Services\Plan;

use Illuminate\Database\Connection;
use Maatwebsite\Excel\Excel;
use App\Models\Plan\GrowthSimulation;
use App\Models\Plan\Collections\PlannedCultivationStatusWorkCollection;
use App\Repositories\Plan\PlannedCultivationStatusWorkRepository;
use App\ValueObjects\Date\SimulationDate;

class PlannedCultivationStatusWorkService
{
    /**
     * @var \Illuminate\Database\Connection
     */
    private $db;

    /**
     * @var \Maatwebsite\Excel\Excel
     */
    private $excel;

    /**
     * @var \App\Repositories\Plan\PlannedCultivationStatusWorkRepository
     */
    private $planned_cultivation_status_work_repo;

    /**
     * @param  \Illuminate\Database\Connection $db
     * @param  \Maatwebsite\Excel\Excel $excel
     * @param  \App\Repositories\Plan\PlannedCultivationStatusWorkRepository $planned_cultivation_status_work_repo
     * @return void
     */
    public function __construct(
        Connection $db,
        Excel $excel,
        PlannedCultivationStatusWorkRepository $planned_cultivation_status_work_repo
    ) {
        $this->db = $db;
        $this->excel = $excel;
        $this->planned_cultivation_status_work_repo = $planned_cultivation_status_work_repo;
    }

    /**
     * 生産計画栽培状況取得
     *
     * @param  \App\Models\Plan\GrowthSimulation $growth_simulation
     * @param  \App\ValueObjects\Date\SimulationDate $simulation_date
     * @return \App\Models\Plan\Collections\PlannedCultivationStatusWorkCollection
     */
    public function getPlannedCultivationStatusWorks(
        GrowthSimulation $growth_simulation,
        SimulationDate $simulation_date
    ): PlannedCultivationStatusWorkCollection {
        return $this->planned_cultivation_status_work_repo
            ->getPlannedCultivationStatusWorks($growth_simulation, $simulation_date);
    }

    /**
     * 各階栽培株数を帳票出力する
     *
     * @param \App\Models\Plan\GrowthSimulation $growth_simulation
     * @param \App\ValueObjects\Date\SimulationDate $simulation_date
     */
    public function exportPlannedCultivationStatusWorks(
        GrowthSimulation $growth_simulation,
        SimulationDate $simulation_date
    ) {
        $planned_cultivation_status_works = $this
            ->getPlannedCultivationStatusWorks($growth_simulation, $simulation_date);
        $simulatable_dates = $simulation_date
            ->getSimulatableDatesOnTheWeek($growth_simulation->factory_species->factory);

        $config = config('settings.data_link.plan.planned_cultivation_status_work');
        $file_name = generate_file_name($config['file_name'], [
            $growth_simulation->factory_species->factory->factory_abbreviation,
            $growth_simulation->factory_species->factory_species_name,
            $growth_simulation->simulation_name,
            implode('～', [
                head($simulatable_dates)->format('Ymd'),
                last($simulatable_dates)->format('Ymd')
            ])
        ]);

        $this->excel->create($file_name, function ($excel) use (
            $growth_simulation,
            $planned_cultivation_status_works,
            $simulatable_dates,
            $config
        ) {
            // 各階栽培株数一覧表
            $excel->sheet($config['floor_cultivation_stock']['sheet_title'], function ($sheet) use (
                $growth_simulation,
                $planned_cultivation_status_works,
                $simulatable_dates,
                $config
            ) {
                $factory_growing_stages = $growth_simulation
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

                $sheet->loadView('plan.planned_cultivation_status_work.template_floor_cultivation_stock')
                    ->with(compact(
                        'growth_simulation',
                        'factory_growing_stages',
                        'base_patterns',
                        'planned_cultivation_status_works',
                        'simulatable_dates',
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
                    foreach (range(1, $growth_simulation->factory_species->factory->number_of_floors) as $floor) {
                        $sheet->mergeCells(
                            get_excel_column_str($hole_column).$row.':'.
                            get_excel_column_str($hole_column).($row + $number_of_patterns)
                        );

                        $row = $row + $number_of_patterns + 1;
                    }

                    $hole_column = $hole_column + 3 + count($simulatable_dates);
                }
            });

            // 各階栽培株数合計表
            $excel->sheet($config['floor_cultivation_stock_sum']['sheet_title'], function ($sheet) use (
                $growth_simulation,
                $simulatable_dates,
                $config
            ) {
                $factory_growing_stages = $growth_simulation
                    ->factory_species
                    ->factory_growing_stages
                    ->exceptSeeding()
                    ->reverse();
                $sheet->loadView('plan.planned_cultivation_status_work.template_stage_in_out_balance')
                    ->with(compact(
                        'growth_simulation',
                        'factory_growing_stages',
                        'base_patterns',
                        'simulatable_dates',
                        'config'
                    ));
            });

            // 栽培シュミレーション
            $excel->sheet($config['cultivation_simulation']['sheet_title'], function ($sheet) use (
                $growth_simulation,
                $simulatable_dates,
                $config
            ) {
                $factory_growing_stages = $growth_simulation
                    ->factory_species
                    ->factory_growing_stages
                    ->exceptSeeding()
                    ->reverse();

                $seeding_stage = $growth_simulation->factory_species->factory_growing_stages->first();
                $seeding_simulations = array_map(function ($sd) use ($growth_simulation, $seeding_stage) {
                    $filtered = $growth_simulation->growth_simulation_items
                        ->filterByGrwoingStageAndDate($seeding_stage, $sd);

                    $seeding_date = $seeding_stage->getGrowingStage()->getStageChangedDate(
                        $growth_simulation->factory_species->factory,
                        $sd->format('Y-m-d'),
                        $seeding_stage->growing_term
                    );

                    return (object)[
                        'tray' => $filtered->sum('panel_number'),
                        'seed' => $filtered->sum('stock_number'),
                        'simulation_date' => $sd,
                        'seeding_date' => SimulationDate::parse($seeding_date)
                    ];
                }, $simulatable_dates);

                $sheet->loadView('plan.planned_cultivation_status_work.template_floor_in_out_balance')
                    ->with(compact(
                        'growth_simulation',
                        'factory_growing_stages',
                        'simulatable_dates',
                        'seeding_stage',
                        'seeding_simulations',
                        'config'
                    ));
            });
        })
            ->download();
    }

    /**
     * 生産計画栽培状況作業トランの更新
     *
     * @param  \App\Models\Plan\GrowthSimulation $growth_simulation
     * @param  \App\ValueObjects\Date\SimulationDate $simulation_date
     * @param  array $params
     * @return void
     */
    public function updatePlannedCultivationStatusWorks(
        GrowthSimulation $growth_simulation,
        SimulationDate $simulation_date,
        array $params
    ) {
        $this->db->transaction(function () use ($growth_simulation, $simulation_date, $params) {
            $moving_panel_count_pattern_list = [];
            foreach ($params['moving_panel_count_pattern'] as $growing_stage_sequence_number => $week_value) {
                foreach ($week_value as $day_of_the_week => $pattern_value) {
                    $param_def = [
                        'growing_stages_sequence_number' => $growing_stage_sequence_number,
                        'day_of_the_week' => $day_of_the_week
                    ];

                    foreach ($pattern_value as $pattern_index => $value) {
                        $param_def['moving_panel_count_pattern_'.$pattern_index] = $value;
                    }

                    $moving_panel_count_pattern_list[] = $param_def;
                }
            }

            $moving_bed_count_floor_pattern_list = [];
            foreach ($params['moving_bed_count_floor_pattern'] as $growing_stage_sequence_number => $floor_value) {
                $param_def = [];
                foreach ($floor_value as $floor => $pattern_value) {
                    $floor_sum = 0;
                    foreach ($pattern_value as $pattern_index => $value) {
                        $param_def['moving_bed_count_floor_'.$floor.'_pattern_'.$pattern_index] = $value;
                        $floor_sum += $value;
                    }

                    $param_def['moving_bed_count_floor_'.$floor.'_sum'] = $floor_sum;
                }

                $moving_bed_count_floor_pattern_list[$growing_stage_sequence_number] = $param_def;
            }

            foreach ($moving_panel_count_pattern_list as $moving_panel_count_pattern) {
                $this->planned_cultivation_status_work_repo->updatePlannedCultivationStatusWork(
                    $growth_simulation,
                    $simulation_date,
                    array_merge(
                        $moving_panel_count_pattern,
                        $moving_bed_count_floor_pattern_list[
                            $moving_panel_count_pattern['growing_stages_sequence_number']
                        ]
                    )
                );
            }
        });
    }
}

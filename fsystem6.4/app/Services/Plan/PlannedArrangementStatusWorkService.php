<?php

declare(strict_types=1);

namespace App\Services\Plan;

use Illuminate\Database\Connection;
use Maatwebsite\Excel\Excel;
use App\Models\Master\Collections\FactoryGrowingStageCollection;
use App\Models\Plan\GrowthSimulation;
use App\Models\Plan\Collections\PlannedArrangementDetailStatusWorkCollection;
use App\Models\Plan\Collections\PlannedArrangementStatusWorkCollection;
use App\Repositories\Plan\PlannedArrangementStatusWorkRepository;
use App\Repositories\Plan\PlannedArrangementDetailStatusWorkRepository;
use App\ValueObjects\Date\SimulationDate;
use App\ValueObjects\Enum\DisplayKubun;

class PlannedArrangementStatusWorkService
{
    /**
     * @var string
     */
    private const EXCEL_FONT_FAMILY = 'MS PGothic';

    /**
     * @var int
     */
    private const EXCEL_ZOOM_SCALE = 40;

    /**
     * @var \Illuminate\Database\Connection
     */
    private $db;

    /**
     * @var \Maatwebsite\Excel\Excel
     */
    private $excel;

    /**
     * @var \App\Repositories\Plan\PlannedArrangementStatusWorkRepository
     */
    private $planned_arrangement_status_work_repo;

    /**
     * @var \App\Repositories\Plan\PlannedArrangementDetailStatusWorkRepository
     */
    private $planned_arrangement_detail_status_work_repo;

    /**
     * @param  \Illuminate\Database\Connection $db
     * @param  \Maatwebsite\Excel\Excel $excel
     * @param  \App\Repositories\Plan\PlannedArrangementStatusWorkRepository $planned_arrangement_status_work_repo
     * @param  \App\Repositories\Plan\PlannedArrangementDetailStatusWorkRepository
     *          $planned_arrangement_detail_status_work_repo
     * @return void
     */
    public function __construct(
        Connection $db,
        Excel $excel,
        PlannedArrangementStatusWorkRepository $planned_arrangement_status_work_repo,
        PlannedArrangementDetailStatusWorkRepository $planned_arrangement_detail_status_work_repo
    ) {
        $this->db = $db;
        $this->excel = $excel;
        $this->planned_arrangement_status_work_repo = $planned_arrangement_status_work_repo;
        $this->planned_arrangement_detail_status_work_repo = $planned_arrangement_detail_status_work_repo;
    }

    /**
     * 生産シミュレーションデータとシミュレーション日付を条件に生産計画配置状況作業データを取得
     *
     * @param  \App\Models\Plan\GrowthSimulation $growth_simulation
     * @param  \App\ValueObjects\Enum\DisplayKubun $display_kubun
     * @param  \App\ValueObjects\Date\SimulationDate $simulation_date
     * @return \App\Models\Plan\Collections\PlannedArrangementStatusWorkCollection
     */
    public function getPlannedArrangementStatusWorksBySimulationDate(
        GrowthSimulation $growth_simulation,
        DisplayKubun $display_kubun,
        SimulationDate $simulation_date
    ): PlannedArrangementStatusWorkCollection {
        return $this->planned_arrangement_status_work_repo->getPlannedArrangementStatusWorksBySimulationDate(
            $growth_simulation,
            $display_kubun,
            $simulation_date
        );
    }

    /**
     * 生産シミュレーションデータとシミュレーション日付を条件に生産計画配置詳細状況作業データを取得
     *
     * @param  \App\Models\Plan\GrowthSimulation $growth_simulation
     * @param  \App\ValueObjects\Enum\DisplayKubun $display_kubun
     * @param  \App\ValueObjects\Date\SimulationDate $simulation_date
     * @return \App\Models\Plan\Collections\PlannedArrangementDetailStatusWorkCollection
     */
    public function getPlannedArrangementDetailStatusWorksBySimulationDate(
        GrowthSimulation $growth_simulation,
        DisplayKubun $display_kubun,
        SimulationDate $simulation_date
    ): PlannedArrangementDetailStatusWorkCollection {
        return $this->planned_arrangement_detail_status_work_repo
            ->getPlannedArrangementDetailStatusWorksBySimulationDate(
                $growth_simulation,
                $display_kubun,
                $simulation_date
            );
    }

    /**
     * 栽培パネル配置図の帳票出力
     *
     * @param  \App\Models\Plan\GrowthSimulation $growth_simulation
     * @param  \App\ValueObjects\Date\SimulationDate $simulation_date
     * @param  \App\Models\Master\Collections\FactoryGrowingStageCollection $factory_growing_stages
     * @param  array $factory_layout
     * @param  string $label_of_bed
     */
    public function exportPlannedArrangementStatusWork(
        GrowthSimulation $growth_simulation,
        SimulationDate $simulation_date,
        FactoryGrowingStageCollection $factory_growing_stages,
        array $factory_layout,
        string $label_of_bed
    ) {
        $sheet_name = trans('view.plan.planned_arrangement_status_work.index');
        $file_name = generate_file_name($sheet_name, [
            $growth_simulation->factory_species->factory->factory_abbreviation,
            $growth_simulation->factory_species->species->species_name,
            $growth_simulation->simulation_name,
            $simulation_date->format('Ymd')
        ]);

        return $this->excel
            ->create($file_name, function ($excel) use (
                $growth_simulation,
                $simulation_date,
                $factory_growing_stages,
                $factory_layout,
                $label_of_bed,
                $sheet_name
            ) {
                $excel->sheet($sheet_name, function ($sheet) use (
                    $growth_simulation,
                    $simulation_date,
                    $factory_growing_stages,
                    $factory_layout,
                    $label_of_bed
                ) {
                    $sheet->setFontFamily(self::EXCEL_FONT_FAMILY);
                    $sheet->getSheetView()->setZoomScale(self::EXCEL_ZOOM_SCALE);

                    $sheet->loadView('plan.planned_arrangement_status_work.export')
                        ->with(compact(
                            'growth_simulation',
                            'simulation_date',
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
     * @param  \App\Models\Plan\GrowthSimulation $growth_simulation
     * @param  \App\ValueObjects\Date\SimulationDate $simulation_date
     * @param  \App\Models\Master\Collections\FactoryGrowingStageCollection $factory_growing_stages
     * @param  array $factory_layout
     */
    public function exportPlannedArrangementDetailStatusWork(
        GrowthSimulation $growth_simulation,
        SimulationDate $simulation_date,
        FactoryGrowingStageCollection $factory_growing_stages,
        array $factory_layout
    ) {
        $sheet_name = trans('view.plan.planned_arrangement_status_work.detail');
        $file_name = generate_file_name($sheet_name, [
            $growth_simulation->factory_species->factory->factory_abbreviation,
            $growth_simulation->factory_species->species->species_name,
            $growth_simulation->simulation_name,
            $simulation_date->format('Ymd')
        ]);

        return $this->excel
            ->create($file_name, function ($excel) use (
                $growth_simulation,
                $simulation_date,
                $factory_growing_stages,
                $factory_layout,
                $sheet_name
            ) {
                $excel->sheet($sheet_name, function ($sheet) use (
                    $growth_simulation,
                    $simulation_date,
                    $factory_growing_stages,
                    $factory_layout
                ) {
                    $sheet->setFontFamily(self::EXCEL_FONT_FAMILY);
                    $sheet->getSheetView()->setZoomScale(self::EXCEL_ZOOM_SCALE);

                    $sheet->loadView('plan.planned_arrangement_status_work.export_detail')
                        ->with(compact(
                            'growth_simulation',
                            'simulation_date',
                            'factory_growing_stages',
                            'factory_layout'
                        ));
                });
            })
            ->download();
    }

    /**
     * 生産計画配置状況作業データと生産計画配置詳細状況作業データの更新
     *
     * @param  \App\Models\Plan\GrowthSimulation $growth_simulation
     * @param  \App\ValueObjects\Date\SimulationDate $simulation_date
     * @param  array $statuses
     * @return void
     */
    public function updatePlannedArrangementStatusWorks(
        GrowthSimulation $growth_simulation,
        SimulationDate $simulation_date,
        array $statuses
    ): void {
        $this->db->transaction(function () use ($growth_simulation, $simulation_date, $statuses) {
            $factory_growing_stages = $growth_simulation->getFactoryGrowingStagesOnTheDate($simulation_date);

            $planned_arrangement_status_works = $growth_simulation->planned_arrangement_status_works;
            $fixed_planned_arrangement_status_works = $planned_arrangement_status_works
                ->filterFixedBySimulationDate($simulation_date);

            $planned_arrangement_detail_status_works = $this->planned_arrangement_detail_status_work_repo
                ->getPlannedArrangementDetailStatusWorksWithBedCordinationBySimulationDate(
                    $growth_simulation,
                    $simulation_date
                );

            $planned_arrangement_status_works
                ->filterProcessingBySimulationDate($simulation_date)
                ->each(function ($pasw) use (
                    $growth_simulation,
                    $statuses,
                    $factory_growing_stages,
                    $fixed_planned_arrangement_status_works,
                    $planned_arrangement_detail_status_works
                ) {
                    $column = $pasw->bed_column;
                    $fixed = $fixed_planned_arrangement_status_works->where('bed_column', $column)->first();

                    foreach (range(1, $pasw->bed_row_number) as $row) {
                        $stage = $statuses[$column][$row]['stage'] ?? null;
                        $pattern = $statuses[$column][$row]['pattern'] ?? null;
                        if (! is_null($stage)) {
                            $stage = (int)$stage;
                        }

                        $padsw = $planned_arrangement_detail_status_works
                            ->findPlannedArrangementDetailStatusWorkWithPreviousDay($growth_simulation, $row, $column);

                        if ($stage) {
                            $number_of_panels = $factory_growing_stages->getNumberOfPanels($stage, $pattern);
                            foreach (range(1, $number_of_panels) as $panel) {
                                $padsw["panel_status_{$panel}"] = $stage;
                            }
                            foreach ((range(1, $padsw->x_coordinate_panel * $padsw->y_coordinate_panel)) as $panel) {
                                $next = $number_of_panels + $panel;
                                $padsw["panel_status_{$next}"] = $padsw["prev_panel_status_{$panel}"];
                            }
                        }

                        if ($stage === GrowthSimulation::DUMMY_SEQ_NUM_OF_EMPTY_PANEL) {
                            $pattern = null;
                            foreach (range(1, $padsw->x_coordinate_panel) as $panel) {
                                $padsw["panel_status_{$panel}"] = $stage;
                            }
                            foreach ((range(1, $padsw->x_coordinate_panel * $padsw->y_coordinate_panel)) as $panel) {
                                $next = $padsw->x_coordinate_panel + $panel;
                                $padsw["panel_status_{$next}"] = $padsw["prev_panel_status_{$panel}"];
                            }
                        }

                        if (is_null($stage)) {
                            // 前日の状況をそのまま引き継ぎ
                            foreach ((range(1, $padsw->x_coordinate_panel * $padsw->y_coordinate_panel)) as $panel) {
                                $padsw["panel_status_{$panel}"] = $padsw["prev_panel_status_{$panel}"];
                            }
                        }

                        if ($padsw->isDirty()) {
                            $this->planned_arrangement_detail_status_work_repo
                                ->savePlannedArrangementDetailStatusWork($padsw);
                        }

                        $fixed_stage = $fixed["growing_stages_count_{$row}"] ?? null;
                        $fixed_pattern = $fixed["pattern_row_count_{$row}"] ?? null;

                        if ($stage === $fixed_stage && $pattern === $fixed_pattern) {
                            $stage = $pattern = null;
                        }

                        $pasw["growing_stages_count_{$row}"] = $stage;
                        $pasw["pattern_row_count_{$row}"] = $pattern;
                    }

                    if ($pasw->isDirty()) {
                        $this->planned_arrangement_status_work_repo->savePlannedArrangementStatusWork($pasw);
                    }
                });
        });
    }
}

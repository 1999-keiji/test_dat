<?php

declare(strict_types=1);

namespace App\Services\FactoryProductionWork;

use PHPExcel_Cell_DataValidation;
use PHPExcel_RichText;
use PHPExcel_Style_Border;
use Maatwebsite\Excel\Excel;
use App\Models\Master\Factory;
use App\Models\Master\FactoryGrowingStage;
use App\Models\Master\FactorySpecies;
use App\Models\Master\Species;
use App\Repositories\Plan\CropRepository;
use App\Repositories\Plan\ForecastedProductRateRepository;
use App\Repositories\Plan\PanelStateRepository;
use App\ValueObjects\Date\WorkingDate;

class WorkInstructionService
{
    /**
     * @var \Maatwebsite\Excel\Excel
     */
    private $excel;

    /**
     * @var \App\Repositories\Plan\PanelStateRepository
     */
    private $panel_state_repo;

    /**
     * @var \App\Repositories\Plan\ForecastedProductRateRepository
     */
    private $forecasted_product_rate_repo;

    /**
     * @var \App\Repositories\Plan\CropRepository
     */
    private $crop_repo;

    /**
     * @param  \Maatwebsite\Excel\Excel $excel
     * @param  \App\Repositories\Plan\PanelStateRepository $panel_state_repository
     * @param  \App\Repositories\Plan\ForecastedProductRateRepository $forecasted_product_rate_repository
     * @param  \App\Repositories\Plan\CropRepository $crop_repository
     * @return void
     */
    public function __construct(
        Excel $excel,
        PanelStateRepository $panel_state_repository,
        ForecastedProductRateRepository $forecasted_product_rate_repository,
        CropRepository $crop_repository
    ) {
        $this->excel = $excel;
        $this->panel_state_repo = $panel_state_repository;
        $this->forecasted_product_rate_repo = $forecasted_product_rate_repository;
        $this->crop_repo = $crop_repository;
    }

    /**
     * 工場取扱品種ごとの作業指示内容を取得
     *
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Master\Species $species
     * @param  array $working_date_term
     * @param  array $factory_species_list
     */
    public function getFactorySpeciesListWithWorkingDates(
        Factory $factory,
        Species $species,
        array $working_date_term
    ): array {
        $base_working_dates = $working_date_term['from']
            ->getWorkingDatesExceptFactoryRest($working_date_term['to'], $factory);

        $factory_species_list = [];
        foreach ($factory->factory_species->filterBySpecies($species->species_code) as $fs) {
            $working_dates = [];
            $factory_growing_stages = $fs->factory_growing_stages;

            foreach ($base_working_dates as $wd) {
                $stages = [];

                // 前ステージ検索
                $panel_counts_previous = $this->panel_state_repo
                    ->getPanelCountsStageHasChanged($fs, $wd->format('Y-m-d'));
                // 次ステージ検索
                $panel_counts_next = $this->panel_state_repo
                    ->getPanelCountsTransferedNextStage($fs, $wd->format('Y-m-d'));
                // 収穫ステージ検索
                $panel_count_harvest = $this->panel_state_repo
                    ->getPanelCountsWillHarvesting($fs, $wd->format('Y-m-d'));

                // 播種ステージ情報
                $seeding_stage = $factory_growing_stages->first();
                $stages[] = [
                    'growing_stage' => $seeding_stage->growing_stage,
                    'growing_stage_name' => $seeding_stage->growing_stage_name,
                    'seeding_tray_count' => $this->calcSeedingTrayCount(
                        $fs,
                        $seeding_stage,
                        $seeding_stage->getGrowingStage()->getNextGrowthStageDate(
                            $factory,
                            $wd->format('Y-m-d'),
                            $seeding_stage->growing_term
                        )
                    )
                ];

                foreach ($factory_growing_stages as $idx => $fgs) {
                    if ($idx === 0) {
                        continue;
                    }

                    $stage = [
                        'growing_stage' => $fgs->growing_stage,
                        'growing_stage_name' => $fgs->growing_stage_name,
                        'seeding_date' => '',
                        'panel_count_previous' => 0,
                        'panel_count_next' => 0
                    ];

                    $seeding_date = $wd->format('Y-m-d');
                    foreach ($factory_growing_stages->filterPreviousStages($fgs->sequence_number) as $prev_stage) {
                        $seeding_date = $prev_stage->getGrowingStage()->getStageChangedDate(
                            $factory,
                            $seeding_date,
                            $prev_stage->growing_term
                        );
                    }

                    $stage['seeding_date'] = WorkingDate::parse($seeding_date)->formatWithDayOfWeek();

                    foreach ($panel_counts_previous as $ps) {
                        if ($fgs->sequence_number === $ps->next_growing_stage_sequence_number) {
                            $stage['panel_count_previous'] = $ps->panel_count_previous;
                        }
                    }
                    foreach ($panel_counts_next as $ps) {
                        if ($fgs->sequence_number === $ps->growing_stage_sequence_number) {
                            $stage['panel_count_next'] = $ps->panel_count_next;
                        }
                    }

                    // 播種の次のステージ?
                    if ($idx === 1) {
                        $stage['seeding_tray_count'] = $this->calcSeedingTrayCount(
                            $fs,
                            $seeding_stage,
                            $wd->format('Y-m-d')
                        );
                    }

                    $stages[] = $stage;
                }

                // 収穫ステージ
                $seeding_date = $wd->format('Y-m-d');
                foreach ($factory_growing_stages as $fgs) {
                    $seeding_date = $fgs->getGrowingStage()->getStageChangedDate(
                        $factory,
                        $seeding_date,
                        $fgs->growing_term
                    );
                }

                $stages[] = [
                    'seeding_date' => WorkingDate::parse($seeding_date)->formatWithDayOfWeek(),
                    'harvesting_panel_count' => $panel_count_harvest->harvesting_panel_count ?? 0,
                    'harvesting_hole_count' => (int)($panel_count_harvest->harvesting_hole_count ?? 0)
                ];

                $working_dates[] = [
                    'working_date' => $wd,
                    'stages' => $stages
                ];
            }

            $factory_species_list[] = [
                'factory_species' => $fs,
                'working_dates' => $working_dates
            ];
        }

        return $factory_species_list;
    }

    /**
     * 播種トレイ数取得
     *
     * @param  \App\Models\Master\FactorySpecies $factory_species
     * @param  \App\Models\Master\FactoryGrowingStage $seeding_stage
     * @param  string $working_date
     * @return int
     */
    private function calcSeedingTrayCount(
        FactorySpecies $factory_species,
        FactoryGrowingStage $seeding_stage,
        string $working_date
    ) {
        $panel_state = $this->panel_state_repo->getPanelCountAfterSeeding($factory_species, $working_date);

        $multiplicated = (($panel_state->panel_count ?? 0) * ($panel_state->number_of_holes ?? 0));
        return (int)(ceil(ceil($multiplicated / $seeding_stage->yield_rate) / $seeding_stage->number_of_holes));
    }

    /**
     * 作業日ごとの製品化指示内容を取得
     *
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Master\Species $species
     * @param  array $working_date_term
     * @return array
     */
    public function getWorkingDatesWithSpecies(Factory $factory, Species $species, array $working_date_term): array
    {
        $average_weight = $factory->factory_species
            ->filterBySpecies($species->species_code)
            ->getAverageWeight();

        $base_working_dates = $working_date_term['from']->getWorkingDates($working_date_term['to']);

        $forecasted_product_rates = $this->forecasted_product_rate_repo
            ->getForecastedProductRatesBySpeciesAndHarvestingDate([
                'factory_code' => $factory->factory_code,
                'species_code' => $species->species_code,
                'display_term' => 'date',
            ], [
                'from' => $working_date_term['from'],
                'to' => $working_date_term['to']
            ]);

        $working_dates = [];
        foreach ($base_working_dates as $wd) {
            $fpr = $forecasted_product_rates->filterByHarvestingDate($wd);
            $details = [];

            $crops = $this->crop_repo->getCrops($factory, $species, $wd);
            foreach ($crops as $c) {
                $details[] = [
                    'number_of_heads' => $c->number_of_heads,
                    'packaging_style' => $c->packaging_style,
                    'crop_number' => $c->crop_number,
                    'product_quantity' => $c->product_quantity ?: ''
                ];
            }

            $details[] = [
                'number_of_heads' => '',
                'packaging_style' => '',
                'crop_number' => '',
                'product_quantity' => ''
            ];

            $working_dates[] = [
                'working_date' => $wd,
                'average_weight' => $average_weight,
                'forecasted_product_rate' => $fpr,
                'details' => $details
            ];
        }

        return $working_dates;
    }

    /**
     * Excel作成
     *
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Master\Species $species
     * @param  array $factory_species_list
     * @param  array $working_dates
     * @param  string $file_name
     */
    public function exportWorkInstruction(
        Factory $factory,
        Species $species,
        array $factory_species_list,
        array $working_dates
    ) {
        $config = config('constant.factory_production_work.work_instruction');
        $file_name = generate_file_name($config['file_name'], [
            $factory->factory_abbreviation,
            $species->species_abbreviation,
            head($working_dates)['working_date']->format('Ymd').'～'.last($working_dates)['working_date']->format('Ymd')
        ]);

        return $this->excel->create($file_name, function ($excel) use (
            $factory,
            $species,
            $factory_species_list,
            $working_dates,
            $config
        ) {
            $trimming_stage_columns = [];
            $sheet_title_base_list = $config['sheet_title_base'];

            // 作業指示書
            foreach ($factory_species_list as $fs) {
                foreach ($fs['working_dates'] as $wd) {
                    $trimming_stage_column = '';
                    $sheet_title = implode('_', [
                        $sheet_title_base_list['work'],
                        str_limit_ja($fs['factory_species']->factory_species_name, 15, ''),
                        $wd['working_date']->format('Ymd')
                    ]);

                    $excel->sheet($sheet_title, function ($sheet) use (
                        $factory,
                        $species,
                        $wd,
                        $config,
                        &$trimming_stage_column
                    ) {
                        $sheet->setAutoSize();
                        $sheet->setFontFamily($config['work']['font_family']);
                        $sheet->setFontSize($config['work']['font_size']);
                        $sheet->getDefaultColumnDimension()->setWidth($config['work']['width']);
                        $sheet->getDefaultRowDimension()->setRowHeight($config['work']['height']);
                        $sheet->setWidth($config['work']['width_list']);

                        $sheet->loadView('factory_production_work.work_instruction.template_work_instruction')
                            ->with('factory', $factory)
                            ->with('species', $species)
                            ->with('working_date', $wd['working_date'])
                            ->with('seeding_stage', head($wd['stages']))
                            ->with('stages', $wd['stages'])
                            ->with('harvesting_stage', last($wd['stages']));

                        foreach ($config['work']['fixed_merge_cells'] as $cell_range) {
                            $sheet->mergeCells($cell_range);
                        }
                        foreach ($config['work']['fixed_outline_borders'] as $cell_range) {
                            $sheet->getStyle($cell_range)
                                ->getBorders()
                                ->getOutline()
                                ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                        }

                        $iterative_data_start_column = $config['work']['iterative_data_start_column'];
                        $oneset_iterative_data_culumn_num = $config['work']['oneset_iterative_data_culumn_num'];

                        $harvest_stage_column = $iterative_data_start_column;
                        $iterative_data_count = count($wd['stages']);
                        if ($iterative_data_count !== 0) {
                            $harvest_stage_column += ($iterative_data_count - 2) * $oneset_iterative_data_culumn_num;
                        }

                        $trimming_stage_column = $harvest_stage_column + 4;
                        $remarks_column = $trimming_stage_column + 5;
                        $working_report_column = $remarks_column + 5;

                        foreach ($wd['stages'] as $idx => $stage) {
                            if ($idx === 0 || $idx === count($wd['stages']) - 1) {
                                continue;
                            }

                            foreach ($config['work']['iterative_data_merge_set_list'] as $value) {
                                $sheet->mergeCells(
                                    $value['target_first_row'].
                                    ($iterative_data_start_column + $value['target_first_culumn_plus_num']).':'.
                                    $value['target_second_row'].
                                    ($iterative_data_start_column + $value['target_second_culumn_plus_num'])
                                );
                            }

                            $sheet
                                ->getStyle('B'.$iterative_data_start_column.':C'.($iterative_data_start_column + 3))
                                ->getBorders()
                                ->getOutline()
                                ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                            $sheet->setBorder(
                                'D'.$iterative_data_start_column.':F'.($iterative_data_start_column + 3),
                                'thin'
                            );
                            $sheet->getStyle('G'.$iterative_data_start_column.':K'.($iterative_data_start_column + 3))
                                ->getBorders()
                                ->getOutline()
                                ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                            $sheet->setBorder(
                                'L'.$iterative_data_start_column.':O'.($iterative_data_start_column + 3),
                                'thin'
                            );

                            $iterative_data_start_column += $config['work']['oneset_iterative_data_culumn_num'];
                        }

                        $sheet->mergeCells('D'.$harvest_stage_column.':F'.($harvest_stage_column + 3));
                        $sheet->mergeCells('G'.$harvest_stage_column.':K'.($harvest_stage_column + 1));
                        $sheet->mergeCells('L'.$harvest_stage_column.':O'.($harvest_stage_column + 3));
                        $sheet->getStyle('B'.$harvest_stage_column.':C'.($harvest_stage_column + 3))
                            ->getBorders()->getOutline()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                        $sheet->setBorder('D'.$harvest_stage_column.':F'.($harvest_stage_column + 3), 'thin');
                        $sheet->getStyle('G'.$harvest_stage_column.':K'.($harvest_stage_column + 3))
                            ->getBorders()->getOutline()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                        $sheet->setBorder('L'.$harvest_stage_column.':O'.($harvest_stage_column + 3), 'thin');

                        $sheet->mergeCells('D'.$trimming_stage_column.':F'.($trimming_stage_column + 3));
                        $sheet->mergeCells('G'.$trimming_stage_column.':K'.($trimming_stage_column + 1));
                        $sheet->mergeCells('L'.$trimming_stage_column.':O'.($trimming_stage_column + 3));
                        $sheet->getStyle('B'.$trimming_stage_column.':C'.($trimming_stage_column + 3))
                            ->getBorders()->getOutline()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                        $sheet->setBorder('D'.$trimming_stage_column.':F'.($trimming_stage_column + 3), 'thin');
                        $sheet->getStyle('G'.$trimming_stage_column.':K'.($trimming_stage_column + 3))
                            ->getBorders()->getOutline()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                        $sheet->setBorder('L'.$trimming_stage_column.':O'.($trimming_stage_column + 3), 'thin');

                        $sheet->mergeCells('B'.($remarks_column + 1).':O'.($remarks_column + 3));
                        $sheet->mergeCells('B'.($working_report_column + 1).':O'.($working_report_column + 4));
                    });

                    $trimming_stage_columns[$sheet_title] = [
                        'working_date' => $wd['working_date'],
                        'trimming_stage_column' =>  $trimming_stage_column
                    ];
                }
            }

            // 製品化指示書
            foreach ($working_dates as $wd) {
                $linked_sheets = [];
                foreach ($trimming_stage_columns as $sheet_title => $trimming_stage_column) {
                    if ($wd['working_date']->eq($trimming_stage_column['working_date'])) {
                        $linked_sheets[] = "'{$sheet_title}'".'!G'.$trimming_stage_column['trimming_stage_column'];
                    }
                }

                $harvest_plan_shares = (count($linked_sheets) !== 0) ? '=SUM('.implode(',', $linked_sheets).')' : '0';
                $sheet_title = implode('_', [
                    $sheet_title_base_list['productization'],
                    $wd['working_date']->format('Ymd')
                ]);

                $excel->sheet($sheet_title, function ($sheet) use (
                    $factory,
                    $species,
                    $config,
                    $harvest_plan_shares,
                    $wd
                ) {
                    $sheet->setAutoSize();
                    $sheet->setFontFamily($config['productization']['font_family']);
                    $sheet->setFontSize($config['productization']['font_size']);
                    $sheet->getDefaultColumnDimension()->setWidth($config['productization']['width']);
                    $sheet->getDefaultRowDimension()->setRowHeight($config['productization']['height']);
                    $sheet->getColumnDimension('J')->setVisible(false);
                    $sheet->getColumnDimension('N')->setVisible(false);

                    foreach ($config['productization']['fixed_merge_cells'] as $cell_range) {
                        $sheet->mergeCells($cell_range);
                    }
                    foreach ($config['productization']['fixed_borders'] as $cell_range) {
                        $sheet->setBorder($cell_range, 'thin');
                    }
                    foreach ($config['productization']['fixed_new_lines'] as $cell) {
                        $sheet->getStyle($cell)->getAlignment()->setWrapText(true);
                    }
                    foreach ($config['productization']['fixed_outline_thick_borders'] as $value) {
                        $sheet->getStyle($value)
                            ->getBorders()
                            ->getOutline()
                            ->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
                    }

                    $sheet->cells('B13', function ($cells) use ($species, $config) {
                        $rich_text = new PHPExcel_RichText();
                        $rich_text->createText("{$species->species_name}\n");
                        $rich_text->createTextRun('株数')->getFont()->getColor()->setARGB('FFFF0000');
                        $cells->setValue($rich_text);
                    });

                    $iterative_data_count = count($wd['details']);
                    $oneset_culumn_num = $config['productization']['oneset_iterative_data_culumn_num'];
                    $iterative_data_start_column = $config['productization']['iterative_data_start_column'];

                    $iterative_data_end_column =
                        $iterative_data_count * $oneset_culumn_num + $iterative_data_start_column - 1;
                    $total_shares_culumn = $iterative_data_end_column + 1;
                    $total_weight_culumn = $iterative_data_end_column + 3;
                    $discard_related_culumn = $total_weight_culumn + 2;
                    $sample_order_culumn = $discard_related_culumn + 2;
                    $sample_order_header_culumn = $sample_order_culumn + 1;

                    $sheet->loadView('factory_production_work.work_instruction.template_productization_instruction')
                        ->with('factory', $factory)
                        ->with('species', $species)
                        ->with('working_date', $wd['working_date'])
                        ->with('harvest_plan_shares', $harvest_plan_shares)
                        ->with('forecasted_product_rate', $wd['forecasted_product_rate'])
                        ->with('details', $wd['details'])
                        ->with('iterative_data_end_column', $iterative_data_end_column)
                        ->with('total_shares_culumn', $total_shares_culumn)
                        ->with('total_weight_culumn', $total_weight_culumn);

                    $sheet->setCellValue('N13', '(　　　　　　　　)');

                    $sheet->getStyle('B'.$discard_related_culumn)->getAlignment()->setWrapText(true);
                    $sheet->getStyle('E'.$iterative_data_start_column.':F'.$iterative_data_end_column)
                        ->getAlignment()
                        ->setWrapText(true);

                    $sheet->mergeCells('B'.$iterative_data_start_column.':C'.($iterative_data_end_column - 1));
                    $sheet->setCellValue('B'.$iterative_data_start_column, $species->species_name);
                    $sheet->setBorder('B'.$iterative_data_start_column.':P'.$iterative_data_end_column, 'thin');

                    $sheet->setCellValue('B'.$iterative_data_end_column, '基本想定重量');
                    $sheet->cells('B'.$iterative_data_end_column, function ($cells) {
                        $cells->setFontSize(8);
                        $cells->setFontWeight('bold');
                        $cells->setAlignment('left');
                        $cells->setValignment('center');
                    });

                    $sheet->setCellValue('C'.$iterative_data_end_column, $wd['average_weight']);
                    $sheet->setColumnFormat(['C'.$iterative_data_end_column => '#,##0"g"']);
                    $sheet->cells('C'.$iterative_data_end_column, function ($cells) {
                        $cells->setFontSize(8);
                        $cells->setFontWeight('bold');
                        $cells->setAlignment('right');
                        $cells->setValignment('center');
                    });

                    foreach ($wd['details'] as $idx => $detail) {
                        foreach ($config['productization']['iterative_data_merge_list'] as $value) {
                            $sheet->mergeCells(
                                $value['first'].$iterative_data_start_column.
                                $value['second'].($iterative_data_start_column + 2)
                            );
                        }

                        $sheet->setCellValue(
                            'J'.$iterative_data_start_column,
                            '=G'.$iterative_data_start_column.'*H'.$iterative_data_start_column
                        );

                        $cell = $sheet->getCell('K'.$iterative_data_start_column)->getDataValidation();
                        $cell->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
                        $cell->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
                        $cell->setShowDropDown(true);
                        $cell->setFormula1('"'.implode(',', ['固定', '増可', '増減可']).'"');

                        $sheet->setCellValue(
                            'N'.$iterative_data_start_column,
                            '=G'.$iterative_data_start_column.'*L'.$iterative_data_start_column
                        );

                        $sheet->setCellValue(
                            'O'.$iterative_data_start_column,
                            (
                                '=G'.$iterative_data_start_column.
                                '*L'.$iterative_data_start_column.
                                '*C'.$iterative_data_end_column.
                                '/1000'
                            )
                        );

                        if ($idx === (count($wd['details']) -1)) {
                            $cell = $sheet->getCell('E'.$iterative_data_start_column)->getDataValidation();
                            $cell->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
                            $cell->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
                            $cell->setShowDropDown(true);

                            $packaging_styles =
                                $factory->factory_products->getPackagingStylesBySpeciesForWorkInstruction($species);
                            $cell->setFormula1('"'.implode(',', $packaging_styles).'"');
                        }

                        $iterative_data_start_column += $oneset_culumn_num;
                    }

                    foreach ($config['productization']['data_totals_merge_list'] as $value) {
                        $sheet->mergeCells(
                            $value['first'].
                            $total_shares_culumn.
                            $value['second'].
                            ($total_shares_culumn + 1)
                        );

                        $sheet->mergeCells(
                            $value['first'].
                            $total_weight_culumn.
                            $value['second'].
                            ($total_weight_culumn + 1)
                        );
                    }

                    $sheet->setCellValue('H'.$total_shares_culumn, '=SUM(J16:J'.$iterative_data_end_column.')');
                    $sheet->setCellValue('O'.$total_shares_culumn, '=SUM(N16:N'.$iterative_data_end_column.')');
                    $sheet->setCellValue(
                        'H'.$total_weight_culumn,
                        '=H'.$total_shares_culumn.'*C'.$iterative_data_end_column.'/1000'
                    );
                    $sheet->setCellValue(
                        'O'.$total_weight_culumn,
                        '=O'.$total_shares_culumn.'*C'.$iterative_data_end_column.'/1000'
                    );
                    $sheet->setBorder('B'.$total_shares_culumn.':P'.($total_weight_culumn + 1), 'thin');

                    foreach ($config['productization']['discard_related_merge_list'] as $value) {
                        $sheet->mergeCells(
                            $value['first'].
                            $discard_related_culumn.
                            $value['second'].
                            ($discard_related_culumn + 1)
                        );
                    }

                    $sheet->setCellValue(
                        'I'.$discard_related_culumn,
                        '=IFERROR(N'.$discard_related_culumn.
                        '/(O'.$total_weight_culumn.'+N'.$discard_related_culumn.'), "")'
                    );
                    $sheet->getStyle('B'.$discard_related_culumn)->getAlignment()->setWrapText(true);
                    $sheet->setBorder(
                        'G'.$discard_related_culumn.':P'.($discard_related_culumn + 1),
                        'medium'
                    );
                    $sheet->getStyle('B17:G'.($total_weight_culumn + 1))
                        ->getBorders()
                        ->getOutline()
                        ->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
                    $sheet->getStyle('H17:K'.($total_weight_culumn + 1))
                        ->getBorders()
                        ->getOutline()
                        ->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
                    $sheet->getStyle('L17:P'.($total_weight_culumn + 1))
                        ->getBorders()
                        ->getOutline()
                        ->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);

                    $sheet->setBorder(
                        'B'.($sample_order_header_culumn).':L'.($sample_order_header_culumn + 2),
                        'thin'
                    );
                });
            }
        })
            ->export();
    }
}

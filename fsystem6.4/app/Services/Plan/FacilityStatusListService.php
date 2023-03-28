<?php

declare(strict_types=1);

namespace App\Services\Plan;

use Maatwebsite\Excel\Facades\Excel;
use App\Models\Master\Factory;
use App\Repositories\Plan\PanelStateRepository;
use App\ValueObjects\Date\WorkingDate;

class FacilityStatusListService
{
    /**
     * @var \App\Repositories\Plan\PanelStateRepository
     */
    private $panel_state_repo;

    /**
     * @param  \App\Repositories\Plan\PanelStateRepository $panel_state_repo
     * @return void
     */
    public function __construct(PanelStateRepository $panel_state_repo)
    {
        $this->panel_state_repo = $panel_state_repo;
    }

    /**
     * 施設利用状況一覧 Excel出力
     *
     * @param \App\Models\Master\Factory $factory
     * @param \App\ValueObjects\Date\WorkingDate $working_date
     */
    public function exportFacilityStatusList(Factory $factory, WorkingDate $working_date)
    {
        $config = config('constant.plan.facility_status_list');
        $working_date_term = [
            'from' => $working_date,
            'to' => $working_date->addDays($config['list_days'])
        ];

        // Excel出力処理
        $file_name = generate_file_name($config['excel_file_name'], [$factory->factory_factory_abbreviation]);
        Excel::create($file_name, function ($excel) use ($factory, $working_date_term, $config) {
            // 施設利用状況一覧表
            $excel->sheet($config['excel_sheet_name'], function ($sheet) use ($factory, $working_date_term, $config) {
                $this->setExcelFacilitySheet($factory, $working_date_term, $config, $sheet);
            });

            $export_panel_data = []; // 日付別施設利用状況表用データ

            $growing_panel_states = $this->panel_state_repo->getGrowingPanelStates($factory, $working_date_term);
            foreach ($growing_panel_states as $ps) {
                $export_panel_data['use'][$ps->date][$ps->factory_species_code][$ps->growing_stage_sequence_number] =
                    $ps->number_of_holes_count;
                $export_panel_data['stock'][$ps->date][$ps->factory_species_code][$ps->growing_stage_sequence_number] =
                    $ps->using_hole_count;
            }

            $bed_using_status_list = $this->panel_state_repo->getBedUsingStatusList($factory, $working_date_term);
            foreach ($bed_using_status_list as $ps) {
                $export_panel_data['bed'][$ps->date][$ps->factory_species_code][$ps->growing_stage_sequence_number]
                    = $ps->number_of_beds;
            }

            // 日付別施設利用状況表
            $date = $working_date_term['from'];
            while ($date->lte($working_date_term['to'])) {
                $sheet_name = $date->format('Ymd');
                $excel->sheet($sheet_name, function ($sheet) use ($factory, $date, $config, $export_panel_data) {
                    $this->setExcelFacilityDateSheet($factory, $date, $config, $export_panel_data, $sheet);
                });

                $date = $date->addDay();
            }
        })
            ->download();
    }

    /**
     * Excel出力 施設利用状況一覧表
     *
     * @param \App\Models\Master\Factory $factory
     * @param array $working_date_term
     * @param array $config
     * @param $sheet
     */
    private function setExcelFacilitySheet(Factory $factory, array $working_date_term, array $config, $sheet)
    {
        $sheet_list[] = [''];
        $sheet_list[] = $this->setTitle($factory, $working_date_term, $config);
        $sheet_list[] = [''];
        $sheet_list = $this->setHeader($working_date_term, $config, $sheet_list);
        $sheet_list = $this->setData($factory, $working_date_term, $config, $sheet_list);

        $columns_name = range('A', 'Z');                                                                             // 列名リスト
        $column_count = count($sheet_list[3]) - 1;                                                                   // ヘッダー項目数
        $factory_species_count = $factory->factory_species->count();                                                 // 工場取扱品種数
        $list_days = $config['list_days'];                                                                           // データ取得日数(28)
        $species_row_num = $config['species_row_num'];                                                               // 品種開始行(5)
        $data_begin_row_num = $config['data_begin_row_num'];                                                         // データ表記開始行(6)
        $data_begin_column_num = $config['data_begin_column_num'];                                                   // データ表記開始列(3)
        $bed_sum_row_num = $data_begin_row_num + $factory_species_count;                                             // 総ベッド数行
        $rate_row_num = $bed_sum_row_num + 1;                                                                        // 利用率行
        $last_column_name = $column_count >= count($columns_name) ?
            $columns_name[$column_count/count($columns_name)-1].$columns_name[$column_count%count($columns_name)] :
            $columns_name[$column_count];                                                                            // Z以上の列対応

        // データ反映
        $sheet->loadView('plan.facility_status_list.export')->with('sheet_list', $sheet_list);

        // セル結合
        $sheet->mergeCells('B4:C4');
        $sheet->mergeCells('B'.$bed_sum_row_num.':C'.$bed_sum_row_num);
        $sheet->mergeCells('B'.$rate_row_num.':C'.$rate_row_num);
        for ($i=$species_row_num; $i<=$bed_sum_row_num; $i++) {
            if ($sheet_list[$i][1] !== '' && $species_row_num !== $i) {
                $sheet->mergeCells('B'.$species_row_num.':B'.$i);
            }
            $species_row_num = $sheet_list[$i][1] !== '' ? $i + 1 : $species_row_num;
        }
        for ($i=$data_begin_column_num; $i<=($data_begin_column_num + $list_days); $i++) {
            $column_name = $i >= count($columns_name) ?
                $columns_name[$i/count($columns_name)-1].$columns_name[$i%count($columns_name)] : $columns_name[$i];
            $sheet->mergeCells($column_name.'4:'.$column_name.'5');
            $sheet->setWidth([$column_name => 10]);
            $sheet->getStyle($column_name.$rate_row_num)->getNumberFormat()->setFormatCode('0%');
        }

        // セル内書式設定
        $sheet->setWidth(['A' => 10, 'B' => 20, 'C' => 40]);
        $sheet->getStyle('B'.$bed_sum_row_num)->getNumberFormat()->setFormatCode('計(0ベッド)');
        $sheet->cell('B2', function ($cell) {
            $cell->setFontWeight('bold');
        });
        $sheet->cells('B4:'.$last_column_name.'5', function ($cells) {
            $cells->setAlignment('center')->setValignment('center');
            $cells->setBackground(config('constant.plan.facility_status_list.excel_header_color'));
            $cells->setFontWeight('bold');
        });
        $sheet->cells('B'.$data_begin_row_num.':B'.($species_row_num + $factory_species_count), function ($cells) {
            $cells->setValignment('center');
        });
        $sheet->cells('B'.$bed_sum_row_num.':B'.$rate_row_num, function ($cells) {
            $cells->setAlignment('right');
            $cells->setFontWeight('bold');
        });

        // 枠線設定
        $sheet->setBorder('B4:'.$last_column_name.$rate_row_num, 'thin');
    }

    /**
     * Excel出力 日付別施設利用状況表
     *
     * @param \App\Models\Master\Factory $factory
     * @param \App\ValueObjects\Date\WorkingDate $working_date
     * @param array $config
     * @param array $export_panel_data
     * @param $sheet
     */
    private function setExcelFacilityDateSheet(
        Factory $factory,
        WorkingDate $working_date,
        array $config,
        array $export_panel_data,
        $sheet
    ) {
        $sheet_list[] = [''];
        $sheet_list[] = $this->setTitle($factory, $working_date, $config);

        $factory_species_row_nums = [];
        foreach ($factory->factory_species->sortBySpecies() as $key => $fs) {
            $sheet_list[] = [''];
            $sheet_list[] = ['', $fs->factory_species_name];
            $factory_species_row_nums[$key]['species'] = count($sheet_list);
            $sheet_list[] = $config['excel_header_date'];
            $data_position = count($sheet_list) + 1;

            $date = $working_date->format('Y-m-d');
            foreach ($fs->factory_growing_stages as $idx => $fgs) {
                // 最初のステージ?(播種ステージ?)
                if ($idx === 0) {
                    continue;
                }

                $sheet_list[] = [
                    '',
                    $fgs->growing_stage_name,
                    $export_panel_data['bed'][$date][$fs->factory_species_code][$fgs->sequence_number] ?? 0,
                    $export_panel_data['use'][$date][$fs->factory_species_code][$fgs->sequence_number] ?? 0,
                    $fgs->number_of_holes,
                    $export_panel_data['stock'][$date][$fs->factory_species_code][$fgs->sequence_number] ?? 0
                ];
            }

            $sheet_list[] = [
                '',
                '計',
                '=SUM(C'.$data_position.':C'.count($sheet_list).')',
                '=SUM(D'.$data_position.':D'.count($sheet_list).')',
                '-',
                '=SUM(F'.$data_position.':F'.count($sheet_list).')'
            ];

            $factory_species_row_nums[$key]['sum'] = count($sheet_list);
        }

        $sheet->loadView('plan.facility_status_list.export')
            ->with('sheet_list', $sheet_list);

        // セル内書式設定
        $sheet->setWidth(['B' => 20, 'C' => 20, 'D' => 20, 'E' => 20, 'F' => 20]);
        $sheet->cell('B2', function ($cell) {
            $cell->setFontWeight('bold');
        });

        foreach ($factory_species_row_nums as $val) {
            $sheet->cell('B'.$val['species'], function ($cell) {
                $cell->setFontWeight('bold');
            });
            $sheet->cells('B'.($val['species'] + 1).':F'.($val['species'] + 1), function ($cells) {
                $cells->setAlignment('center');
                $cells->setBackground(config('constant.plan.facility_status_list.excel_header_color'));
                $cells->setFontWeight('bold');
            });
            $sheet->cells('B'.$val['sum'].':F'.$val['sum'], function ($cells) {
                $cells->setAlignment('right');
                $cells->setFontWeight('bold');
            });
            $sheet->cell('E'.$val['sum'], function ($cell) {
                $cell->setAlignment('center');
            });

            $sheet->setColumnFormat(['D'.($val['species'] + 2).':D'.$val['sum'] => '#,##0']);
            $sheet->setColumnFormat(['F'.($val['species'] + 2).':F'.$val['sum'] => '#,##0']);

            // 枠線設定
            $sheet->setBorder('B'.($val['species'] + 1).':F'.$val['sum'], 'thin');
        }

        return $sheet;
    }

    /**
     * Excel出力 タイトル
     *
     * @param  \App\Models\Master\Factory $factory
     * @param  array|\App\ValueObjects\Date\WorkingDate $working_date
     * @param  array $config
     * @return array
     */
    private function setTitle(Factory $factory, $working_date, array $config): array
    {
        $title = $config['excel_file_name'].'【'.$factory->factory_abbreviation. ' ';
        if ($working_date instanceof WorkingDate) {
            $title .= $working_date->formatShortWithDayOfWeek();
        }
        if (is_array($working_date)) {
            $title .= $working_date['from']->formatShortWithDayOfWeek().'～'.
                $working_date['to']->formatShortWithDayOfWeek();
        }

        return ['', $title.'】'];
    }

    /**
     * Excel出力 ヘッダー
     *
     * @param  array $working_date_term
     * @param  array $config
     * @param  array $sheet_list
     * @return array $sheet_list
     */
    private function setHeader(array $working_date_term, array $config, array $sheet_list): array
    {
        $header_row_num = count($sheet_list);
        $sheet_list[] = $config['excel_header_bed'];

        $date = $working_date_term['from'];
        while ($date->lte($working_date_term['to'])) {
            $sheet_list[$header_row_num][] = $date->formatShortWithDayOfWeek();
            $date = $date->addDay();
        }

        $sheet_list[] = config('constant.plan.facility_status_list.excel_header_factory');
        return $sheet_list;
    }

    /**
     * Excel出力 データセット
     *
     * @param  \App\Models\Master\Factory $factory
     * @param  array $working_date_term
     * @param  array $config
     * @param  array $sheet_list
     * @return array $sheet_list
     */
    private function setData(Factory $factory, array $working_date_term, array $config, array $sheet_list): array
    {
        $bed_data = [];
        $using_bed_numbers = $this->panel_state_repo->getUsingBedNumbers($factory, $working_date_term);
        foreach ($using_bed_numbers as $ps) {
            $bed_data[$ps->factory_species_code][$ps->date] = (int)$ps->number_of_beds;
        }

        $species_name = '';
        foreach ($factory->factory_species->sortBySpecies() as $fs) {
            $key_num = count($sheet_list);
            $sheet_list[] = [''];
            $sheet_list[$key_num][] = $fs->species->species_name === $species_name ? '' : $fs->species->species_name;
            $sheet_list[$key_num][] = $fs->factory_species_name;
            $species_name = $fs->species->species_name;

            $date = $working_date_term['from'];
            while ($date->lte($working_date_term['to'])) {
                $sheet_list[$key_num][] = $bed_data[$fs->factory_species_code][$date->format('Y-m-d')] ?? 0;
                $date = $date->addDay();
            }
        }

        $sum_row_num = count($sheet_list);
        $sheet_list[] = ['', $factory->factory_beds->count(), ''];
        $roundup_row_num = count($sheet_list);
        $sheet_list[] = $config['excel_ratio'];

        $columns_name = range('A', 'Z');
        $data_begin_row_num = $config['data_begin_row_num'];
        $data_begin_column_num = $config['data_begin_column_num'];

        for ($i = $data_begin_column_num; $i <= $config['list_days'] + $data_begin_column_num; $i++) {
            $column_name = $i >= count($columns_name) ?
                $columns_name[$i/count($columns_name)-1].$columns_name[$i%count($columns_name)] : $columns_name[$i];

            $sheet_list[$sum_row_num][] = '=SUM('.$column_name.$data_begin_row_num.':'.$column_name.$sum_row_num.')';
            $sheet_list[$roundup_row_num][] =
                '=ROUNDUP('.$column_name.$roundup_row_num.'/'.$factory->factory_beds->count().', 2)';
        }

        return $sheet_list;
    }
}

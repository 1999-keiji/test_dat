<?php

declare(strict_types=1);

namespace App\Services\Plan;

use Excel;
use PHPExcel_RichText;
use PHPExcel_Style_Protection;
use PHPExcel_Worksheet;
use Illuminate\Database\Connection;
use Maatwebsite\Excel\Classes\LaravelExcelWorksheet;
use App\Models\Master\Factory;
use App\Models\Master\Species;
use App\Repositories\Master\DeliveryFactoryProductRepository;
use App\Repositories\Master\FactoryProductRepository;
use App\Repositories\Order\OrderRepository;
use App\Repositories\Order\OrderForecastRepository;
use App\Repositories\Plan\CropRepository;
use App\Repositories\Plan\ForecastedProductRateRepository;
use App\Repositories\Plan\PanelStateRepository;
use App\Repositories\Stock\CarryOverStockRepository;
use App\ValueObjects\Date\DeliveryDate;
use App\ValueObjects\Date\HarvestingDate;
use App\ValueObjects\Enum\ShipmentLeadTime;

class GrowthSaleManagementService
{
    /**
     * @var \Illuminate\Database\Connection
     */
    private $db;

    /**
     * @var \App\Repositories\Master\FactoryProductRepository
     */
    private $factory_product_repo;

    /**
     * @var \App\Repositories\Master\DeliveryFactoryProductRepository
     */
    private $delivery_factory_product_repo;

    /**
     * @var \App\Repositories\Order\OrderForecastRepository
     */
    private $order_forecast_repository;

    /**
     * @var \App\Repositories\Order\OrderRepository
     */
    private $order_repository;

    /**
     * @var \App\Repositories\Plan\CropRepository
     */
    private $crop_repo;

    /**
     * @var \App\Repositories\Plan\PanelStateRepository
     */
    private $panel_state_repo;

    /**
     * @var \App\Repositories\Plan\ForecastedProductRateRepository
     */
    private $forecasted_product_rate_repo;

    /**
     * @var \App\Repositories\Stock\CarryOverStockRepository
     */
    private $carry_over_stock_repo;

    /**
     * @param  \Illuminate\Database\Connection $db
     * @param  \App\Repositories\Master\FactoryProductRepository factory_product_repository
     * @param  \App\Repositories\Master\DeliveryFactoryProductRepository $delivery_factory_product_repository
     * @param  \App\Repositories\Order\OrderForecastRepository $order_forecast_repository
     * @param  \App\Repositories\Order\OrderRepository $order_repository
     * @param  \App\Repositories\Plan\CropRepository $crop_repository
     * @param  \App\Repositories\Plan\PanelStateRepository $panel_state_repository
     * @param  \App\Repositories\Plan\ForecastedProductRateRepository $forecasted_product_rate_repository
     * @param  \App\Repositories\Stock\CarryOverStockRepository
     * @return void
     */
    public function __construct(
        Connection $db,
        FactoryProductRepository $factory_product_repository,
        DeliveryFactoryProductRepository $delivery_factory_product_repository,
        OrderForecastRepository $order_forecast_repository,
        OrderRepository $order_repository,
        CropRepository $crop_repo,
        PanelStateRepository $panel_state_repository,
        ForecastedProductRateRepository $forecasted_product_rate_repository,
        CarryOverStockRepository $carry_over_stock_repo
    ) {
        $this->db = $db;
        $this->factory_product_repo = $factory_product_repository;
        $this->delivery_factory_product_repo = $delivery_factory_product_repository;
        $this->order_forecast_repo = $order_forecast_repository;
        $this->order_repo = $order_repository;
        $this->crop_repo = $crop_repo;
        $this->panel_state_repo = $panel_state_repository;
        $this->forecasted_product_rate_repo = $forecasted_product_rate_repository;
        $this->carry_over_stock_repo = $carry_over_stock_repo;
    }

    /**
     * 生産・販売管理表出力用パラメータ作成
     *
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Master\Species $species
     * @param  \App\ValueObjects\Date\HarvestingDate $harvesting_date
     * @return array
     */
    public function createGrowthSaleManagementParam(
        Factory $factory,
        Species $species,
        HarvestingDate $harvesting_date
    ): ?array {
        $packaging_styles = $this->factory_product_repo
            ->getFactoryProductsByFactoryAndSpecies($factory->factory_code, $species->species_code)
            ->groupByPackagingStyle()
            ->map(function ($grouped, $packaging_style) use ($factory, $species, $harvesting_date) {
                $packaging_style = array_combine([
                    'number_of_heads',
                    'weight_per_number_of_heads',
                    'input_group'
                ], explode('-', $packaging_style));

                $packaging_style['factory_products'] = $grouped
                    ->map(function ($fp) use ($harvesting_date) {
                        $delivery_destinations = $this->delivery_factory_product_repo
                            ->getDeliveryDestinationsByFactoryProduct($fp)
                            ->map(function ($dfp) use ($harvesting_date) {
                                $dfp->shipment_lead_time = ! is_null($dfp->shipment_lead_time) ?
                                    new ShipmentLeadTime($dfp->shipment_lead_time) :
                                    (new ShipmentLeadTime)->getDefaultShipmentLeadTime();

                                if (! is_null($dfp->latest_delivery_date)) {
                                    $dfp->latest_delivery_date = DeliveryDate::parse($dfp->latest_delivery_date);
                                }

                                $dfp->order_forecasts = $this->order_forecast_repo
                                    ->getOrderForecastsByDeliveryFactoryProductAndHarvestingDate(
                                        $dfp,
                                        $harvesting_date
                                    );

                                $dfp->orders = $this->order_repo
                                    ->getOrdersByDeliveryFactoryProductAndHarvestingDate($dfp, $harvesting_date);

                                return $dfp;
                            });

                        $fp->delivery_destinations = $delivery_destinations;
                        return $fp;
                    })
                    ->reject(function ($fp) {
                        return $fp->delivery_destinations->isEmpty();
                    });

                $packaging_style['crops'] = $this->crop_repo
                    ->getCropsByFactoryAndSpeciesAndHarvestingDate(
                        $factory,
                        $species,
                        $harvesting_date,
                        $packaging_style
                    );

                $packaging_style['carry_over_stock'] = (int)($this->carry_over_stock_repo
                    ->getCarryOveredStock(
                        $factory,
                        $species,
                        $harvesting_date,
                        $packaging_style
                    )
                        ->carry_over_stock_quantity ?? 0);

                return (object)$packaging_style;
            })
            ->reject(function ($packaging_style) {
                return $packaging_style->factory_products->isEmpty();
            })
            ->values()
            ->all();

        if (count($packaging_styles) === 0) {
            return null;
        }

        $panel_states = $this->panel_state_repo
            ->getHarvestingQuantitiesByFactoryAndSpecies($factory, $species, $harvesting_date);

        $forecasted_product_rates = $this->forecasted_product_rate_repo
            ->getForecastedProductRatesByFactoryAndSpeciesAndHarvestingDate($factory, $species, $harvesting_date);

        $config = config('settings.data_link.plan.growth_sale_management.visible_sheet');

        // 一日あたりの行数
        $daily_row_num = 2;
        // 参照先予想製品化率セルリスト
        $referring_rows_of_productes_rate = [];
        // 参照先行リスト
        $reference_target_row_list = [];

        // 参照先行リスト作成
        $count = 0;
        for ($i = $config['start_row_crop'] + 1; $i <= $config['end_row_crop']; $i = $i + $daily_row_num) {
            if ($count == $config['skip_row_cnt']) {
                $count = 0;
                $i++;
                $i = $i - $daily_row_num;
                continue;
            }

            $reference_target_row_list[] = $i;
            $referring_rows_of_productes_rate[] = $i;
            $count++;
        }

        // 商品規格あたりの納入先数カウント
        $delivery_destination_count_list = [];
        foreach ($packaging_styles as $idx => $packaging_style) {
            $delivery_destination_count_list[$idx] = 0;
            foreach ($packaging_style->factory_products as $fp) {
                $delivery_destination_count_list[$idx] += $fp->delivery_destinations->count();
            }
        }

        // パック数、使用株数の出力列算出 + 使用株数参照先を取得
        $pack_number_set_num = $config['pack_number_set_num'];
        $pack_number_column_list = $use_shares_column_list = [];
        foreach ($delivery_destination_count_list as $delivery_destination_count) {
            $pack_number_column_list[] = get_excel_column_str($pack_number_set_num);
            $use_shares_column_list[] = get_excel_column_str($pack_number_set_num + 1);
            $pack_number_set_num += $delivery_destination_count + $config['not_delivery_destination_column_num'];
        }

        $date_data['pack_number_column_list'] = $pack_number_column_list;

        // パック数出力列リストを用いてパック数（出来高）参照先を取得
        // 予想製品化率と行は同じなので列番号だけ置き換え
        $referring_cells_of_crop = [];
        foreach ($pack_number_column_list as $pack_number_column) {
            foreach ($referring_rows_of_productes_rate as $row) {
                $referring_cells_of_crop[] = $pack_number_column.$row;
            }
        }

        // 使用株数合計を求めるExcel関数を取得
        $plan_use_shares_list = [];
        foreach ($reference_target_row_list as $reference_target_row) {
            $implode_list = [];
            foreach ($use_shares_column_list as $use_shares_column) {
                $implode_list[] = $use_shares_column.$reference_target_row;
            }

            $plan_use_shares_list[] = '=SUM('.implode(',', $implode_list).')';
        }

        // 週間合計予想製品化率参照先リスト
        $target_sum_week_cell_list = $config['target_sum_week_cell_list'];
        $plan_use_shares_reference_count = 0;

        for ($i = 0; $i < $harvesting_date->getOutputDateTermOfGrowthSaleManagement(); $i++) {
            $added_harvesting_date = $harvesting_date->addDays($i);

            $date_data['date'][$i]['date'] = $added_harvesting_date;
            $date_data['date'][$i]['plan_use_shares_reference'] =
                $plan_use_shares_list[$plan_use_shares_reference_count];
            $date_data['date'][$i]['set_row_num'] = $reference_target_row_list[$plan_use_shares_reference_count];

            foreach ($target_sum_week_cell_list as $sum_week_row => $value) {
                if ($date_data['date'][$i]['set_row_num'] < $sum_week_row) {
                    $date_data['date'][$i]['target_sum_week'] = $value;
                    break;
                }
            }

            $plan_use_shares_reference_count++;
        }

        // 書込用フォーキャスト表作成に必要なデータの作成
        $visible_data_list = $this->createVisibleData(
            $factory,
            $date_data,
            $panel_states,
            $packaging_styles,
            $forecasted_product_rates,
            $pack_number_set_num
        );

        // 隠し取込用フォーキャスト表作成に必要なデータの作成
        $hidden_data_list = $this->createHiddenData(
            $date_data,
            $forecasted_product_rates,
            $packaging_styles,
            $referring_rows_of_productes_rate,
            $referring_cells_of_crop,
            $pack_number_set_num
        );

        return [
            'visible_data_list' => $visible_data_list + compact('packaging_styles'),
            'hidden_data_list' => $hidden_data_list
        ];
    }

    /**
     * 生産・管理表作成に必要なデータの作成
     */
    private function createVisibleData(
        $factory,
        $date_data,
        $panel_states,
        $packaging_styles,
        $forecasted_product_rates,
        $pack_number_set_num
    ) {
        foreach ($date_data['date'] as $idx => $date) {
            $date_data['date'][$idx]['harvesting_quantity'] = $panel_states
                ->filterByHarvestingDate($date['date'])
                ->harvesting_quantity ?? 0;
            $date_data['date'][$idx]['harvesting_quantity_cell'] = 'C'.$date['set_row_num'];

            $date_data['date'][$idx]['crop_failure'] = 0;
            $date_data['date'][$idx]['crop_failure_cell'] =
                get_excel_column_str($pack_number_set_num + 5).$date['set_row_num'];

            $date_data['date'][$idx]['advanced_harvest'] = 0;
            $date_data['date'][$idx]['advanced_harvest_cell'] =
                get_excel_column_str($pack_number_set_num + 6).$date['set_row_num'];

            $date_data['date'][$idx]['product_rate'] =
                $date['date']->isWorkingDay($factory) ? $date['target_sum_week'] : 0;
            $date_data['date'][$idx]['can_import'] = true;

            $fpr = $forecasted_product_rates->filterByHarvestingDate($date['date']);
            if (! is_null($fpr)) {
                $can_import = (bool)$fpr->can_import;
                $date_data['date'][$idx]['product_rate'] = $can_import ?
                    ($fpr->product_rate / 100) :
                    'IFERROR((G'.$date['set_row_num'].'/D'.$date['set_row_num'].'), 0)';
                $date_data['date'][$idx]['crop_failure'] = $can_import ?
                    (int)$fpr->crop_failure :
                    (int)$fpr->actual_crop_failure;
                $date_data['date'][$idx]['advanced_harvest'] = $can_import ?
                    (int)$fpr->advanced_harvest :
                    (int)$fpr->actual_advanced_harvest;
                $date_data['date'][$idx]['can_import'] = $can_import;
            }

            $shipping_date = $date['date']->getDefaultShippingDate();

            $details = [];
            foreach ($packaging_styles as $ps) {
                $pack_number = $disposal_quantity = 0;
                if ($c = $ps->crops->filterByHarvestingDate($date['date'])) {
                    $pack_number = $date_data['date'][$idx]['can_import'] ?
                        $c->crop_number :
                        $c->product_quantity;

                    $disposal_quantity = (int)$c->disposal_quantity;
                }

                $orders = $ps->factory_products
                    ->map(function ($fp) use ($shipping_date) {
                        return $fp->delivery_destinations->map(function ($dd) use ($shipping_date) {
                            $will_ship_on_the_date = $dd->shipment_lead_time->willShipOnTheDate();
                            if ($shipping_date->willDisplayOrderOnTheDate($dd->latest_delivery_date)) {
                                return $dd->orders
                                    ->filterByShippingDate($shipping_date, $dd->shipment_lead_time)
                                    ->toSumOfQuantityAndWeight() +
                                    [
                                        'is_not_forecasted_order' => true,
                                        'will_ship_on_the_date' => $will_ship_on_the_date
                                    ];
                            }

                            return [
                                'quantity' => $dd->order_forecasts
                                    ->filterByShippingDate($shipping_date, $dd->shipment_lead_time)
                                    ->first()
                                    ->forecast_number ?? 0,
                                'is_not_forecasted_order' => false,
                                'will_ship_on_the_date' => $will_ship_on_the_date
                            ];
                        })
                        ->all();
                    })
                    ->all();

                $details[] = [
                    'pack_number' => $pack_number,
                    'orders' => $orders,
                    'disposal_quantity' => $disposal_quantity
                ];
            }

            $date_data['date'][$idx]['shipping_date'] = $shipping_date;
            $date_data['date'][$idx]['details'] = $details;
        }

        return [
            'date_list' => $date_data
        ];
    }

    /**
     * 隠し取込用フォーキャスト表作成に必要なデータの作成
     */
    private function createHiddenData(
        $date_data,
        $base_forecasted_product_rates,
        $packaging_styles,
        $referring_rows_of_productes_rate,
        $referring_cells_of_crop,
        $pack_number_set_num
    ) {
        $latest_product_rate = 0;

        $forecasted_product_rates = [];
        foreach (array_values($date_data['date']) as $idx => $date) {
            $fpr = [
                'date' => $date['date']->format('Y-m-d'),
                'product_rate' => $latest_product_rate,
                'crop_failure' => 0,
                'advanced_harvest' => 0,
                'updated_at' => null,
                'referring_row' => $referring_rows_of_productes_rate[$idx],
                'pack_number_set_num' => $pack_number_set_num
            ];

            $existed = $base_forecasted_product_rates->filterByHarvestingDate($date['date']);
            if (! is_null($existed)) {
                $fpr['product_rate'] = $latest_product_rate = $existed->product_rate;
                $fpr['crop_failure'] = $existed->crop_failure;
                $fpr['advanced_harvest'] = $existed->advanced_harvest;
                $fpr['updated_at'] = $existed->updated_at->format('Y-m-d H:i:s');
            }

            $forecasted_product_rates[] = $fpr;
        }

        $crops = [];
        $idx = 0;
        foreach ($packaging_styles as $ps) {
            foreach ($date_data['date'] as $date) {
                $crop = [
                    'date' => $date['date']->format('Y-m-d'),
                    'number_of_heads' => $ps->number_of_heads,
                    'weight_per_number_of_heads' => $ps->weight_per_number_of_heads,
                    'input_group' => $ps->input_group,
                    'crop_number' => 0,
                    'updated_at' => null,
                    'refrrring_cell' => $referring_cells_of_crop[$idx]
                ];

                $existed = $ps->crops->filterByHarvestingDate($date['date']);
                if (! is_null($existed)) {
                    $crop['crop_number'] = $existed->crop_number;
                    $crop['updated_at'] = $existed->updated_at->format('Y-m-d H:i:s');
                }

                $crops[] = $crop;
                $idx += 1;
            }
        }

        return compact('forecasted_product_rates', 'crops');
    }

    /**
     * 生産・販売管理表Excel作成
     *
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Master\Species $species
     * @param  \App\ValueObjects\Date\HarvestingDate $harvesting_date
     * @param  array $params
     */
    public function createForecastExcel(
        Factory $factory,
        Species $species,
        HarvestingDate $harvesting_date,
        array $params
    ) {
        $config = config('settings.data_link.plan.growth_sale_management');
        $file_name = generate_file_name($config['visible_sheet']['sheet_title'], [
            $factory->factory_abbreviation,
            $species->species_name,
            implode('～', [
                $harvesting_date->format('Ymd'),
                $harvesting_date->getEndOfDateOfGrowthSaleManagement()->format('Ymd')
            ])
        ]);

        return Excel::create($file_name, function ($excel) use ($factory, $species, $params, $config) {
            // 書込用フォーキャスト表シート
            $sheet_name = $config['visible_sheet']['sheet_title'];
            $excel->sheet($sheet_name, function ($sheet) use ($factory, $species, $params, $config) {
                $sheet->protect($config['sheet_lock_pass']);
                $sheet->getSheetView()->setZoomScale(70);
                $sheet->setFontFamily('HG丸ｺﾞｼｯｸM-PRO');
                $sheet->setAutoSize(false);
                $sheet->setFontSize(11);
                $sheet->getDefaultColumnDimension()->setWidth(7);
                $sheet->getDefaultRowDimension()->setRowHeight(18);
                $sheet->setFreeze('K9');

                $sheet->loadView('plan.growth_sale_management.template_visible')
                    ->with('factory', $factory)
                    ->with('species', $species)
                    ->with('date_list', $params['visible_data_list']['date_list']);

                foreach ($config['visible_sheet']['merge_cell_list'] as $cells) {
                    $sheet->mergeCells($cells);
                    $sheet->setBorder($cells, 'thin');
                    $sheet->cells($cells, function ($cells) {
                        $cells->setAlignment('center');
                        $cells->setValignment('center');
                        $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }

                $sheet->getStyle('A3:J7')->getAlignment()->setTextRotation(255);
                $sheet->setCellValue('A8', '当初在庫数');
                $sheet->cells('A8:J8', function ($cells) {
                    $cells->setBorder('thin', 'thick', 'thin', 'none');
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                });

                $sheet->getStyle('G3')->getAlignment()->setWrapText(true);
                $text = new PHPExcel_RichText();
                $text->createText("予定使用株数\n  ・");
                $text->createTextRun('実使用株数')->getFont()->getColor()->setARGB('FFFF0000');
                $sheet->setCellValue('G3', $text);

                // 納入工場商品情報表示開始列番号
                $delivery_factory_product_start_at = $config['visible_sheet']['pack_number_set_num'];
                // ユーザー定義リスト
                $column_format_list = $config['visible_sheet']['column_format_list'];
                // 縦書き用数値
                $fixed_row_number = $config['visible_sheet']['fixed_row_number'];
                // 一つの納入工場商品列で納入先ではない列の数
                $not_delivery_destination_column_num = $config['visible_sheet']['not_delivery_destination_column_num'];
                // 一つの納入工場商品列で納入先より前の副見出し
                $before_sub_value = $config['visible_sheet']['before_delivery_destination_sub_value'];
                // 一つの納入工場商品列で納入先内の副見出し
                $in_sub_value = $config['visible_sheet']['in_delivery_destination_sub_value'];
                // 一つの納入工場商品列で納入先より後の副見出し
                $after_sub_value = $config['visible_sheet']['after_delivery_destination_sub_value'];
                // 一つの納入工場商品列で納入先より前の見出し
                $before_delivery_destination_value = $config['visible_sheet']['before_delivery_destination_value'];
                // 一つの納入工場商品列で納入先より後の見出し
                $after_delivery_destination_value = $config['visible_sheet']['after_delivery_destination_value'];
                // 複数の接尾辞を設定する行
                $multi_suffix_defined_row = '6';
                // 商品規格背景色
                $product_regulation_background_color = $config['visible_sheet']['product_regulation_background_color'];
                // 繰越在庫株数の算出式
                $carry_over_stocks = [];

                foreach ($params['visible_data_list']['packaging_styles'] as $idx => $packaging_style) {
                    $delivery_destination_abbreviations = $packaging_style->factory_products
                        ->map(function ($fp) {
                            return $fp->delivery_destinations->pluck('delivery_destination_abbreviation');
                        })
                        ->flatten()
                        ->all();

                    $delivery_destination_count = count($delivery_destination_abbreviations);
                    if ($idx !== 0) {
                        $delivery_factory_product_start_at = $next_delivery_factory_product_start_at;
                    }

                    // パック数表示列、かつ、納入工場商品別項目開始列
                    $pack_number_column = get_excel_column_str($delivery_factory_product_start_at);
                    // 使用株数表示列
                    $using_shares_column = get_excel_column_str($delivery_factory_product_start_at + 1);
                    // 納入先表示開始列
                    $delivery_destination_start_column = get_excel_column_str($delivery_factory_product_start_at + 2);
                    // 小計表示列
                    $subtotal_column =
                        get_excel_column_str($delivery_factory_product_start_at + $delivery_destination_count + 2);
                    // 重量表示列
                    $weight_column =
                        get_excel_column_str($delivery_factory_product_start_at + $delivery_destination_count + 3);
                    // 過不足数表示列
                    $over_and_under_number_column =
                        get_excel_column_str($delivery_factory_product_start_at + $delivery_destination_count + 4);
                    // 在庫数表示列、かつ、納入工場商品別項目終了列
                    $stock_number_column =
                        get_excel_column_str($delivery_factory_product_start_at + $delivery_destination_count + 6);
                    // 次の納入工場商品別項目開始列
                    $next_delivery_factory_product_start_at = $delivery_factory_product_start_at +
                        $delivery_destination_count +
                        $not_delivery_destination_column_num;

                    $sheet->mergeCells(
                        $pack_number_column.$fixed_row_number['product_weight_row'].':'.
                        $stock_number_column.$fixed_row_number['product_weight_row']
                    );
                    $sheet->setColumnFormat([
                        $pack_number_column.$fixed_row_number['product_weight_row'] =>
                            $column_format_list['weight_per_number_of_heads']
                    ]);
                    $sheet->setCellValue(
                        $pack_number_column.$fixed_row_number['product_weight_row'],
                        $packaging_style->weight_per_number_of_heads
                    );
                    $sheet->setBorder($pack_number_column.$fixed_row_number['product_weight_row'], 'thin');
                    $sheet->cell($pack_number_column.$fixed_row_number['product_weight_row'], function ($cells) {
                        $cells->setAlignment('center');
                        $cells->setValignment('center');
                    });
                    $sheet->cell(
                        $pack_number_column.$fixed_row_number['product_weight_row'].':'.
                            $stock_number_column.$fixed_row_number['end_row'],
                        function ($cells) use ($product_regulation_background_color, $idx) {
                            $color = ($idx % 2 === 0) ?
                                $product_regulation_background_color['odd'] :
                                $product_regulation_background_color['even'];
                            $cells->setBackground($color);
                        }
                    );

                    $set_number = $config['visible_sheet']['number_set3'];
                    foreach ($set_number as $number) {
                        $set_cell = $before_sub_value[$number] !== '' ?
                            $before_sub_value[$number] :
                            $packaging_style->number_of_heads;
                        $set_cell_next = $in_sub_value[$number] !== '' ?
                            $in_sub_value[$number] :
                            '';
                        $set_cell_final = $after_sub_value[$number] != '' ?
                            $after_sub_value[$number] :
                            '';
                        if ($number === $multi_suffix_defined_row) {
                            $sheet->setColumnFormat([
                                $pack_number_column.$number => $column_format_list['number_of_heads']
                            ]);
                        }

                        $sheet->mergeCells($pack_number_column.$number.':'.$using_shares_column.$number);
                        $sheet->setCellValue($pack_number_column.$number, $set_cell);
                        $sheet->setBorder($pack_number_column.$number, 'thin');

                        if ($set_cell_next !== '') {
                            $sheet->mergeCells($delivery_destination_start_column.$number.':'.$subtotal_column.$number);
                            $sheet->setCellValue($delivery_destination_start_column.$number, $set_cell_next);
                            $sheet->setBorder($delivery_destination_start_column.$number, 'thin');
                        }
                        if ($set_cell_next === '') {
                            $idx = 0;
                            foreach ($packaging_style->factory_products as $fp) {
                                foreach ($fp->delivery_destinations as $idx_of_dd => $dd) {
                                    $column = get_excel_column_str($delivery_factory_product_start_at + 2 + $idx);
                                    $sheet->setCellValue($column.$number, $fp->number_of_cases);
                                    $sheet->cell($column.$number, function ($cells) use ($fp, $idx_of_dd) {
                                        $cells->setAlignment('center');
                                        $cells->setValignment('center');
                                        $cells->setBorder([
                                            'right' => [
                                                'style' => $idx_of_dd !== ($fp->delivery_destinations->count() - 1) ?
                                                    'dotted' :
                                                    'thin'
                                            ],
                                            'bottom' => ['style' => 'thin'],
                                        ]);
                                    });

                                    $idx = $idx + 1;
                                }

                                $column = get_excel_column_str($delivery_factory_product_start_at + 2 + $idx);
                                $sheet->cell($column.$number, function ($cells) {
                                    $cells->setAlignment('center');
                                    $cells->setValignment('center');
                                    $cells->setBorder([
                                        'right' => ['style' => 'thin'],
                                        'bottom' => ['style' => 'thin'],
                                    ]);
                                });
                            }

                            $column = get_excel_column_str($delivery_factory_product_start_at + 2 + $idx + 1);
                            $sheet->setBorder($column.$number, 'thin');
                            $sheet->cell($column.$number, function ($cells) {
                                $cells->setAlignment('center');
                                $cells->setValignment('center');
                            });
                        }

                        $sheet->mergeCells($over_and_under_number_column.$number.':'.$stock_number_column.$number);
                        $sheet->setCellValue($over_and_under_number_column.$number, $set_cell_final);
                        $sheet->setBorder($over_and_under_number_column.$number, 'thin');

                        $sheet->cell($pack_number_column.$number, function ($cells) {
                            $cells->setAlignment('center');
                            $cells->setValignment('center');
                        });
                        $sheet->cell($delivery_destination_start_column.$number, function ($cells) {
                            $cells->setAlignment('center');
                            $cells->setValignment('center');
                        });
                        $sheet->cell($over_and_under_number_column.$number, function ($cells) {
                            $cells->setAlignment('center');
                            $cells->setValignment('center');
                        });
                    }

                    $sheet->setBorder(
                        $weight_column.$fixed_row_number['product_header_first_row'].':'.
                        $weight_column.$fixed_row_number['product_multi_data_row'],
                        'thin'
                    );

                    $incount = 0;
                    $before_delivery_destination_column_num = 2;

                    // 一つの納入工場商品列分繰り返し
                    for ($i = $delivery_factory_product_start_at; $i < $next_delivery_factory_product_start_at; $i++) {
                        // パック数から在庫数までを縦文字化
                        $sheet
                            ->getStyle(
                                get_excel_column_str($i).$fixed_row_number['delivery_destination_name_row']
                            )
                            ->getAlignment()
                            ->setTextRotation(255);

                        $set_cell_value = $incount < $before_delivery_destination_column_num ?
                            $before_delivery_destination_value[$incount] :
                            '';

                        // 納入先範囲内であれば真
                        $delivery_flag = $before_delivery_destination_column_num <= $incount &&
                            $incount < $before_delivery_destination_column_num + $delivery_destination_count;

                        if ($delivery_flag) {
                            $delivery_destination_abbreviation =
                                $delivery_destination_abbreviations[$incount - $before_delivery_destination_column_num];
                            // 納入先略称のうち、半角カナのものを全角カナに変換
                            $set_cell_value = implode("\n", explode(
                                ' ',
                                mb_convert_kana($delivery_destination_abbreviation, 'KVs')
                            ));

                            $sheet->cells(
                                get_excel_column_str($i).$fixed_row_number['delivery_destination_name_row'].':'.
                                get_excel_column_str($i).$fixed_row_number['end_row'],
                                function ($cells) use ($i, $next_delivery_factory_product_start_at) {
                                    $cells->setBorder(['right' => [
                                        'style' => ($i !== ($next_delivery_factory_product_start_at - 5)) ?
                                            'dotted' :
                                            'thin'
                                    ]]);
                                }
                            );

                            $sheet->setSize([
                                get_excel_column_str($i).$fixed_row_number['delivery_destination_name_row'] => [
                                    'width' => 5
                                ]
                            ]);
                        } else {
                            $sheet->cells(
                                get_excel_column_str($i).$fixed_row_number['delivery_destination_name_row'].':'.
                                get_excel_column_str($i).$fixed_row_number['end_row'],
                                function ($cells) {
                                    $cells->setBorder(['right' => ['style' => 'thin']]);
                                }
                            );
                        }

                        $sheet->cells(
                            get_excel_column_str($i).$fixed_row_number['assist_space_row'],
                            function ($cells) {
                                $cells->setBorder(['top' => ['style' => 'thin']]);
                            }
                        );

                        if ($set_cell_value == '') {
                            $set_cell_value = $after_delivery_destination_value
                                [$delivery_factory_product_start_at + $delivery_destination_count + 6 - $i];
                        }

                        $sheet->setCellValue(
                            get_excel_column_str($i).$fixed_row_number['delivery_destination_name_row'],
                            $set_cell_value
                        );

                        $sheet->cells(
                            get_excel_column_str($i).$fixed_row_number['delivery_destination_name_row'],
                            function ($cells) {
                                $cells->setAlignment('center');
                                $cells->setValignment('center');
                            }
                        );

                        $sheet->getStyle(
                            get_excel_column_str($i).$fixed_row_number['delivery_destination_name_row']
                        )
                            ->getAlignment()
                            ->setWrapText(true);

                        $cell = get_excel_column_str($i).$fixed_row_number['assist_space_row'];
                        if ($set_cell_value === '在庫数') {
                            $sheet->setCellValue($cell, $packaging_style->carry_over_stock);
                            $sheet->setColumnFormat([$cell => $column_format_list['integer']]);
                            $sheet->cells($cell, function ($cells) {
                                $cells->setAlignment('right');
                                $cells->setValignment('center');
                            });

                            $carry_over_stocks[] = $pack_number_column.$number.'*'.$cell;
                        } else {
                            $sheet->cells($cell, function ($cells) {
                                $cells->setAlignment('center');
                                $cells->setValignment('center');
                            });
                        }

                        $incount++;
                    }
                }

                // 合計欄の見出し
                $sum_value = $config['visible_sheet']['sum_value'];

                // 合計欄を出力
                $sum_count = 0;
                $format_end_cell = $next_delivery_factory_product_start_at + 6;
                for ($i = $next_delivery_factory_product_start_at; $i <= $format_end_cell; $i++) {
                    $sheet->mergeCells(
                        get_excel_column_str($i).$fixed_row_number['product_weight_row'].':'.
                        get_excel_column_str($i).$fixed_row_number['delivery_destination_name_row']
                    );

                    $sheet->getStyle(get_excel_column_str($i).$fixed_row_number['product_weight_row'])
                        ->getAlignment()
                        ->setTextRotation(255);

                    if ($sum_value[$sum_count] === '') {
                        $sheet->setSize([
                            get_excel_column_str($i).$fixed_row_number['product_weight_row'] => ['width' => 2]
                        ]);

                        $sum_count++;
                        continue;
                    }

                    $sheet->setCellValue(
                        get_excel_column_str($i).$fixed_row_number['product_weight_row'],
                        $sum_value[$sum_count]
                    );

                    if ($sum_value[$sum_count] === $sum_value[3]) {
                        $cell = get_excel_column_str($i).$fixed_row_number['assist_space_row'];
                        $sheet->setColumnFormat([$cell => $column_format_list['integer']]);
                        $sheet->setCellValue($cell, '='.implode('+', $carry_over_stocks));
                    }

                    $sheet->setBorder(
                        get_excel_column_str($i).$fixed_row_number['product_weight_row'].':'.
                            get_excel_column_str($i).$fixed_row_number['end_row'],
                        'thin'
                    );

                    $sheet->cells(
                        get_excel_column_str($i).$fixed_row_number['product_weight_row'],
                        function ($cells) {
                            $cells->setAlignment('center');
                            $cells->setValignment('center');
                        }
                    );

                    $sum_count++;
                }

                // 上部のレイアウト及び調整
                $sheet->cells(
                    'A2:'.get_excel_column_str($i - 1).$fixed_row_number['title_row'],
                    function ($cells) {
                        $cells->setBorder('thin', 'thin', 'thin', 'thin');
                        $cells->setAlignment('center');
                    }
                );

                // データ出力部分レイアウト作成
                $row_count = 0;
                foreach ($params['visible_data_list']['date_list']['date'] as $date) {
                    $beforeline = 0;
                    $this->doubleLine(
                        $sheet,
                        $params['visible_data_list']['packaging_styles'],
                        $row_count,
                        $beforeline,
                        $sum_line,
                        $sum_end,
                        $date['date']->isMonday()
                    );

                    $row_count = $row_count + 2;

                    if ($date['date']->isSunday()) {
                        $this->sumWeek(
                            $sheet,
                            $params['visible_data_list']['packaging_styles'],
                            $sum_line,
                            $sum_end
                        );

                        $row_count = $row_count + 1;
                    }
                }

                $sheet->setColumnFormat([
                    'C9:C68' => $column_format_list['integer'],
                    'D9:D68' => $column_format_list['integer'],
                    'E9:E68' => $column_format_list['percentage'],
                    'F9:F68' => $column_format_list['integer'],
                    'F9:F68' => $column_format_list['integer'],
                    'G9:G68' => $column_format_list['integer'],
                    'H9:H68' => $column_format_list['integer'],
                    'K9:'.get_excel_column_str($format_end_cell).$fixed_row_number['end_row'] =>
                        $column_format_list['integer']
                ]);
                $sheet->cells(
                    'K9:'.get_excel_column_str($format_end_cell).$fixed_row_number['end_row'],
                    function ($cells) {
                        $cells->setAlignment('right');
                        $cells->setValignment('center');
                    }
                );

                // 入力可能セル（非プロテクトセル）背景色
                $not_protect_cell_background_color = $config['visible_sheet']['not_protect_cell_background_color'];
                $pack_number_column_list = $params['visible_data_list']['date_list']['pack_number_column_list'];
                foreach ($params['visible_data_list']['date_list']['date'] as $date) {
                    $sheet->setCellValue($date['crop_failure_cell'], $date['crop_failure']);
                    $sheet->setCellValue($date['advanced_harvest_cell'], $date['advanced_harvest']);

                    if ($date['can_import']) {
                        // 保護解除 & 背景色付与
                        $sheet->getStyle($date['crop_failure_cell'])
                            ->getProtection()
                            ->setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);
                        $sheet->cells(
                            $date['crop_failure_cell'],
                            function ($cells) use ($not_protect_cell_background_color) {
                                $cells->setBackground($not_protect_cell_background_color);
                            }
                        );

                        $sheet->getStyle($date['advanced_harvest_cell'])
                            ->getProtection()
                            ->setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);
                        $sheet->cells(
                            $date['advanced_harvest_cell'],
                            function ($cells) use ($not_protect_cell_background_color) {
                                $cells->setBackground($not_protect_cell_background_color);
                            }
                        );

                        if ($date['date']->isWorkingDay($factory) ||
                            $date['crop_failure'] !== 0 ||
                            $date['advanced_harvest'] !== 0) {
                            $sheet->getStyle('E'.$date['set_row_num'])
                                ->getProtection()
                                ->setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);

                            $sheet->cells(
                                'E'.$date['set_row_num'],
                                function ($cells) use ($not_protect_cell_background_color) {
                                    $cells->setBackground($not_protect_cell_background_color);
                                }
                            );

                            foreach ($pack_number_column_list as $pack_number_column) {
                                $sheet->getStyle($pack_number_column.$date['set_row_num'])
                                    ->getProtection()
                                    ->setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);

                                $sheet->cells(
                                    $pack_number_column.$date['set_row_num'],
                                    function ($cells) use ($not_protect_cell_background_color) {
                                        $cells->setBackground($not_protect_cell_background_color);
                                    }
                                );
                            }
                        }
                    }
                }
            });

            // 取込用隠し予想製品化率表
            $sheet_title = $config['hidden_sheet']['sheet_title1'];
            $excel->sheet($sheet_title, function ($sheet) use ($factory, $species, $params, $config) {
                $sheet->loadView('plan.growth_sale_management.template_hidden')
                    ->with('config', $config)
                    ->with('factory', $factory)
                    ->with('species', $species)
                    ->with('forecasted_product_rates', $params['hidden_data_list']['forecasted_product_rates']);

                $sheet->setSheetState(PHPExcel_Worksheet::SHEETSTATE_HIDDEN);
                $sheet->setAutoSize(false);
                $sheet->getDefaultColumnDimension()->setWidth(11);
                $sheet->getDefaultRowDimension()->setRowHeight(19);
            });

            // 取込用隠し出来高表
            $sheet_title = $config['hidden_sheet']['sheet_title2'];
            $excel->sheet($sheet_title, function ($sheet) use ($factory, $species, $params, $config) {
                $sheet->setSheetState(PHPExcel_Worksheet::SHEETSTATE_HIDDEN);

                $sheet->loadView('plan.growth_sale_management.template_hidden2')
                    ->with('config', $config)
                    ->with('factory', $factory)
                    ->with('species', $species)
                    ->with('crops', $params['hidden_data_list']['crops']);

                $sheet->setAutoSize(false);
                $sheet->getDefaultColumnDimension()->setWidth(11);
                $sheet->getDefaultRowDimension()->setRowHeight(19);
            });
        })
            ->export();
    }

    /**
     * ２段組レイアウトの描画
     *
     * @return void
     */
    private function doubleLine($sheet, $packaging_styles, $row_count, &$beforeline, &$sum_line, &$sum_end, $is_monday)
    {
        $config = config('settings.data_link.plan.growth_sale_management.visible_sheet');

        // 固定行番号
        $fixed_row_number = $config['fixed_row_number'];
        // 変動部開始行
        $default_start_row = $config['before_delivery_factory_product_datas_output_row'];

        // 主項目のレイアウト
        foreach ($config['merge_cell_pattern_defalut'] as $column) {
            $set_start = $default_start_row + $row_count;
            $set_end = $default_start_row + 1 + $row_count;
            $sheet->mergeCells($column.$set_start.':'.$column.$set_end);
            $sheet->cells($column.$set_start.':'.$column.$set_end, function ($cells) {
                $cells->setValignment('center');
                $cells->setBorder('thin', 'thin', 'thin', 'thin');
            });

            // 使用可能株数
            $sheet->setCellValue('F'.$set_start, '=D'.$set_start.'*E'.$set_start);
            // 差引株数
            $sheet->setCellValue('H'.$set_start, '=F'.$set_start.'-G'.$set_start);
        }

        // 各商品毎のデータ欄のレイアウト
        foreach ($packaging_styles as $ps) {
            $setline_number = $config['pack_number_set_num'];
            if ($beforeline !== 0) {
                $setline_number = $beforeline;
            }

            // 使用株数算出式
            $set_cell_value = get_excel_column_str($setline_number).$set_start.'*'.
                get_excel_column_str($setline_number).$fixed_row_number['product_multi_data_row'];

            // 出来高パック数
            $sheet->mergeCells(
                get_excel_column_str($setline_number).$set_start.':'.
                get_excel_column_str($setline_number).$set_end
            );
            $this->writeThin($sheet, get_excel_column_str($setline_number).$set_start);

            // 使用株数
            $sheet->mergeCells(
                get_excel_column_str($setline_number + 1).$set_start.':'.
                get_excel_column_str($setline_number + 1).$set_end
            );
            $sheet->setCellValue(
                get_excel_column_str($setline_number + 1).$set_start,
                '='.$set_cell_value
            );
            $this->writeThin(
                $sheet,
                get_excel_column_str($setline_number + 1).$set_start
            );

            // 納入先毎の項目表示
            $idx = 0;
            $set_delivery_line = $setline_number + 1 + $idx + 1;
            $delivery_destinations = [];

            foreach ($ps->factory_products as $fp) {
                foreach ($fp->delivery_destinations as $dd) {
                    $set_delivery_line = $setline_number + 1 + $idx + 1;
                    if ($fp->number_of_cases === 1) {
                        $sheet->mergeCells(
                            get_excel_column_str($set_delivery_line).$set_start.':'.
                            get_excel_column_str($set_delivery_line).$set_end
                        );
                        $sheet->cells(get_excel_column_str($set_delivery_line).$set_start, function ($cells) {
                            $cells->setAlignment('right');
                            $cells->setValignment('center');
                            $cells->setBorder('thin', 'dotted', 'thin', 'dotted');
                        });
                    } else {
                        $this->writeDottedDown($sheet, get_excel_column_str($set_delivery_line).$set_start);
                        $this->writeDottedUp($sheet, get_excel_column_str($set_delivery_line).$set_end);

                        $set_cell_value = get_excel_column_str($set_delivery_line).$set_start.'*'.
                            get_excel_column_str($set_delivery_line).
                            $fixed_row_number['product_multi_data_row'];
                        $sheet->setCellValue(
                            get_excel_column_str($set_delivery_line).$set_end,
                            '='.$set_cell_value
                        );
                    }

                    $idx = $idx + 1;

                    $dd->number_of_cases = $fp->number_of_cases;
                    $delivery_destinations[$set_delivery_line] = $dd;
                }
            }

            foreach ($config['merge_cell_pattern_1']['number_set1'] as $key => $value) {
                $sheet->mergeCells(
                    get_excel_column_str($set_delivery_line + $value).$set_start.':'.
                    get_excel_column_str($set_delivery_line + $value).$set_end
                );

                $this->writeThin(
                    $sheet,
                    get_excel_column_str($set_delivery_line + $value).$set_start.':'.
                    get_excel_column_str($set_delivery_line + $value).$set_end
                );

                // 小計
                if ($key === 'subtotal') {
                    $sub_total[] = get_excel_column_str($set_delivery_line + $value).$set_start.'+'.
                        get_excel_column_str($set_delivery_line + $value + 3).$set_start;
                    $target_columns = [];
                    foreach (range($setline_number + 2, $set_delivery_line) as $idx) {
                        $target_columns[] = $delivery_destinations[$idx]->number_of_cases === 1 ?
                            get_excel_column_str($idx).$set_start :
                            get_excel_column_str($idx).$set_end;
                    }
                    $sheet->setCellValue(
                        get_excel_column_str($set_delivery_line + $value).$set_start,
                        '='.implode('+', $target_columns)
                    );
                // 重量
                } elseif ($key === 'weight') {
                    $weight[] = get_excel_column_str($set_delivery_line + $value).$set_start;
                    $set_cell_value = get_excel_column_str($setline_number).
                        $fixed_row_number['product_weight_row'].
                        '*'.get_excel_column_str($set_delivery_line + $value - 1).$set_start.'/1000';
                    $sheet->setCellValue(
                        get_excel_column_str($set_delivery_line + $value).$set_start,
                        '='.$set_cell_value
                    );
                // 過不足数
                } elseif ($key === 'over_and_under_num') {
                    $set_cell_value = get_excel_column_str($setline_number).$set_start.'-'.
                        get_excel_column_str($set_delivery_line + $value - 2).$set_start;
                    $sheet->setCellValue(
                        get_excel_column_str($set_delivery_line + $value).$set_start,
                        '='.$set_cell_value
                    );
                // 在庫数
                } elseif ($key === 'stock_num') {
                    $slide = $is_monday ? 1 : 2;
                    $set_cell_value = get_excel_column_str($set_delivery_line + $value).($set_start - $slide).
                        '+'.get_excel_column_str($setline_number).$set_start.
                        '-'.get_excel_column_str($set_delivery_line + $value - 4).$set_start.
                        '-'.get_excel_column_str($set_delivery_line + $value - 1).$set_start;
                    $sheet->setCellValue(
                        get_excel_column_str($set_delivery_line + $value).$set_start,
                        '='.$set_cell_value
                    );
                }
            }

            $calculations[] = get_excel_column_str($setline_number);
            $beforeline = $set_delivery_line + 6;
        }

        // 合計欄のレイアウト
        foreach ($config['merge_cell_pattern_1']['number_set3'] as $key => $value) {
            // 合計使用株数
            if ($key == 'total_use_shares') {
                $cells = [];
                foreach ($calculations as $key => $calculation) {
                    $cells[] = $calculation.$fixed_row_number['product_multi_data_row'].'*('.$sub_total[$key].')';
                }

                $sheet->setCellValue(
                    get_excel_column_str($set_delivery_line + $value).$set_start,
                    '='.implode('+', $cells)
                );
            // 合計重量
            } elseif ($key == 'total_weight') {
                $sheet->setCellValue(
                    get_excel_column_str($set_delivery_line + $value).$set_start,
                    '='.implode('+', $weight)
                );
            // 差引過不足株数
            } elseif ($key == 'deduction_over_and_under_shares') {
                $set_cell_value = get_excel_column_str(6).$set_start.'-'.
                    get_excel_column_str($set_delivery_line + $value - 2).$set_start;
                $sheet->setCellValue(
                    get_excel_column_str($set_delivery_line + $value).$set_start,
                    '='.$set_cell_value
                );
            // 累積過不足株数
            } elseif ($key == 'cumulative_over_and_under_shares') {
                $slide = $is_monday ? 1 : 2;
                $set_cell_value = get_excel_column_str($set_delivery_line + $value - 1).$set_start.
                    '+'.get_excel_column_str($set_delivery_line + $value).($set_start - $slide);
                $sheet->setCellValue(
                    get_excel_column_str($set_delivery_line + $value).$set_start,
                    '='.$set_cell_value
                );
            }

            $sheet->mergeCells(
                get_excel_column_str($set_delivery_line + $value).$set_start.':'.
                get_excel_column_str($set_delivery_line + $value).$set_end
            );

            $this->writeThin(
                $sheet,
                get_excel_column_str($set_delivery_line + $value).$set_start.':'.
                get_excel_column_str($set_delivery_line + $value).$set_end
            );
        }

        $sum_line = $set_delivery_line + $value + 1;
        $sum_end = $set_end;
    }

    /**
     * 週合計を描画
     *
     * @return void
     */
    private function sumWeek($sheet, $packaging_styles, $sum_line, $sum_end)
    {
        $config = config('settings.data_link.plan.growth_sale_management.visible_sheet');

        $start_row = $config['merge_cell_pattern_1']['sum_week_point']['start_row'];
        $end_row = $config['merge_cell_pattern_1']['sum_week_point']['end_row'];
        $sum_week_background_color = $config['sum_week_background_color'];

        $set_row = $sum_end + 1;
        $sheet->mergeCells('A'.$set_row.':'.'B'.$set_row);
        $sheet->setCellValue('A'.$set_row, '週間合計');
        $sheet->cells('A'.$set_row, function ($cells) {
            $cells->setAlignment('center');
            $cells->setValignment('center');
            $cells->setBorder('thin', 'thin', 'thin', 'thin');
        });

        for ($i = 3; $i < 10; $i++) {
            $this->writeThin(
                $sheet,
                get_excel_column_str($i).$set_row.':'.get_excel_column_str($i).$set_row
            );

            $set_cell_value = get_excel_column_str($i).($sum_end - ($end_row - $start_row)).':'.
                get_excel_column_str($i).$sum_end;
            if ($i !== 9 && $i !== 10) {
                if ($i === 5) {
                    $sheet->setCellValue(
                        get_excel_column_str($i).($set_row),
                        '=IFERROR(AVERAGEIF('.$set_cell_value.', ">0"), 0)'
                    );
                } else {
                    $sheet->setCellValue(get_excel_column_str($i).($set_row), '=SUM('.$set_cell_value.')');
                }
            }
        }

        foreach ($packaging_styles as $ps) {
            foreach (range(1, 2) as $j) {
                $i += 1;
                $set_cell_value = get_excel_column_str($i).($sum_end - ($end_row - $start_row)).':'.
                    get_excel_column_str($i).$sum_end;
                $sheet->setCellValue(get_excel_column_str($i).($set_row), '=SUM('.$set_cell_value.')');
                $sheet->cells(get_excel_column_str($i).($set_row), function ($cells) {
                    $cells->setBorder([
                        'top' => ['style' => 'thin'],
                        'bottom' => ['style' => 'thin'],
                    ]);
                });
            }

            foreach ($ps->factory_products as $fp) {
                foreach ($fp->delivery_destinations as $dd) {
                    $i += 1;
                    $orders = array_map(function ($num) use ($i, $set_row, $fp) {
                        $is_multi_column = $fp->number_of_cases !== 1;
                        $target_row = $set_row - $num * 2;
                        if ($is_multi_column) {
                            $target_row += 1;
                        }

                        return get_excel_column_str($i).$target_row;
                    }, range(1, HarvestingDate::DAYS_PER_WEEK));

                    $sheet->setCellValue(get_excel_column_str($i).($set_row), '='.implode('+', $orders));
                    $sheet->cells(get_excel_column_str($i).($set_row), function ($cells) {
                        $cells->setBorder([
                            'top' => ['style' => 'thin'],
                            'bottom' => ['style' => 'thin'],
                        ]);
                    });
                }
            }

            foreach (range(1, 2) as $j) {
                $i += 1;
                $set_cell_value = get_excel_column_str($i).($sum_end - ($end_row - $start_row)).':'.
                    get_excel_column_str($i).$sum_end;
                $sheet->setCellValue(get_excel_column_str($i).($set_row), '=SUM('.$set_cell_value.')');
                $sheet->cells(get_excel_column_str($i).($set_row), function ($cells) {
                    $cells->setBorder([
                        'top' => ['style' => 'thin'],
                        'bottom' => ['style' => 'thin'],
                    ]);
                });
            }

            $i += 1;
            $sheet->setCellValue(get_excel_column_str($i).($set_row), '廃棄');
            $sheet->cells(get_excel_column_str($i).($set_row), function ($cells) {
                $cells->setBorder([
                    'top' => ['style' => 'thin'],
                    'bottom' => ['style' => 'thin'],
                ]);

                $cells->setAlignment('center');
                $cells->setValignment('center');
            });

            $i += 1;
            $set_cell_value = get_excel_column_str($i).($sum_end - ($end_row - $start_row)).':'.
                get_excel_column_str($i).$sum_end;
            $sheet->setCellValue(get_excel_column_str($i).($set_row), '=SUM('.$set_cell_value.')');
            $this->writeThin(
                $sheet,
                get_excel_column_str($i).$set_row.':'.get_excel_column_str($i).$set_row
            );

            $i += 1;
            $set_cell_value = get_excel_column_str($i).($set_row -2);
            $sheet->setCellValue(get_excel_column_str($i).($set_row), '='.$set_cell_value);
            $sheet->cells(get_excel_column_str($i).($set_row), function ($cells) {
                $cells->setBorder([
                    'top' => ['style' => 'thin'],
                    'bottom' => ['style' => 'thin'],
                ]);
            });
        }

        foreach (range(1, 2) as $j) {
            $i += 1;
            $set_cell_value = get_excel_column_str($i).($sum_end - ($end_row - $start_row)).':'.
                get_excel_column_str($i).$sum_end;
            $sheet->setCellValue(get_excel_column_str($i).($set_row), '=SUM('.$set_cell_value.')');
            $this->writeThin(
                $sheet,
                get_excel_column_str($i).$set_row.':'.get_excel_column_str($i).$set_row
            );
        }

        $i += 1;
        $sheet->setCellValue(get_excel_column_str($i).($set_row), '廃棄');
        $sheet->cells(get_excel_column_str($i).($set_row), function ($cells) {
            $cells->setAlignment('center');
            $cells->setValignment('center');
        });
        $this->writeThin(
            $sheet,
            get_excel_column_str($i).$set_row.':'.get_excel_column_str($i).$set_row
        );

        $i += 1;
        $set_cell_value = get_excel_column_str($i).($set_row - 2);
        $sheet->setCellValue(get_excel_column_str($i).($set_row), '='.$set_cell_value);
        $this->writeThin(
            $sheet,
            get_excel_column_str($i).$set_row.':'.get_excel_column_str($i).$set_row
        );

        $i += 1;
        foreach (range(1, 2) as $j) {
            $i += 1;
            $set_cell_value = get_excel_column_str($i).($sum_end - ($end_row - $start_row)).':'.
                get_excel_column_str($i).$sum_end;
            $sheet->setCellValue(get_excel_column_str($i).($set_row), '=SUM('.$set_cell_value.')');
            $this->writeThin(
                $sheet,
                get_excel_column_str($i).$set_row.':'.get_excel_column_str($i).$set_row
            );
        }

        // 週間合計背景色付与
        $sheet->cell(
            'A'.$set_row.':'.get_excel_column_str($sum_line - 1).$set_row,
            function ($cells) use ($sum_week_background_color) {
                $cells->setBackground($sum_week_background_color);
            }
        );
    }

    /**
     * 周囲に細い罫線を描画
     *
     * @param  Maatwebsite\Excel\Classes\LaravelExcelWorksheet $sheet
     * @param  string $cells
     * @return void
     */
    private static function writeThin(LaravelExcelWorksheet $sheet, string $cells)
    {
        $sheet->cells($cells, function ($cells) {
            $cells->setAlignment('right');
            $cells->setValignment('center');
            $cells->setBorder('thin', 'thin', 'thin', 'thin');
        });
    }

    /**
     * 上部に点線を描画
     *
     * @param  Maatwebsite\Excel\Classes\LaravelExcelWorksheet $sheet
     * @param  string $cells
     * @return void
     */
    private static function writeDottedUp(LaravelExcelWorksheet $sheet, string $cells)
    {
        $sheet->cells($cells, function ($cells) {
            $cells->setAlignment('right');
            $cells->setValignment('center');
            $cells->setBorder('dotted', 'dotted', 'thin', 'dotted');
        });
    }

    /**
     * 下部に点線を描画
     *
     * @param  Maatwebsite\Excel\Classes\LaravelExcelWorksheet $sheet
     * @param  string $cells
     * @return void
     */
    private static function writeDottedDown(LaravelExcelWorksheet $sheet, string $cells)
    {
        $sheet->cells($cells, function ($cells) {
            $cells->setAlignment('right');
            $cells->setValignment('center');
            $cells->setBorder('thin', 'dotted', 'dotted', 'dotted');
        });
    }

    /**
     * アップロードされたファイルの確認
     *
     * @param  array $params
     * @return bool
     */
    public function checkUploadedFile(array $params): bool
    {
        $reader = Excel::load($params['import_file']->getRealPath());
        $reader->noHeading();

        $config = config('settings.data_link.plan.growth_sale_management.hidden_sheet');
        return ! is_null($reader->getSheetByName($config['sheet_title1'])) && ! is_null($config['sheet_title2']);
    }

    /**
     * アップロードされたデータの整理
     *
     * @param  array array $params
     * @return array [array $forecasted_product_rates, array $crops]
     */
    public function parseUploadedFile(array $params): array
    {
        $config = config('settings.data_link.plan.growth_sale_management');

        $reader = Excel::load($params['import_file']->getRealPath());
        $reader->noHeading();

        $sheet = $reader->getSheetByName($config['hidden_sheet']['sheet_title1']);
        $factory_code = (string)($sheet->getCell($config['import_growth_sale']['factory_code_cell'])->getValue());
        $species_code = (string)($sheet->getCell($config['import_growth_sale']['species_code_cell'])->getValue());

        // 固定行の削除
        $sheet->removeRow(
            $config['import_growth_sale']['remove_fix_row_range']['start_row'],
            $config['import_growth_sale']['remove_fix_row_range']['end_row']
        );

        // 入力データの整形
        $forecasted_product_rates = [];
        foreach ($sheet->toArray() as $idx => $values) {
            if ($idx === 0) {
                continue;
            }

            $fpr = compact('factory_code', 'species_code') +
                array_combine($config['import_growth_sale']['import_key_list'], $values);
            if ($fpr['current_product_rate'] !== null) {
                $fpr['current_product_rate'] = round($fpr['current_product_rate'], 2);
            }
            if (is_string($fpr['product_rate'])) {
                $fpr['product_rate'] = null;
            }
            if (! is_null($fpr['product_rate'])) {
                $fpr['product_rate'] = number_format($fpr['product_rate'], 2);
            }

            if (is_string($fpr['crop_failure'])) {
                $fpr['crop_failure'] = null;
            }
            if (! is_null($fpr['crop_failure'])) {
                $fpr['crop_failure'] = (int)round($fpr['crop_failure']);
            }

            if (is_string($fpr['advanced_harvest'])) {
                $fpr['advanced_harvest'] = null;
            }
            if (! is_null($fpr['advanced_harvest'])) {
                $fpr['advanced_harvest'] = (int)round($fpr['advanced_harvest']);
            }

            if ($fpr['current_product_rate'] == $fpr['product_rate'] &&
                $fpr['current_crop_failure'] === $fpr['crop_failure'] &&
                $fpr['current_advanced_harvest'] === $fpr['advanced_harvest']) {
                continue;
            }

            $forecasted_product_rates[] = $fpr;
        }

        $sheet = $reader->getSheetByName($config['hidden_sheet']['sheet_title2']);
        $factory_code = (string)($sheet->getCell($config['import_growth_sale']['factory_code_cell'])->getValue());
        $species_code = (string)($sheet->getCell($config['import_growth_sale']['species_code_cell'])->getValue());

        // 固定行の削除
        $sheet->removeRow(
            $config['import_growth_sale']['remove_fix_row_range']['start_row'],
            $config['import_growth_sale']['remove_fix_row_range']['end_row']
        );

        // 入力データの整形
        $crops = [];
        foreach ($sheet->toArray() as $idx => $values) {
            if ($idx === 0) {
                continue;
            }

            $crop = compact('factory_code', 'species_code') +
                array_combine($config['import_growth_sale']['import_key_list_crop'], $values);

            $crop['weight_per_number_of_heads'] = (int)round($crop['weight_per_number_of_heads']);
            if (! is_null($crop['current_crop_number'])) {
                $crop['current_crop_number'] = (int)round($crop['current_crop_number']);
            }
            if (is_string($crop['crop_number'])) {
                $crop['crop_number'] = null;
            }
            if (! is_null($crop['crop_number'])) {
                $crop['crop_number'] = (int)round($crop['crop_number']);
            }

            if ($crop['current_crop_number'] === $crop['crop_number']) {
                continue;
            }

            $crop['crop_stock_number'] = $crop['number_of_heads'] * $crop['crop_number'];
            $crop['product_weight'] = $crop['crop_number'] * $crop['weight_per_number_of_heads'];
            $crop['product_rate'] = 0;

            $crops[] = $crop;
        }

        return [$forecasted_product_rates, $crops];
    }

    /**
     * アップロードされたファイルの入力データを取込
     *
     * @param  array $forecasted_product_rates
     * @param  array $crops
     * @return array
     */
    public function importUploadedData(array $forecasted_product_rates, array $crops): array
    {
        return $this->db->transaction(function () use ($forecasted_product_rates, $crops) {
            $config = config('settings.data_link.plan.growth_sale_management.import_growth_sale');

            $messages = [];
            foreach ($forecasted_product_rates as $fpr) {
                $skipped = false;

                $existed = $this->forecasted_product_rate_repo->getForecastedProductRate($fpr);
                if (! is_null($existed) && ! (bool)$existed->can_import) {
                    continue;
                }

                if (is_null($fpr['updated_at'])) {
                    if (is_null($existed) && $fpr['product_rate']) {
                        $this->forecasted_product_rate_repo->create($fpr);
                    }
                    if (! is_null($existed)) {
                        $skipped = true;
                    }
                }
                if (! is_null($fpr['updated_at']) && ! is_null($existed)) {
                    $updated_at = $existed->updated_at->format('Y-m-d H:i:s');
                    if ($fpr['updated_at'] === $updated_at) {
                        if (! is_null($fpr['product_rate'])) {
                            $this->forecasted_product_rate_repo->update($existed, $fpr);
                        }
                        if (is_null($fpr['product_rate'])) {
                            $existed->delete();
                        }
                    }
                    if ($fpr['updated_at'] !== $updated_at) {
                        $skipped = true;
                    }
                }
                if (! is_null($fpr['updated_at']) && is_null($existed)) {
                    $skipped = true;
                }

                if ($skipped) {
                    $messages[] = sprintf(
                        $config['exclusion_product_rate_message'],
                        $fpr['factory_code'],
                        $fpr['species_code'],
                        $fpr['date']
                    );
                }
            }

            foreach ($crops as $c) {
                $skipped = false;

                $existed = $this->crop_repo->getCrop($c);
                if (! is_null($existed) && ! (bool)$existed->can_import) {
                    continue;
                }

                if (is_null($c['updated_at'])) {
                    if (is_null($existed) && $c['crop_number']) {
                        $this->crop_repo->create($c);
                    }
                    if (! is_null($existed)) {
                        $skipped = true;
                    }
                }
                if (! is_null($c['updated_at']) && ! is_null($existed)) {
                    $updated_at = $existed->updated_at->format('Y-m-d H:i:s');
                    if ($c['updated_at'] === $updated_at) {
                        if (! is_null($c['crop_number'])) {
                            $this->crop_repo->update($existed, $c);
                        }
                        if (is_null($c['crop_number'])) {
                            $existed->delete();
                        }
                    }
                    if ($c['updated_at'] !== $updated_at) {
                        $skipped = true;
                    }
                }
                if (! is_null($c['updated_at']) && is_null($existed)) {
                    $skipped = true;
                }

                if ($skipped) {
                    $messages[] = sprintf(
                        $config['exclusion_crop_message'],
                        $c['factory_code'],
                        $c['species_code'],
                        $c['number_of_heads'],
                        $c['weight_per_number_of_heads'],
                        $c['input_group'],
                        $c['date']
                    );
                }
            }

            return $messages;
        });
    }
}

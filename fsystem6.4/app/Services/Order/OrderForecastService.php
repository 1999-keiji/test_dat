<?php

declare(strict_types=1);

namespace App\Services\Order;

use PHPExcel_Style_Border;
use PHPExcel_Style_Protection;
use PHPExcel_Worksheet;
use Illuminate\Database\Connection;
use Maatwebsite\Excel\Excel;
use App\Models\Master\Factory;
use App\Models\Master\Species;
use App\Models\Order\Collections\OrderCollection;
use App\Models\Order\Collections\OrderForecastCollection;
use App\Repositories\Order\OrderForecastRepository;
use App\ValueObjects\Date\Date;
use App\ValueObjects\Date\DeliveryDate;
use App\ValueObjects\Date\HarvestingDate;
use App\ValueObjects\Enum\ShipmentLeadTime;
use App\ValueObjects\Integer\DeliveryLeadTime;

class OrderForecastService
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
     * @var \App\Repositories\Order\OrderForecastRepository
     */
    private $order_forecast_repo;

    /**
     * @param  \Illuminate\Database\Connection $db
     * @param  \Maatwebsite\Excel\Excel $excel
     * @param  \App\Repositories\Order\OrderForecastRepository $order_forecast_repo
     * @return void
     */
    public function __construct(Connection $db, Excel $excel, OrderForecastRepository $order_forecast_repo)
    {
        $this->db = $db;
        $this->excel = $excel;
        $this->order_forecast_repo = $order_forecast_repo;
    }

    /**
     * 指定された工場、品種、納入年月の受注フォーキャストの情報を取得
     *
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Master\Species $species
     * @param  \App\ValueObjects\Date\Date $date
     * @param  bool $only_one_month
     * @return \App\Models\Order\Collections\OrderForecastCollection
     */
    public function getOrderForecastsByFactoryAndSpecies(
        Factory $factory,
        Species $species,
        Date $date,
        ?bool $only_one_month = false
    ): OrderForecastCollection {
        return $this->order_forecast_repo
            ->getOrderForecastsByFactoryAndSpecies($factory, $species, $date, $only_one_month);
    }

    /**
     * 受注フォーキャストデータをファイル出力
     *
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Master\Species $species
     * @param  \App\ValueObjects\Date\HarvestingDate $harvesting_date
     * @param  array $summary
     * @param  \App\ValueObjects\Date\DeliveryDate $delivery_date
     * @param  array $factory_products
     * @param  \App\Models\Order\Collections\OrderCollection $orders
     */
    public function exportOrderForecasts(
        Factory $factory,
        Species $species,
        HarvestingDate $harvesting_date,
        array $summary,
        DeliveryDate $delivery_date,
        array $factory_products,
        OrderCollection $orders
    ) {
        $delivery_dates = $delivery_date->toListToExportOrderForecasts();
        $order_forecasts = $this->getOrderForecastsByFactoryAndSpecies($factory, $species, $delivery_date);

        $row_count = 0;
        foreach ($factory_products as $fp) {
            foreach ($fp->delivery_destinations as $dd) {
                $dd->delivery_dates = [];
                foreach ($delivery_dates as $delivery_date) {
                    $filtered = $orders->filterByFactoryProduct($fp)
                        ->filterByDeliveryDestination($dd)
                        ->filterByDeliveryDate($delivery_date);

                    $order_forecast = $order_forecasts->filterByFactoryProduct($fp)
                        ->filterByDeliveryDestination($dd)
                        ->filterByDeliveryDate($delivery_date)
                        ->first();

                    $order_quantity = $filtered->isNotEmpty() ? ($filtered->pluck('order_quantity')->sum() ?: '') : '';
                    $dd->delivery_dates[] = [
                        'date' => $delivery_date,
                        'forecast' => $order_forecast->forecast_number ?? '',
                        'order' => $order_quantity,
                        'updated_at' => ! is_null($order_forecast) ?
                            $order_forecast->updated_at->format('Y-m-d H:i:s') :
                            '',
                        'can_import' => ! $delivery_date->willDisplayOrderOnTheDate($dd->latest_delivery_date)
                    ];
                }

                $row_count = $row_count + 1;
            }
        }

        $config = config('settings.order.order_forecasts');
        $file_name = generate_file_name($config['visible_forecast']['forecast_file_name'], [
            $factory->factory_abbreviation,
            $species->species_name,
            implode('～', [head($delivery_dates)->format('Ymd'), last($delivery_dates)->format('Ymd')])
        ]);

        $this->excel
            ->create($file_name, function ($excel) use (
                $factory,
                $species,
                $harvesting_date,
                $summary,
                $delivery_dates,
                $factory_products,
                $row_count,
                $config
            ) {
                $excel->sheet($config['visible_forecast']['sheet_name_number'], function ($sheet) use (
                    $factory,
                    $species,
                    $harvesting_date,
                    $summary,
                    $delivery_dates,
                    $factory_products,
                    $row_count,
                    $config
                ) {
                    $end_row = $row_count + $config['visible_forecast']['merge_start_row'] - 1;
                    $sheet->loadView('order.order_forecasts.template_visible_number')
                        ->with(compact(
                            'factory',
                            'species',
                            'harvesting_date',
                            'summary',
                            'delivery_dates',
                            'factory_products',
                            'config'
                        ));

                    $merge_start_row = $config['visible_forecast']['merge_start_row'];
                    foreach ($factory_products as $fp) {
                        $sheet->mergeCells(
                            'B'.$merge_start_row.
                            ':B'.($merge_start_row + count($fp->delivery_destinations) - 1)
                        );

                        $merge_start_row += count($fp->delivery_destinations);
                    }

                    $sheet->setBorder($config['visible_forecast']['header_table_range'], 'thin');
                    $sheet->cells($config['visible_forecast']['header_table_range'], function ($cells) {
                        $cells->setBorder([
                            'top' => ['style' => 'thick'],
                            'right' => ['style' => 'thick'],
                            'left' => ['style' => 'thick']
                        ]);
                    });

                    $sheet->setBorder(sprintf($config['visible_forecast']['body_table_range'], $end_row), 'thin');
                    $sheet->cells(
                        sprintf($config['visible_forecast']['body_table_range'], $end_row),
                        function ($cells) {
                            $cells->setBorder([
                                'right' => ['style' => 'thick'],
                                'bottom' => ['style' => 'thick'],
                                'left' => ['style' => 'thick']
                            ]);
                        }
                    );

                    $sheet->getColumnDimension(
                        get_excel_column_str($config['visible_forecast']['date_start_column'] - 1)
                    )
                        ->setVisible(false);

                    $sheet->protect($config['sheet_lock_pass']);
                    $sheet->setAutoSize(false);
                    $sheet->setFreeze($config['visible_forecast']['freeze_cell']);
                    $sheet->getDefaultColumnDimension()->setWidth($config['default_column_width']);
                    $sheet->getDefaultRowDimension()->setRowHeight($config['default_row_height']);
                    $sheet->setWidth($config['visible_forecast']['width_set_list']);
                    $sheet->setHeight($config['visible_forecast']['height_set_list']);

                    $row = $config['visible_forecast']['merge_start_row'];
                    foreach ($factory_products as $fp) {
                        foreach ($fp->delivery_destinations as $dd) {
                            foreach ($dd->delivery_dates as $idx => $delivery_date) {
                                if (! $delivery_date['can_import']) {
                                    continue;
                                }

                                $sheet->getStyle(
                                    get_excel_column_str($idx + $config['visible_forecast']['date_start_column']).$row
                                )
                                    ->getProtection()
                                    ->setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);
                            }

                            $row = $row + 1;
                        }
                    }
                });

                $excel->sheet($config['visible_forecast']['sheet_name_weight'], function ($sheet) use (
                    $factory,
                    $species,
                    $delivery_dates,
                    $factory_products,
                    $row_count,
                    $config
                ) {
                    $end_row = $row_count + $config['visible_forecast']['merge_start_row'] - 1;
                    $sheet->loadView('order.order_forecasts.template_visible_weight')
                        ->with(compact(
                            'factory',
                            'species',
                            'harvesting_date',
                            'summary',
                            'delivery_dates',
                            'factory_products',
                            'config',
                            'end_row'
                        ));

                    $merge_start_row = $config['visible_forecast']['merge_start_row'];
                    foreach ($factory_products as $fp) {
                        $sheet->mergeCells(
                            'B'.$merge_start_row.
                            ':B'.($merge_start_row + count($fp->delivery_destinations) - 1)
                        );

                        $merge_start_row += count($fp->delivery_destinations);
                    }

                    $sheet->setBorder($config['visible_forecast']['header_table_range'], 'thin');
                    $sheet->cells($config['visible_forecast']['header_table_range'], function ($cells) {
                        $cells->setBorder([
                            'top' => ['style' => 'thick'],
                            'right' => ['style' => 'thick'],
                            'left' => ['style' => 'thick']
                        ]);
                    });

                    $sheet->setBorder(sprintf($config['visible_forecast']['body_table_range'], $end_row), 'thin');
                    $sheet->cells(
                        sprintf($config['visible_forecast']['body_table_range'], $end_row),
                        function ($cells) {
                            $cells->setBorder([
                                'right' => ['style' => 'thick'],
                                'bottom' => ['style' => 'thick'],
                                'left' => ['style' => 'thick']
                            ]);
                        }
                    );

                    $sheet->getColumnDimension(
                        get_excel_column_str($config['visible_forecast']['date_start_column'] - 1)
                    )
                        ->setVisible(false);

                    $sheet->protect($config['sheet_lock_pass']);
                    $sheet->setAutoSize(false);
                    $sheet->setFreeze($config['visible_forecast']['freeze_cell']);
                    $sheet->getDefaultColumnDimension()->setWidth($config['default_column_width']);
                    $sheet->getDefaultRowDimension()->setRowHeight($config['default_row_height']);
                    $sheet->setWidth($config['visible_forecast']['width_set_list']);
                    $sheet->setHeight($config['visible_forecast']['height_set_list']);
                });

                $excel->sheet($config['hidden_forecast']['sheet_name'], function ($sheet) use (
                    $factory,
                    $delivery_dates,
                    $factory_products,
                    $config
                ) {
                    $sheet->setSheetState(PHPExcel_Worksheet::SHEETSTATE_HIDDEN);
                    $sheet->loadView('order.order_forecasts.template_hidden')
                        ->with(compact('factory', 'delivery_dates', 'factory_products', 'config'));

                    $sheet->setAutoSize(false);
                });

                $excel->setActiveSheetIndex(0);
            })
            ->export();
    }

    /**
     * アップロードされたファイルの確認
     *
     * @param  array $params
     * @return bool
     */
    public function checkUploadedFile(array $params): bool
    {
        $reader = $this->excel->load($params['import_file']->getRealPath());
        $reader->noHeading();

        $sheet_name = config('settings.order.order_forecasts.hidden_forecast.sheet_name');
        return ! is_null($reader->getSheetByName($sheet_name));
    }

    /**
     * アップロードされたデータの整理
     *
     * @param  array $params
     * @return array $order_forecasts
     */
    public function parseUploadedFile($params): array
    {
        $config = config('settings.order.order_forecasts');

        $reader = $this->excel->load($params['import_file']->getRealPath());
        $reader->noHeading();

        $sheet = $reader->getSheetByName($config['hidden_forecast']['sheet_name']);
        $factory_code = (string)($sheet->getCell($config['import_forecast']['factory_code_cell'])->getValue());

        $sheet->removeRow(
            $config['import_forecast']['remove_fix_row_range']['start_row'],
            $config['import_forecast']['remove_fix_row_range']['end_row']
        );

        $order_forecasts = [];
        foreach ($sheet->toArray() as $idx => $values) {
            if ($idx === 0) {
                continue;
            }

            $of = compact('factory_code') + array_combine($config['import_forecast']['import_key_list'], $values);
            if (! ((bool)$of['can_import'])) {
                continue;
            }

            $of['factory_product_sequence_number'] = (int)$of['factory_product_sequence_number'];
            if (! is_null($of['current_forecast_number'])) {
                $of['current_forecast_number'] = (int)round($of['current_forecast_number']);
            }
            if (! is_null($of['forecast_number'])) {
                $of['forecast_number'] = (int)round($of['forecast_number']);
            }

            if ($of['current_forecast_number'] === $of['forecast_number']) {
                continue;
            }

            $of['date'] = DeliveryDate::parse($of['date']);
            $order_forecasts[] = $of;
        }

        return $order_forecasts;
    }

    /**
     * アップロードされたファイルの入力データを取込
     *
     * @param  array $order_forecasts
     * @return array $messages
     */
    public function importUploadedData(array $order_forecasts): array
    {
        return $this->db->transaction(function () use ($order_forecasts) {
            $messages = [];
            foreach ($order_forecasts as $of) {
                $order_forecast = $this->order_forecast_repo->getOrderForecastWithDeliveryFactoryProduct($of);
                if (! is_null($order_forecast->latest_delivery_date)) {
                    $order_forecast->latest_delivery_date = DeliveryDate::parse($order_forecast->latest_delivery_date);
                }

                if ($of['date']->willDisplayOrderOnTheDate($order_forecast->latest_delivery_date)) {
                    continue;
                }

                $of['forecast_weight'] = $of['forecast_number'] *
                    $order_forecast->weight_per_number_of_heads *
                    $order_forecast->number_of_cases;

                $delivery_lead_time = $order_forecast->delivery_lead_time ?:
                    (new DeliveryLeadTime)->getDefaultDeliveryLeadTime()->value();
                $of['shipping_date'] = $of['date']->subDays($delivery_lead_time);

                $shipment_lead_time = $order_forecast->shipment_lead_time ?:
                    (new ShipmentLeadTime)->getDefaultShipmentLeadTime()->value();
                $of['harvesting_date'] = $of['shipping_date']->subDays($shipment_lead_time);

                $skipped = false;
                if (is_null($of['updated_at'])) {
                    if (is_null($order_forecast->updated_at)) {
                        $this->order_forecast_repo->create($of);
                    }
                    if (! is_null($order_forecast->updated_at)) {
                        $skipped = true;
                    }
                }

                if (! is_null($of['updated_at']) && ! is_null($order_forecast->updated_at)) {
                    $updated_at = $order_forecast->updated_at->format('Y-m-d H:i:s');
                    if ($of['updated_at'] === $updated_at) {
                        $this->order_forecast_repo->update($order_forecast, $of);
                    }
                    if ($of['updated_at'] !== $updated_at) {
                        $skipped = true;
                    }
                }

                if (! is_null($of['updated_at']) && is_null($order_forecast->updated_at)) {
                    $skipped = true;
                }

                if ($skipped) {
                    $messages[] = sprintf(
                        config('settings.order.order_forecasts.import_forecast.exclusion_message'),
                        $order_forecast->delivery_destination_abbreviation,
                        $order_forecast->factory_product_abbreviation,
                        $order_forecast->date
                    );
                }
            }

            return $messages;
        });
    }

    /**
     * 受注フォーキャストデータを納入先の情報とともに取得
     *
     * @param  array $params
     * @param  \App\ValueObjects\Date\HarvestingDate $harvesting_date
     * @return \App\Models\Order\Collections\OrderForecastCollection
     */
    public function getOrderForecastsBySpeciesAndHarvestingDate(
        array $params,
        HarvestingDate $harvesting_date
    ): OrderForecastCollection {
        $shipping_date_term = [];
        if ($params['display_term'] === 'date') {
            $harvesting_dates = $harvesting_date->toListOfDate((int)$params['week_term']);
            $shipping_date_term = [
                'from' => head($harvesting_dates)->getDefaultShippingDate(),
                'to' => last($harvesting_dates)->getDefaultShippingDate(),
            ];
        }
        if ($params['display_term'] === 'month') {
            $harvesting_months = $harvesting_date->toListOfMonth();
            $shipping_date_term = [
                'from' => head($harvesting_months)->firstOfMonth()->addDay(),
                'to' => last($harvesting_months)->lastOfMonth()->addDay()
            ];
        }

        return $this->order_forecast_repo->getOrderForecastsBySpeciesAndHarvestingDate($params, $shipping_date_term);
    }
}

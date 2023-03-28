<?php

declare(strict_types=1);

namespace App\Services\Order;

use PDOException;
use ZipArchive;
use Illuminate\Auth\AuthManager;
use Illuminate\Database\Connection;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Pagination\LengthAwarePaginator;
use Cake\Chronos\Chronos;
use Maatwebsite\Excel\Excel;
use App\Exceptions\OptimisticLockException;
use App\Exceptions\PageOverException;
use App\Models\Master\Customer;
use App\Models\Master\Factory;
use App\Models\Master\Species;
use App\Models\Master\Warehouse;
use App\Models\Order\Collections\OrderForecastCollection;
use App\Models\Order\Collections\OrderCollection;
use App\Models\Shipment\Invoice;
use App\Repositories\Master\EndUserRepository;
use App\Repositories\Master\TaxRepository;
use App\Repositories\Order\OrderRepository;
use App\Repositories\Order\OrderHistoryRepository;
use App\Repositories\Stock\CarryOverStockRepository;
use App\Repositories\Stock\StockRepository;
use App\ValueObjects\Date\Date;
use App\ValueObjects\Date\DeliveryDate;
use App\ValueObjects\Date\HarvestingDate;
use App\ValueObjects\Date\ShippingDate;
use App\ValueObjects\Enum\ProcessClass;

class OrderService
{
    /**
     * @var \Illuminate\Auth\AuthManager
     */
    private $auth;

    /**
     * @var \Illuminate\Database\Connection
     */
    private $db;

    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    private $file;

    /**
     * @var \Maatwebsite\Excel\Excel
     */
    private $excel;

    /**
     * @var \App\Repositories\Order\OrderRepository
     */
    private $order_repo;

    /**
     * @var \App\Repositories\Order\OrderHistoryRepository
     */
    private $order_history_repo;

    /**
     * @var \App\Repositories\Master\EndUserRepository
     */
    private $end_user_repo;

    /**
     * @var \App\Repositories\Master\TaxRepository
     */
    private $tax_repo;

    /**
     * @var \App\Repositories\Stock\StockRepository
     */
    private $stock_repo;

    /**
     * @var \App\Repositories\Stock\CarryOverStockRepository
     */
    private $carry_over_stock_repo;

    /**
     * @param  \Illuminate\Auth\AuthManager $auth
     * @param  \Illuminate\Database\Connection $db
     * @param  \Illuminate\Filesystem\Filesystem $file
     * @param  \Maatwebsite\Excel\Excel $excel
     * @param  \App\Repositories\Order\OrderRepository $order_repo
     * @param  \App\Repositories\Order\OrderHistoryRepository $order_history_repo
     * @param  \App\Repositories\Master\EndUserRepository $end_user_repo
     * @param  \App\Repositories\Master\TaxRepository $tax_repo
     * @param  \App\Repositories\Stock\StockRepository $stock_repo
     * @param  \App\Repositories\Stock\CarryOverStockRepository $carry_over_stock_repo
     * @return void
     */
    public function __construct(
        AuthManager $auth,
        Connection $db,
        Filesystem $file,
        Excel $excel,
        OrderRepository $order_repo,
        OrderHistoryRepository $order_history_repo,
        EndUserRepository $end_user_repo,
        TaxRepository $tax_repo,
        StockRepository $stock_repo,
        CarryOverStockRepository $carry_over_stock_repo
    ) {
        $this->auth = $auth;
        $this->db = $db;
        $this->file = $file;
        $this->excel = $excel;
        $this->order_repo = $order_repo;
        $this->order_history_repo = $order_history_repo;
        $this->end_user_repo = $end_user_repo;
        $this->tax_repo = $tax_repo;
        $this->stock_repo = $stock_repo;
        $this->carry_over_stock_repo = $carry_over_stock_repo;
    }

    /**
     * 工場（商品）ごとに注文データをサマライズ
     *
     * @param  array $params
     * @param  \App\ValueObjects\Date\HarvestingDate $harvesting_date
     * @param  \App\Models\Order\Collections\OrderForecastCollection $order_forecasts
     * @return \App\Models\Order\Collections\OrderCollection
     */
    public function summarizeOrdersPerFactoryProduct(
        array $params,
        HarvestingDate $harvesting_date,
        OrderForecastCollection $order_forecasts
    ): OrderCollection {
        if ($params['display_term'] === 'month') {
            return $this->summarizeOrdersPerFactoryProductAndMonth($params, $harvesting_date, $order_forecasts);
        }

        $harvesting_dates_list = $harvesting_date->toListOfDatePerWeek((int)$params['week_term'], true);
        $shipping_date_term = [
            'from' => head(head($harvesting_dates_list))->getDefaultShippingDate(),
            'to' => last(last($harvesting_dates_list))->getDefaultShippingDate(),
        ];

        $orders = $this->order_repo->getOrdersBySpeciesAndHarvestingDate($params, $shipping_date_term);
        $factories = $orders->pluckFactoryProducts($order_forecasts)
            ->map(function ($f) use ($orders, $order_forecasts, $harvesting_dates_list) {
                $factory_products = $f->factory_products
                    ->map(function ($fp) use ($orders, $order_forecasts, $harvesting_dates_list) {
                        $summary = [];
                        foreach ($harvesting_dates_list as $week => $harvesting_dates) {
                            $summary[$week] = array_fill_keys(['weights', 'quantities'], []);
                            foreach ($harvesting_dates as $hd) {
                                $date = $hd->format('Ymd');
                                $summary[$week]['weights'][$date] = convert_to_kilogram(0);
                                $summary[$week]['quantities'][$date] = 0;

                                if ($hd->getDefaultShippingDate()
                                    ->willDisplayOrderOnTheDate($fp->latest_delivery_date)) {
                                    [
                                        $summary[$week]['weights'][$date],
                                        $summary[$week]['quantities'][$date]
                                    ]
                                        = array_values(
                                            $orders->filterByFactoryProduct($fp)
                                                ->filterByShippingDate($hd->getDefaultShippingDate())
                                                ->toSumOfQuantityAndWeight()
                                        );

                                    continue;
                                }

                                [
                                    $summary[$week]['weights'][$date],
                                    $summary[$week]['quantities'][$date]
                                ]
                                    = array_values(
                                        $order_forecasts->filterByFactoryProduct($fp)
                                            ->filterByShippingDate($hd->getDefaultShippingDate())
                                            ->toSumOfQuantityAndWeight()
                                    );
                            }

                            $summary[$week]['weights']['total'] = array_sum($summary[$week]['weights']);
                            $summary[$week]['quantities']['total'] = array_sum($summary[$week]['quantities']);
                        }

                        $fp->orders = $summary;
                        return $fp;
                    })
                    ->all();

                $f->factory_products = new OrderCollection($factory_products);
                return $f;
            })
            ->all();

        return new OrderCollection($factories);
    }

    /**
     * 工場（商品）/年月ごとに注文データをサマライズ
     *
     * @param  array $params
     * @param  \App\ValueObjects\Date\HarvestingDate $harvesting_date
     * @param  \App\Models\Order\Collections\OrderForecastCollection $order_forecasts
     * @return \App\Models\Order\Collections\OrderCollection
     */
    public function summarizeOrdersPerFactoryProductAndMonth(
        array $params,
        HarvestingDate $harvesting_date,
        OrderForecastCollection $order_forecasts
    ): OrderCollection {
        $harvesting_months = $harvesting_date->toListOfMonth();
        $shipping_date_term = [
            'from' => head($harvesting_months)->firstOfMonth()->addDay(),
            'to' => last($harvesting_months)->lastOfMonth()->addDay()
        ];

        $orders = $this->order_repo->getOrdersBySpeciesAndHarvestingDate($params, $shipping_date_term);
        $factories = $orders->pluckFactoryProducts($order_forecasts)
            ->map(function ($f) use ($orders, $order_forecasts, $harvesting_months) {
                $factory_products = $f->factory_products
                    ->map(function ($fp) use ($orders, $order_forecasts, $harvesting_months) {
                        $summary = [];
                        foreach ($harvesting_months as $hm) {
                            $month = $hm->format('Ym');
                            $summary['weights'][$month] = convert_to_kilogram(0);
                            $summary['quantities'][$month] = 0;

                            if ($hm->willDisplayOrderOnTheMonth()) {
                                [
                                    $summary['weights'][$month],
                                    $summary['quantities'][$month]
                                ]
                                    = array_values(
                                        $orders->filterByFactoryProduct($fp)
                                            ->filterByShippingMonth($month)
                                            ->toSumOfQuantityAndWeight()
                                    );
                            }
                            if (! $hm->willDisplayOrderOnTheMonth()) {
                                [
                                    $summary['weights'][$month],
                                    $summary['quantities'][$month]
                                ]
                                    = array_values(
                                        $order_forecasts->filterByFactoryProduct($fp)
                                            ->filterByShippingMonth($month)
                                            ->toSumOfQuantityAndWeight()
                                    );
                            }
                        }

                        $summary['weights']['total'] = array_sum($summary['weights']);
                        $summary['quantities']['total'] = array_sum($summary['quantities']);

                        $fp->orders = $summary;
                        return $fp;
                    })
                    ->all();

                $f->factory_products = new OrderCollection($factory_products);
                return $f;
            })
            ->all();

        return new OrderCollection($factories);
    }

    /**
     * 工場商品/納入先ごとに注文データをサマライズ
     *
     * @param  array $params
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Master\Species $species
     * @param  \App\ValueObjects\Date\HarvestingDate $harvesting_date
     * @param  \App\Models\Order\Collections\OrderForecastCollection $order_forecasts
     * @return \App\Models\Order\Collections\OrderCollection
     */
    public function summarizeOrdersPerFactoryProductAndDeliveryDestination(
        array $params,
        Factory $factory,
        Species $species,
        HarvestingDate $harvesting_date,
        OrderForecastCollection $order_forecasts
    ): OrderCollection {
        if ($params['display_term'] === 'month') {
            return $this->summarizeOrdersPerFactoryProductAndDeliveryDestinationAndMonth(
                $params,
                $harvesting_date,
                $order_forecasts
            );
        }

        $harvesting_dates_list = $harvesting_date->toListOfDatePerWeek((int)$params['week_term']);
        $shipping_date_term = [
            'from' => head(head($harvesting_dates_list))->getDefaultShippingDate(),
            'to' => last(last($harvesting_dates_list))->getDefaultShippingDate(),
        ];

        $orders = $this->order_repo->getOrdersBySpeciesAndHarvestingDate($params, $shipping_date_term);
        return $orders->pluckPackagingStyles($order_forecasts)
            ->map(function ($f) use ($factory, $species, $orders, $order_forecasts, $harvesting_dates_list) {
                $packaging_styles = $f->packaging_styles
                    ->map(function ($ps) use ($factory, $species, $orders, $order_forecasts, $harvesting_dates_list) {
                        $ps->weight_of_carry_over_stock = convert_to_kilogram(0);

                        $carry_over_stock = $this->carry_over_stock_repo
                            ->getCarryOveredStock($factory, $species, head(head($harvesting_dates_list)), (array)$ps);
                        if (! is_null($carry_over_stock)) {
                            $ps->weight_of_carry_over_stock = convert_to_kilogram(
                                $carry_over_stock->carry_over_stock_quantity * $ps->weight_per_number_of_heads
                            );
                        }

                        $orders = $orders->filterByPackagingStyle($ps);
                        $order_forecasts = $order_forecasts->filterByPackagingStyle($ps);

                        $factory_products = $orders
                            ->pluckFactoryProducts($order_forecasts)
                            ->first()
                            ->factory_products
                            ->map(function ($fp) use ($orders, $order_forecasts, $harvesting_dates_list) {
                                $orders = $orders->filterByFactoryProduct($fp);
                                $order_forecasts = $order_forecasts->filterByFactoryProduct($fp);

                                $delivery_destinations = $orders
                                    ->pluckDeliveryDestinations($order_forecasts)
                                    ->map(function ($dd) use ($orders, $order_forecasts, $harvesting_dates_list) {
                                        $summary = [];
                                        foreach ($harvesting_dates_list as $week => $harvesting_dates) {
                                            $summary[$week] = array_fill_keys(
                                                ['weights', 'quantities', 'not_forecasted_order', 'only_fixed_order'],
                                                []
                                            );

                                            foreach ($harvesting_dates as $hd) {
                                                $date = $hd->format('Ymd');
                                                $summary[$week]['weights'][$date] = convert_to_kilogram(0);
                                                $summary[$week]['quantities'][$date] = 0;

                                                $shipping_date = $hd->getDefaultShippingDate();
                                                if ($shipping_date
                                                    ->willDisplayOrderOnTheDate($dd->latest_delivery_date)) {
                                                    [
                                                        $summary[$week]['weights'][$date],
                                                        $summary[$week]['quantities'][$date],
                                                        $summary[$week]['only_fixed_order'][$date]
                                                    ]
                                                        = array_values(
                                                            $orders->filterByDeliveryDestination($dd)
                                                                ->filterByShippingDate(
                                                                    $shipping_date,
                                                                    $dd->shipment_lead_time
                                                                )
                                                                ->toSumOfQuantityAndWeight()
                                                        );

                                                    $summary[$week]['not_forecasted_order'][$date] = true;
                                                    continue;
                                                }

                                                [
                                                    $summary[$week]['weights'][$date],
                                                    $summary[$week]['quantities'][$date],
                                                    $summary[$week]['only_fixed_order'][$date]
                                                ]
                                                    = array_values(
                                                        $order_forecasts->filterByDeliveryDestination($dd)
                                                            ->filterByShippingDate(
                                                                $shipping_date,
                                                                $dd->shipment_lead_time
                                                            )
                                                            ->toSumOfQuantityAndWeight()
                                                    );

                                                    $summary[$week]['not_forecasted_order'][$date] = false;
                                            }

                                            $summary[$week]['weights']['total'] =
                                                array_sum($summary[$week]['weights']);
                                            $summary[$week]['quantities']['total'] =
                                                array_sum($summary[$week]['quantities']);
                                        }

                                        $dd->orders = $summary;
                                        return $dd;
                                    })
                                    ->all();

                                $fp->delivery_destinations = new OrderCollection($delivery_destinations);
                                return $fp;
                            })
                            ->all();

                        $ps->factory_products = new OrderCollection($factory_products);
                        return $ps;
                    })
                    ->all();

                $f->packaging_styles = new OrderCollection($packaging_styles);
                return $f;
            })
            ->first()
            ->packaging_styles ?? new OrderCollection([]);
    }

    /**
     * 工場商品/納入先/年月ごとに注文データをサマライズ
     *
     * @param  array $params
     * @param  \App\ValueObjects\Date\HarvestingDate $harvesting_date
     * @param  \App\Models\Order\Collections\OrderForecastCollection $order_forecasts
     * @return \App\Models\Order\Collections\OrderCollection
     */
    public function summarizeOrdersPerFactoryProductAndDeliveryDestinationAndMonth(
        array $params,
        HarvestingDate $harvesting_date,
        OrderForecastCollection $order_forecasts
    ): OrderCollection {
        $harvesting_months = $harvesting_date->toListOfMonth();
        $shipping_date_term = [
            'from' => head($harvesting_months)->firstOfMonth()->addDay(),
            'to' => last($harvesting_months)->lastOfMonth()->addDay()
        ];

        $orders = $this->order_repo->getOrdersBySpeciesAndHarvestingDate($params, $shipping_date_term);
        return $orders->pluckPackagingStyles($order_forecasts)
            ->map(function ($f) use ($orders, $order_forecasts, $harvesting_months) {
                $packaging_styles = $f->packaging_styles
                    ->map(function ($ps) use ($orders, $order_forecasts, $harvesting_months) {
                        $orders = $orders->filterByPackagingStyle($ps);
                        $order_forecasts = $order_forecasts->filterByPackagingStyle($ps);

                        $factory_products = $orders
                            ->pluckFactoryProducts($order_forecasts)
                            ->first()
                            ->factory_products
                            ->map(function ($fp) use ($orders, $order_forecasts, $harvesting_months) {
                                $orders = $orders->filterByFactoryProduct($fp);
                                $order_forecasts = $order_forecasts->filterByFactoryProduct($fp);

                                $delivery_destinations = $orders
                                    ->pluckDeliveryDestinations($order_forecasts)
                                    ->map(function ($dd) use ($orders, $order_forecasts, $harvesting_months) {
                                        $summary = [];
                                        foreach ($harvesting_months as $hm) {
                                            $month = $hm->format('Ym');
                                            $summary['weights'][$month] = convert_to_kilogram(0);
                                            $summary['quantities'][$month] = 0;

                                            if ($hm->willDisplayOrderOnTheMonth()) {
                                                [
                                                    $summary['weights'][$month],
                                                    $summary['quantities'][$month]
                                                ]
                                                    = array_values(
                                                        $orders->filterByDeliveryDestination($dd)
                                                            ->filterByShippingMonth($month)
                                                            ->toSumOfQuantityAndWeight()
                                                    );
                                            }
                                            if (! $hm->willDisplayOrderOnTheMonth()) {
                                                [
                                                    $summary['weights'][$month],
                                                    $summary['quantities'][$month]
                                                ]
                                                    = array_values(
                                                        $order_forecasts->filterByDeliveryDestination($dd)
                                                            ->filterByShippingMonth($month)
                                                            ->toSumOfQuantityAndWeight()
                                                    );
                                            }
                                        }

                                        $summary['weights']['total'] = array_sum($summary['weights']);
                                        $summary['quantities']['total'] = array_sum($summary['quantities']);

                                        $dd->orders = $summary;
                                        return $dd;
                                    })
                                    ->all();

                                $fp->delivery_destinations = new OrderCollection($delivery_destinations);
                                return $fp;
                            })
                            ->all();

                        $ps->factory_products = new OrderCollection($factory_products);
                        return $ps;
                    })
                    ->all();

                $f->packaging_styles = new OrderCollection($packaging_styles);
                return $f;
            })
            ->first()
            ->packaging_styles ?? new OrderCollection([]);
    }

    /**
     * 工場ごとに注文データをサマライズ
     *
     * @param  array $params
     * @param  \App\Models\Master\Species $species
     * @param  \App\ValueObjects\Date\HarvestingDate $harvesting_date
     * @param  \App\Models\Order\Collections\OrderForecastCollection $order_forecasts
     * @return \App\Models\Order\Collections\OrderCollection
     */
    public function summarizeOrdersPerFactory(
        array $params,
        Species $species,
        HarvestingDate $harvesting_date,
        OrderForecastCollection $order_forecasts
    ): OrderCollection {
        if ($params['display_term'] === 'month') {
            return $this->summarizeOrdersPerFactoryAndMonth(
                $params,
                $harvesting_date,
                $order_forecasts
            );
        }

        $harvesting_dates_list = $harvesting_date->toListOfDatePerWeek((int)$params['week_term']);
        $shipping_date_term = [
            'from' => head(head($harvesting_dates_list))->getDefaultShippingDate(),
            'to' => last(last($harvesting_dates_list))->getDefaultShippingDate(),
        ];

        $orders = $this->order_repo->getOrdersBySpeciesAndHarvestingDate($params, $shipping_date_term);
        $factories = $orders->pluckFactories($order_forecasts)
            ->map(function ($f) use ($params, $species, $order_forecasts, $harvesting_dates_list) {
                $factory = new Factory((array)$f);
                $params['factory_code'] = $factory->factory_code;

                $f->packaging_styles = $this->summarizeOrdersPerFactoryProductAndDeliveryDestination(
                    $params,
                    $factory,
                    $species,
                    head(head($harvesting_dates_list)),
                    $order_forecasts->filterByFactory($f)
                );

                return $f;
            })
            ->all();

        return new OrderCollection($factories);
    }

    /**
     * 工場/年月ごとに注文データをサマライズ
     *
     * @param  array $params
     * @param  \App\ValueObjects\Date\HarvestingDate $harvesting_date
     * @param  \App\Models\Order\Collections\OrderForecastCollection $order_forecasts
     * @return \App\Models\Order\Collections\OrderCollection
     */
    public function summarizeOrdersPerFactoryAndMonth(
        array $params,
        HarvestingDate $harvesting_date,
        OrderForecastCollection $order_forecasts
    ): OrderCollection {
        $harvesting_months = $harvesting_date->toListOfMonth();
        $shipping_date_term = [
            'from' => head($harvesting_months)->firstOfMonth()->addDay(),
            'to' => last($harvesting_months)->lastOfMonth()->addDay()
        ];

        $orders = $this->order_repo->getOrdersBySpeciesAndHarvestingDate($params, $shipping_date_term);
        $factories = $orders->pluckFactories($order_forecasts)
            ->map(function ($f) use ($params, $order_forecasts, $harvesting_date) {
                $params['factory_code'] = $f->factory_code;
                $f->packaging_styles = $this->summarizeOrdersPerFactoryProductAndDeliveryDestinationAndMonth(
                    $params,
                    $harvesting_date,
                    $order_forecasts->filterByFactory($f)
                );

                return $f;
            })
            ->all();

        return new OrderCollection($factories);
    }

    /**
     * 注文情報を、更新前の注文数とともに取得
     *
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Master\Species $species
     * @param  \App\ValueObjects\Date\Date $date
     * @param  bool $only_one_month
     * @return \App\Models\Order\Collections\OrderCollection
     */
    public function getOrdersWithPreviousOrderQuantity(
        Factory $factory,
        Species $species,
        Date $date,
        ?bool $only_one_month = false
    ): OrderCollection {
        return $this->order_repo->getOrdersWithPreviousOrderQuantity($factory, $species, $date, $only_one_month);
    }

    /**
     * 出荷日もしくは納入日ごとの受注数を取得
     *
     * @param  \App\ValueObjects\Date\Date $year_month
     * @param  array $packaging_styles
     * @param  \App\Models\Order\Collections\OrderCollection $orders
     * @param  \App\Models\Order\Collections\OrderForecastCollection $order_forecasts
     * @return array
     */
    public function getOrderQuantitiesPerDates(
        Date $year_month,
        array $packaging_styles,
        OrderCollection $orders,
        OrderForecastCollection $order_forecasts
    ): array {
        $dates = [];
        foreach ($year_month->toListOfDatesOfTheMonth() as $date) {
            $order_quantities = [];
            foreach ($packaging_styles as $ps) {
                foreach ($ps->list_of_number_of_cases as $number_of_cases) {
                    foreach ($number_of_cases->delivery_destinations as $dd) {
                        $type = $quantity = $status = $per_collection_time = $shipping = null;
                        if (! $date->willDisplayOrderOnTheDate($dd->latest_delivery_date)) {
                            $type = 'forecast';
                            $quantity = $order_forecasts->filterByDate($date)
                                ->filterByDeliveryDestination($dd)
                                ->filterByFactoryProduct($dd)
                                ->first()
                                ->forecast_number ?? '';

                                $order_quantities[] =
                                    compact('type', 'quantity', 'status', 'per_collection_time', 'shipping');
                                continue;
                        }

                        $type = 'order';
                        $filtered = $orders->filterByDate($date)
                            ->filterByDeliveryDestination($dd)
                            ->filterByFactoryProduct($dd);
                        $quantity = $filtered->pluck('order_quantity')->sum() ?: null;

                        $status = 'temporary_order';
                        if (! is_null($quantity)) {
                            $fixed_orders = $filtered->filterFixed();
                            if ($filtered->count() === $fixed_orders->count()) {
                                $status = 'only_fixed_order';

                                $sum_of_prev_order_quantity = $fixed_orders->pluck('prev_order_quantity')->sum();
                                if ($quantity !== $sum_of_prev_order_quantity) {
                                    $status = 'order_quantity_updated';
                                }

                                $grouped_per_collection_time = $fixed_orders->groupByCollectionTime();
                                if ($grouped_per_collection_time->count() !== 1) {
                                    $status = 'collection_time_updated';
                                    $per_collection_time = $grouped_per_collection_time
                                        ->map(function ($group) {
                                            return $group->pluck('order_quantity')->sum();
                                        })
                                        ->implode('+');
                                }

                                $allocated_orders = $fixed_orders->filterFullAllocated();
                                if ($fixed_orders->count() === $allocated_orders->count()) {
                                    $shipping = 'allocated';
                                }

                                $shipped_orders = $fixed_orders->filterShipped();
                                if ($fixed_orders->count() === $shipped_orders->count()) {
                                    $shipping = 'shipped';
                                }
                            }
                        }

                        $order_quantities[] = compact('type', 'quantity', 'status', 'per_collection_time', 'shipping');
                    }
                }
            }

            $dates[] = [
                'date' => $date,
                'order_quantities' => $order_quantities
            ];
        }

        return $dates;
    }

    /**
     * 納入日ごとの受注数をファイル出力
     *
     * @param  \App\ValueObjects\Date\Date $year_month
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Master\Species $species
     * @param  array $packaging_styles
     * @param  \App\Models\Order\Collections\OrderCollection $orders
     * @param  \App\Models\Order\Collections\OrderForecastCollection $order_forecasts
     */
    public function exportOrderQuantitiesPerDeliveryDate(
        Date $year_month,
        Factory $factory,
        Species $species,
        array $packaging_styles,
        OrderCollection $orders,
        OrderForecastCollection $order_forecasts
    ) {
        $dates = $this->getOrderQuantitiesPerDates($year_month, $packaging_styles, $orders, $order_forecasts);
        $file_name = generate_file_name(config('constant.order.whiteboard_reference.excel_file_name'), [
            $year_month->format('Ym'),
            $factory->factory_abbreviation,
            $species->species_name
        ]);

        return $this->excel
            ->create($file_name, function ($excel) use ($year_month, $factory, $species, $packaging_styles, $dates) {
                $sheet_name = implode('_', [
                    $year_month->format('Ym'),
                    $factory->factory_abbreviation,
                    $species->species_name
                ]);

                $excel->sheet($sheet_name, function ($sheet) use (
                    $year_month,
                    $factory,
                    $species,
                    $packaging_styles,
                    $dates
                ) {
                    $sheet->setFontFamily('HG丸ｺﾞｼｯｸM-PRO');
                    $sheet->setFreeze('B5');
                    $sheet->loadView('order.whiteboard_reference.export')
                        ->with(compact('year_month', 'factory', 'species', 'packaging_styles', 'dates'));

                    $column = get_excel_column_str(count(array_first($dates)['order_quantities']) + 1);
                    $sheet->getStyle("B4:{$column}4")
                        ->getAlignment()
                        ->setTextRotation(255);
                    $sheet->getStyle("B4:{$column}4")
                        ->getAlignment()
                        ->setWrapText(true);
                });
            })
            ->export();
    }

    /**
     * 返品情報とともに注文情報を取得
     *
     * @param  array $params
     * @param  int $page
     * @param  array $order
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getOrdersWithReturnedProduct(array $params, int $page, array $order): LengthAwarePaginator
    {
        $orders = $this->order_repo->getOrdersWithReturnedProduct($params, $order);
        if ($page > $orders->lastPage() && $orders->lastPage() !== 0) {
            throw new PageOverException('target page does not exist.');
        }

        $end_users = $this->end_user_repo->getCurrentApplicatedEndUsers($orders->pluck('end_user_code')->all());
        foreach ($orders as $o) {
            $o->end_user_abbreviation = $end_users->findByEndUserCode($o->end_user_code)->end_user_abbreviation ?? '';
        }

        return $orders;
    }

    /**
     * 出荷日を条件に未発送の注文情報を取得
     *
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Master\Species $species
     * @param  array $shipping_dates
     * @param  array $factory_product_sequence_numbers
     * @param  \App\Models\Master\Warehouse $excepted_warehouse
     * @return \App\Models\Order\Collections\OrderCollection
     */
    public function getNotDeliveredOrdersByShippingDate(
        Factory $factory,
        Species $species,
        array $shipping_dates,
        array $factory_product_sequence_numbers,
        ?Warehouse $excepted_warehouse
    ): OrderCollection {
        return $this->order_repo->getNotDeliveredOrdersByShippingDate(
            $factory,
            $species,
            $shipping_dates,
            $factory_product_sequence_numbers,
            $excepted_warehouse
        );
    }

    /**
     * 注文情報ともに、工場商品ごとの製品引当数を取得
     *
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Master\Species $species
     * @param  array $shipping_dates
     * @param  array $packaging_style
     * @param  \App\Models\Master\Warehouse $warehouse
     * @return array
     */
    public function getAllocatedFactoryProductsWithOrders(
        Factory $factory,
        Species $species,
        array $shipping_dates,
        array $packaging_style,
        Warehouse $warehouse
    ): array {
        return $this->order_repo
            ->getOrdersWithAllocatedProducts($factory, $species, $shipping_dates, $packaging_style, $warehouse)
            ->groupByFactoryProduct()
            ->map(function ($fp) use ($shipping_dates, $warehouse) {
                $fp->delivery_destinations = $fp->orders->groupByDeliveryDestination()
                    ->map(function ($dd) use ($shipping_dates, $warehouse) {
                        $dd->shipping_dates = $dd->orders->groupByShippingDate($shipping_dates, $warehouse);

                        unset($dd->orders);
                        return $dd;
                    })
                    ->all();

                unset($fp->orders);
                return $fp;
            })
            ->all();
    }

    /**
     * 出荷作業帳票出力用の注文情報を検索
     *
     * @param  array $params
     * @return array
     */
    public function searchOrdersToOutputShipmentFiles(array $params): array
    {
        $orders = $this->order_repo->searchOrdersToOutputShipmentFiles($params)
            ->map(function ($o) {
                $o->formatted_order_amount = $o->formatReceivedOrderAmount();
                return $o;
            });

        $end_users = $this->end_user_repo->getCurrentApplicatedEndUsers(
            $orders->pluck('end_user_code')->unique()->all()
        );

        return $orders->groupToOutputShipmentFiles($end_users);
    }

    /**
     * 出荷作業帳票出力用の注文情報を注文番号を指定して取得
     *
     * @param  array $params
     * @param  \App\Models\Master\Customer $customer
     * @param  array $order_numbers
     * @return array
     */
    public function getOrdersToOutputShipmentFiles(array $params, Customer $customer, array $order_numbers): array
    {
        $grouped_orders = $this->order_repo->searchOrdersToOutputShipmentFiles($params, $order_numbers)
            ->groupToOutputShipmentFiles();

        $tax_rates = $this->tax_repo->getAllConsumptionTaxRates();
        return array_map(function ($group) use ($customer, $tax_rates) {
            $end_user = $this->end_user_repo
                ->getApplicatedEndUser($group->end_user_code, $group->shipping_date->format('Y-m-d'));

            $group->seller_name = $end_user->seller_name ?? null;
            $group->end_user_name = $end_user->end_user_name ?? null;
            $group->end_user_abbreviation = $end_user->end_user_abbreviation ?? null;

            $group->orders = $group->orders->map(function ($o) use ($group, $customer, $tax_rates) {
                $tax_rate = $tax_rates->findAppliedTaxRate($group->shipping_date);
                $o->tax_amount = $tax_rate->calculateTaxAmount($customer, (float)$o->customer_received_order_amount);

                return $o;
            });

            return $group;
        }, $grouped_orders);
    }

    /**
     * 集荷依頼書出力対象の注文情報を検索
     *
     * @param  array $params
     * @param  array $order_numbers
     * @return array
     */
    public function searchOrdersToOutputCollectionRequest(array $params, ?array $order_numbers = []): array
    {
        $orders = $this->order_repo->searchOrdersToOutputCollectionRequest($params, $order_numbers);
        $end_users = $this->end_user_repo->getCurrentApplicatedEndUsers(
            $orders->pluck('end_user_code')->unique()->all()
        );

        return $orders->groupToOutputCollectionRequests($end_users);
    }

    /**
     * 指定された注文の出荷情報を更新する
     *
     * @param  array $order_numbers
     * @return void
     * @throws \App\Exceptions\OptimisticLockException
     */
    public function updateShipmentDataOfOrders(array $orders): void
    {
        $this->db->transaction(function () use ($orders) {
            foreach ($orders as $o) {
                $order = $this->order_repo->find($o['order_number']);
                if ($order->hadBeenShipped()) {
                    $message = 'target order had been updated. order number: %s';
                    throw new OptimisticLockException(sprintf($message, $order->order_number));
                }

                $this->order_history_repo->createByOrder($order);
                $this->order_repo->update($order, [
                    'process_class' => ProcessClass::CHANGE_PROCESS,
                    'printing_shipping_date' => $o['shipping_date'],
                    'transport_company_code' => $o['transport_company_code'],
                    'collection_time_sequence_number' => $o['collection_time_sequence_number']
                ]);
            }
        });
    }

    /**
     * 出荷可能な注文データを取得
     *
     * @param  array $params
     * @return \App\Models\Order\Collections\OrderCollection
     */
    public function getShippableOrders(array $params): OrderCollection
    {
        $orders = $this->order_repo->getShippableOrders($params);

        $end_users = $this->end_user_repo->getCurrentApplicatedEndUsers($orders->pluck('end_user_code')->all());
        return $orders->map(function ($o) use ($end_users) {
            $o->end_user_abbreviation = $end_users->findByEndUserCode($o->end_user_code)->end_user_abbreviation ?? '';
            return $o;
        });
    }

    /**
     * 指定された注文を出荷状態に更新する
     *
     * @param  array $order_numbers
     * @return void
     * @throws \App\Exceptions\OptimisticLockException
     */
    public function fixShippingOrderedProducts(array $order_numbers): void
    {
        $this->db->transaction(function () use ($order_numbers) {
            foreach ($order_numbers as $order_number) {
                $order = $this->order_repo->find($order_number);
                if ($order->hadBeenShipped()) {
                    $message = 'target order had been shipped. order number: %s';
                    throw new OptimisticLockException(sprintf($message, $order_number));
                }

                $this->order_history_repo->createByOrder($order);
                $this->order_repo->update($order, [
                    'fixed_shipping_by' => $this->auth->id(),
                    'fixed_shipping_at' => Chronos::now()->format('Y-m-d H:i:s')
                ]);

                $order->stocks->each(function ($s) use ($order) {
                    $this->stock_repo->shipStock($s, $order);
                });
            }
        });
    }

    /**
     * 出荷確定された注文データの情報をファイル出力する
     *
     * @return bool
     */
    public function exportOrdersThatFixedShipping(): bool
    {
        $config = config('settings.data_link.shipment.shipping_data_export');
        $tsv_file_path = $config['tsv_file_path'];
        $end_file_path = $config['end_file_path'];

        if ($this->file->exists($end_file_path)) {
            return true;
        }

        $orders = $this->order_repo->getOrdersThatFixedShipping();
        if ($orders->isEmpty()) {
            return true;
        }

        $file = fopen($tsv_file_path, 'w');
        if ($file) {
            foreach ($orders as $o) {
                $row = implode("\t", $o->getShippingLinkParams());
                fwrite($file, $row."\r\n");
            }
        }

        $file = fopen($end_file_path, 'w');

        try {
            $orders->each(function ($o) {
                $this->order_history_repo->createByOrder($o);
                $this->order_repo->update($o, [
                    'fixed_shipping_sharing_flag' => true
                ]);
            });
        } catch (PDOException $e) {
            report($e);
            return false;
        }

        $zip = new ZipArchive();
        $zip_file_name = sprintf(
            $config['zip_file_path'].$config['zip_file_name'],
            Chronos::now()->format('YmdHis')
        );

        if ($zip->open($zip_file_name, ZipArchive::CREATE)) {
            $tsv_file_name = basename($tsv_file_path);
            $zip->addFile($tsv_file_path, $tsv_file_name);
            $zip->close();
        }

        return true;
    }

    /**
     * 請求書出力対象の注文情報を取得する
     *
     * @param  array $params
     * @param  \App\Models\Master\Customer $customer
     * @param  \App\ValueObjects\Date\DeliveryDate $delivery_month
     * @param  \App\Models\Master\Invoice $invoice
     * @return \App\Models\Order\Collections\OrderCollection
     */
    public function getOrdersThatWillOutputOnInvoice(
        array $params,
        Customer $customer,
        DeliveryDate $delivery_month,
        ?Invoice $invoice
    ): OrderCollection {
        $tax_rates = $this->tax_repo->getAllConsumptionTaxRates();
        return $this->order_repo->getOrdersThatWillOutputOnInvoice($params, $customer, $delivery_month, $invoice)
            ->map(function ($o) use ($customer, $tax_rates) {
                $o->delivery_date = DeliveryDate::parse($o->delivery_date);

                $shipping_date = ShippingDate::parse($o->shipping_date);
                $o->end_user_name = $this->end_user_repo
                    ->getApplicatedEndUser($o->end_user_code, $shipping_date->format('Y-m-d'))
                    ->end_user_name ?? '';

                $tax_rate = $tax_rates->findAppliedTaxRate($shipping_date);
                $tax_amount = $tax_rate->calculateTaxAmount($customer, (float)$o->order_amount);

                $o->tax_amount = $tax_amount;
                $o->order_amount_with_tax = $o->order_amount + $tax_amount;

                return $o;
            });
    }
}

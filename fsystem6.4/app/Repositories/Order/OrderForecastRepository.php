<?php

declare(strict_types=1);

namespace App\Repositories\Order;

use InvalidArgumentException;
use Illuminate\Database\Connection;
use App\Models\Master\DeliveryFactoryProduct;
use App\Models\Master\Factory;
use App\Models\Master\Species;
use App\Models\Order\OrderForecast;
use App\Models\Order\Collections\OrderForecastCollection;
use App\ValueObjects\Date\Date;
use App\ValueObjects\Date\DeliveryDate;
use App\ValueObjects\Date\HarvestingDate;
use App\ValueObjects\Date\ShippingDate;
use App\ValueObjects\Enum\ProcessClass;
use App\ValueObjects\Enum\SlipStatusType;
use App\ValueObjects\Enum\SlipType;

class OrderForecastRepository
{
    /**
     * @var \Illuminate\Database\Connection
     */
    private $db;

    /**
     * @var \App\Models\Order\OrderForecast
     */
    private $model;

    /**
     * @param  \Illuminate\Database\Connection $db
     * @param  \App\Models\Order\OrderForecast $model
     * @return void
     */
    public function __construct(Connection $db, OrderForecast $model)
    {
        $this->db = $db;
        $this->model = $model;
    }

    /**
     * 指定された工場、品種、納入年月の受注フォーキャストの情報を取得
     *
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Master\Species $species
     * @param  \App\ValueObjects\Date\Date $date
     * @param  bool $only_one_month
     * @return \App\Models\Order\Collections\OrderForecastCollection
     * @throws InvalidArgumentException
     */
    public function getOrderForecastsByFactoryAndSpecies(
        Factory $factory,
        Species $species,
        Date $date,
        bool $only_one_month
    ): OrderForecastCollection {
        $date_term = [];
        if ($only_one_month) {
            $date_term = [
                'from' => head($date->toListOfDatesOfTheMonth())->format('Y-m-d'),
                'to' => last($date->toListOfDatesOfTheMonth())->format('Y-m-d')
            ];
        }
        if (! $only_one_month) {
            $date_term = [
                'from' => head($date->toListToExportOrderForecasts())->format('Y-m-d'),
                'to' => last($date->toListToExportOrderForecasts())->format('Y-m-d')
            ];
        }

        $target_date = '';
        if ($date instanceof DeliveryDate) {
            $target_date = 'date';
        }
        if ($date instanceof ShippingDate) {
            $target_date = 'shipping_date';
        }
        if ($target_date === '') {
            throw new InvalidArgumentException('target date was invalid:'. get_class($date));
        }

        return $this->model
            ->select([
                'order_forecast.delivery_destination_code',
                'order_forecast.factory_code',
                'order_forecast.factory_product_sequence_number',
                'order_forecast.date',
                'order_forecast.shipping_date',
                'order_forecast.forecast_number',
                'order_forecast.updated_at'
            ])
            ->join('factory_products', function ($join) {
                $join->on('factory_products.factory_code', '=', 'order_forecast.factory_code')
                    ->on(
                        'factory_products.sequence_number',
                        '=',
                        'order_forecast.factory_product_sequence_number'
                    );
            })
            ->join('products', 'products.product_code', '=', 'factory_products.product_code')
            ->where('order_forecast.factory_code', $factory->factory_code)
            ->where('products.species_code', $species->species_code)
            ->whereBetween("order_forecast.{$target_date}", array_values($date_term))
            ->orderBy('order_forecast.factory_product_sequence_number', 'ASC')
            ->orderBy('order_forecast.delivery_destination_code', 'ASC')
            ->orderBy("order_forecast.{$target_date}", 'ASC')
            ->get();
    }

    /**
     * 指定された品種、期間に応じて受注フォーキャストデータを取得
     *
     * @param  array $params
     * @param  array $shipping_date_term
     * @return \App\Models\Order\Collections\OrderForecastCollection
     */
    public function getOrderForecastsBySpeciesAndHarvestingDate(
        array $params,
        array $shipping_date_term
    ): OrderForecastCollection {
        $order_query = $this->db->table('orders')
            ->select([
                'delivery_destination_code',
                'factory_code',
                'factory_product_sequence_number',
                $this->db->raw('MAX(delivery_date) AS latest_delivery_date')
            ])
            ->where(function ($query) use ($params) {
                if ($delivery_destination_code = $params['delivery_destination_code'] ?? null) {
                    $query->where('delivery_destination_code', $delivery_destination_code);
                }
            })
            ->where(function ($query) use ($params) {
                if ($factory_code = $params['factory_code'] ?? null) {
                    $query->where('factory_code', $factory_code);
                }
            })
            ->where('process_class', '<>', ProcessClass::CANCEL_PROCESS)
            ->where('slip_type', SlipType::NORMAL_SLIP)
            ->where('slip_status_type', '<>', SlipStatusType::RELATION_TEMP_ORDER)
            ->where('factory_cancel_flag', false)
            ->groupBy(['delivery_destination_code', 'factory_code', 'factory_product_sequence_number']);

        $query = $this->model
            ->select([
                'order_forecast.delivery_destination_code',
                'delivery_destinations.delivery_destination_abbreviation',
                'order_forecast.factory_code',
                'factories.factory_abbreviation',
                'order_forecast.factory_product_sequence_number',
                'factory_products.factory_product_name',
                'factory_products.factory_product_abbreviation',
                'factory_products.number_of_heads',
                'factory_products.weight_per_number_of_heads',
                'factory_products.input_group',
                'factory_products.number_of_cases',
                'orders.latest_delivery_date'
            ])
            ->join(
                'delivery_destinations',
                'delivery_destinations.delivery_destination_code',
                '=',
                'order_forecast.delivery_destination_code'
            )
            ->join('factory_products', function ($join) {
                $join->on('factory_products.factory_code', '=', 'order_forecast.factory_code')
                    ->on(
                        'factory_products.sequence_number',
                        '=',
                        'order_forecast.factory_product_sequence_number'
                    );
            })
            ->join('factories', 'factories.factory_code', '=', 'factory_products.factory_code')
            ->join('products', 'products.product_code', '=', 'factory_products.product_code')
            ->leftJoin(
                $this->db->raw("({$order_query->toSql()}) AS orders"),
                function ($join) {
                    $join->on(
                        'orders.delivery_destination_code',
                        '=',
                        'order_forecast.delivery_destination_code'
                    )
                    ->on('orders.factory_code', '=', 'order_forecast.factory_code')
                    ->on(
                        'orders.factory_product_sequence_number',
                        '=',
                        'order_forecast.factory_product_sequence_number'
                    );
                }
            )
            ->setBindings($order_query->getBindings())
            ->where('products.species_code', $params['species_code'])
            ->where(function ($query) use ($params) {
                $factory_code = $params['factory_code'] ?? null;
                if (! is_null($factory_code)) {
                    $query->where('order_forecast.factory_code', $factory_code);
                }
                if (is_null($factory_code)) {
                    $query->affiliatedFactories('order_forecast');
                }
            })
            ->where(function ($query) use ($params) {
                if ($delivery_destination_code = $params['delivery_destination_code'] ?? null) {
                    $query->where('order_forecast.delivery_destination_code', $delivery_destination_code);
                }
            })
            ->orderBy('order_forecast.factory_code', 'ASC')
            ->orderBy('factory_products.number_of_heads', 'ASC')
            ->orderBy('factory_products.weight_per_number_of_heads', 'ASC')
            ->orderBy('factory_products.input_group', 'ASC')
            ->orderBy('factory_products.number_of_cases', 'ASC')
            ->orderBy('order_forecast.factory_product_sequence_number', 'ASC')
            ->orderBy('order_forecast.delivery_destination_code', 'ASC');

        if ($params['display_term'] === 'date') {
            $query
                ->addSelect([
                    'order_forecast.shipping_date',
                    'order_forecast.forecast_number',
                    'order_forecast.forecast_weight'
                ])
                ->whereBetween('order_forecast.shipping_date', [
                    $shipping_date_term['from']->subDay()->toDateString(),
                    $shipping_date_term['to']->toDateString()
                ])
                ->orderBy('order_forecast.shipping_date', 'ASC');
        }
        if ($params['display_term'] === 'month') {
            $query
                ->addSelect(
                    $this->db->raw(
                        "DATE_FORMAT((order_forecast.shipping_date - INTERVAL 1 DAY), '%Y%m') AS shipping_month"
                    ),
                    $this->db->raw("SUM(order_forecast.forecast_number) AS forecast_number"),
                    $this->db->raw("SUM(order_forecast.forecast_weight) AS forecast_weight")
                )
                ->whereBetween('order_forecast.shipping_date', [
                    $shipping_date_term['from']->toDateString(),
                    $shipping_date_term['to']->toDateString()
                ])
                ->groupBy(
                    'order_forecast.factory_code',
                    'order_forecast.factory_product_sequence_number',
                    'order_forecast.delivery_destination_code',
                    $this->db->raw("DATE_FORMAT((order_forecast.shipping_date - INTERVAL 1 DAY), '%Y%m')")
                )
                ->orderBy('shipping_month', 'ASC');
        }

        if ($factory_code = $params['factory_code'] ?? null) {
            $sub_query = $this->db->table('delivery_warehouses')
                ->select(['delivery_destination_code', 'warehouse_code', 'delivery_lead_time', 'shipment_lead_time'])
                ->where('warehouse_code', function ($query) use ($factory_code) {
                    $query->select('warehouse_code')
                        ->from('factory_warehouses')
                        ->whereRaw("factory_code = '{$factory_code}'")
                        ->orderBy('priority', 'ASC')
                        ->limit(1);
                })
                ->toSql();

            $query
                ->addSelect([
                    'delivery_warehouses.warehouse_code',
                    'delivery_warehouses.delivery_lead_time',
                    'delivery_warehouses.shipment_lead_time'
                ])
                ->leftJoin(
                    $this->db->raw("({$sub_query}) AS delivery_warehouses"),
                    'delivery_warehouses.delivery_destination_code',
                    '=',
                    'delivery_destinations.delivery_destination_code'
                );

            if ($params['display_term'] === 'month') {
                $query->groupBy('delivery_warehouses.warehouse_code');
            }
        }

        return $query->get();
    }

    /**
     * 受注フォーキャストの情報を、納入工場商品マスタともに取得
     *
     * @param  array $params
     * @return \App\Models\Order\OrderForecast
     */
    public function getOrderForecastWithDeliveryFactoryProduct(array $params): OrderForecast
    {
        $warehouse_query = $this->db->table('delivery_warehouses')
            ->select(['delivery_destination_code', 'warehouse_code', 'delivery_lead_time', 'shipment_lead_time'])
            ->where('warehouse_code', function ($query) use ($params) {
                $query->select('warehouse_code')
                    ->from('factory_warehouses')
                    ->where('factory_code', $params['factory_code'])
                    ->orderBy('priority', 'ASC')
                    ->limit(1);
            });

        $order_query = $this->db->table('orders')
            ->select([
                'delivery_destination_code',
                'factory_code',
                'factory_product_sequence_number',
                $this->db->raw('MAX(delivery_date) AS latest_delivery_date')
            ])
            ->where('delivery_destination_code', $params['delivery_destination_code'])
            ->where('factory_code', $params['factory_code'])
            ->where('factory_product_sequence_number', $params['factory_product_sequence_number'])
            ->where('process_class', '<>', ProcessClass::CANCEL_PROCESS)
            ->where('slip_type', SlipType::NORMAL_SLIP)
            ->where('slip_status_type', '<>', SlipStatusType::RELATION_TEMP_ORDER)
            ->where('factory_cancel_flag', false)
            ->groupBy('delivery_destination_code')
            ->groupBy('factory_code')
            ->groupBy('factory_product_sequence_number');

        $order_forecast = $this->db->table('delivery_factory_products')
            ->select([
                'delivery_factory_products.delivery_destination_code',
                'delivery_destinations.delivery_destination_abbreviation',
                'delivery_warehouses.delivery_lead_time',
                'delivery_warehouses.shipment_lead_time',
                'delivery_factory_products.factory_code',
                'delivery_factory_products.factory_product_sequence_number',
                'factory_products.factory_product_abbreviation',
                'factory_products.weight_per_number_of_heads',
                'factory_products.number_of_cases',
                'order_forecast.date',
                'order_forecast.updated_at',
                'orders.latest_delivery_date'
            ])
            ->leftJoin('order_forecast', function ($join) use ($params) {
                $join
                    ->on(
                        'delivery_factory_products.delivery_destination_code',
                        '=',
                        'order_forecast.delivery_destination_code'
                    )
                    ->on('delivery_factory_products.factory_code', '=', 'order_forecast.factory_code')
                    ->on(
                        'delivery_factory_products.factory_product_sequence_number',
                        '=',
                        'order_forecast.factory_product_sequence_number'
                    )
                    ->where('order_forecast.date', $params['date']);
            })
            ->join(
                'delivery_destinations',
                'delivery_factory_products.delivery_destination_code',
                '=',
                'delivery_destinations.delivery_destination_code'
            )
            ->join('factory_products', function ($join) {
                $join->on('delivery_factory_products.factory_code', '=', 'factory_products.factory_code')
                    ->on(
                        'delivery_factory_products.factory_product_sequence_number',
                        '=',
                        'factory_products.sequence_number'
                    );
            })
            ->leftJoin(
                $this->db->raw("({$warehouse_query->toSql()}) AS delivery_warehouses"),
                'delivery_warehouses.delivery_destination_code',
                '=',
                'delivery_destinations.delivery_destination_code'
            )
            ->leftJoin(
                $this->db->raw("({$order_query->toSql()}) AS orders"),
                function ($join) {
                    $join->on(
                        'orders.delivery_destination_code',
                        '=',
                        'delivery_factory_products.delivery_destination_code'
                    )
                    ->on('orders.factory_code', '=', 'delivery_factory_products.factory_code')
                    ->on(
                        'orders.factory_product_sequence_number',
                        '=',
                        'delivery_factory_products.factory_product_sequence_number'
                    );
                }
            )
            ->setBindings(array_merge($warehouse_query->getBindings(), $order_query->getBindings()))
            ->where('delivery_factory_products.delivery_destination_code', $params['delivery_destination_code'])
            ->where('delivery_factory_products.factory_code', $params['factory_code'])
            ->where(
                'delivery_factory_products.factory_product_sequence_number',
                $params['factory_product_sequence_number']
            )
            ->first();

        return $this->model->newInstance((array)$order_forecast, ! is_null($order_forecast->updated_at));
    }

    /**
     * 指定された納入工場商品の受注フォーキャストの情報を、既定の収穫日の分だけ取得
     *
     * @param  \App\Models\Master\DeliveryFactoryProduct $delivery_factory_product
     * @param  \App\ValueObjects\Date\HarvestingDate $harvesting_date
     * @return \App\Models\Order\Collections\OrderForecastCollection
     */
    public function getOrderForecastsByDeliveryFactoryProductAndHarvestingDate(
        DeliveryFactoryProduct $delivery_factory_product,
        HarvestingDate $harvesting_date
    ): OrderForecastCollection {
        return $this->model
            ->select([
                'delivery_destination_code',
                'factory_code',
                'factory_product_sequence_number',
                'shipping_date',
                $this->db->raw('SUM(forecast_number) AS forecast_number'),
                $this->db->raw('SUM(forecast_weight) AS forecast_weight')
            ])
            ->where('delivery_destination_code', $delivery_factory_product->delivery_destination_code)
            ->where('factory_code', $delivery_factory_product->factory_code)
            ->where('factory_product_sequence_number', $delivery_factory_product->factory_product_sequence_number)
            ->whereBetween('shipping_date', [
                $harvesting_date->format('Y-m-d'),
                $harvesting_date->getEndOfDateOfGrowthSaleManagement()->format('Y-m-d')
            ])
            ->groupBy('delivery_destination_code')
            ->groupBy('factory_code')
            ->groupBy('factory_product_sequence_number')
            ->groupBy('shipping_date')
            ->get();
    }

    /**
     * 受注フォーキャストトランの登録
     *
     * @param  array $params
     * @return \App\Models\Order\OrderForecast
     */
    public function create(array $params): OrderForecast
    {
        return $this->model->create(array_only($params, [
            'delivery_destination_code',
            'factory_code',
            'factory_product_sequence_number',
            'date',
            'harvesting_date',
            'shipping_date',
            'forecast_number',
            'forecast_weight'
        ]));
    }

    /**
     * 受注フォーキャストトランの更新
     *
     * @param  \App\Models\Order\OrderForecast $order_forecast
     * @param  array $params
     * @return void
     */
    public function update(OrderForecast $order_forecast, array $params): void
    {
        $params['forecast_number'] = $params['forecast_number'] ?: 0;
        $this->db->table('order_forecast')
            ->where('delivery_destination_code', $order_forecast->delivery_destination_code)
            ->where('factory_code', $order_forecast->factory_code)
            ->where('factory_product_sequence_number', $order_forecast->factory_product_sequence_number)
            ->where('date', $order_forecast->date)
            ->update(array_only($params, ['harvesting_date', 'shipping_date', 'forecast_number', 'forecast_weight']));
    }
}

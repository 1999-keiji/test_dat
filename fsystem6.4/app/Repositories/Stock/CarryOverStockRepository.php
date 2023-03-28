<?php

declare(strict_types=1);

namespace App\Repositories\Stock;

use Illuminate\Database\Connection;
use Illuminate\Support\Collection;
use App\Models\Master\Factory;
use App\Models\Master\Species;
use App\Models\Stock\CarryOverStock;
use App\ValueObjects\Date\HarvestingDate;
use App\ValueObjects\Enum\ProcessClass;
use App\ValueObjects\Enum\ShipmentLeadTime;
use App\ValueObjects\Enum\SlipStatusType;
use App\ValueObjects\Enum\SlipType;

class CarryOverStockRepository
{
    /**
     * @var \Illuminate\Database\Connection
     */
    private $db;

    /**
     * @var \App\Models\Stock\CarryOverStock
     */
    private $model;

    /**
     * @param  \Illuminate\Database\Connection $db
     * @param  \App\Models\Stock\CarryOverStock $model
     * @return void
     */
    public function __construct(Connection $db, CarryOverStock $model)
    {
        $this->db = $db;
        $this->model = $model;
    }

    /**
     * 工場/品種別/商品規格別の指定日時点での繰越在庫を取得
     *
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Master\Species $species
     * @param  \App\ValueObjects\Date\HarvestingDate $harvesting_date
     * @param  array $packaging_style
     * @return \App\Models\Stock\CarryOverStock|null
     */
    public function getCarryOveredStock(
        Factory $factory,
        Species $species,
        HarvestingDate $harvesting_date,
        array $packaging_style
    ): ?CarryOverStock {
        $get_base_date = function ($factory, $species, $date, $packaging_style) {
            return $this->model
                ->selectRaw('DATE_ADD(MAX(date), INTERVAL 1 DAY) AS base_date')
                ->where('factory_code', $factory->factory_code)
                ->where('species_code', $species->species_code)
                ->where('number_of_heads', $packaging_style['number_of_heads'])
                ->where('weight_per_number_of_heads', $packaging_style['weight_per_number_of_heads'])
                ->where('input_group', $packaging_style['input_group'])
                ->where('date', '<', $date->toDateString())
                ->whereRaw('WEEKDAY(date) = ?', HarvestingDate::SUNDAY - 1)
                ->where('carry_over_stock_quantity', 0)
                ->first();
        };

        $base_date = $harvesting_date;
        while (true) {
            $carry_overed_stock = $get_base_date($factory, $species, $base_date, $packaging_style);
            if (is_null($carry_overed_stock->base_date)) {
                $base_date = HarvestingDate::parse('2001-01-01');
                break;
            }

            $base_date = HarvestingDate::parse($carry_overed_stock->base_date);
            if ($base_date->eq($harvesting_date)) {
                $base_date = $base_date->subWeek();
                continue;
            }

            $target_table = $base_date->isPassedDate() ? 'productized_result_details' : 'crop';
            $crops = $this->db->table('crop')
                ->leftJoin('productized_result_details', function ($join) {
                    $join->on('productized_result_details.factory_code', '=', 'crop.factory_code')
                        ->on('productized_result_details.species_code', '=', 'crop.species_code')
                        ->on('productized_result_details.harvesting_date', '=', 'crop.date')
                        ->on('productized_result_details.number_of_heads', '=', 'crop.number_of_heads')
                        ->on(
                            'productized_result_details.weight_per_number_of_heads',
                            '=',
                            'crop.weight_per_number_of_heads'
                        )
                        ->on('productized_result_details.input_group', '=', 'crop.input_group');
                })
                ->where('crop.factory_code', $factory->factory_code)
                ->where('crop.species_code', $species->species_code)
                ->where('crop.number_of_heads', $packaging_style['number_of_heads'])
                ->where('crop.weight_per_number_of_heads', $packaging_style['weight_per_number_of_heads'])
                ->where('crop.input_group', $packaging_style['input_group'])
                ->whereBetween('crop.date', [
                    $base_date->subWeek()->startOfWeek()->toDateString(),
                    $base_date->subWeek()->endOfWeek()->toDateString()
                ])
                ->where("{$target_table}.updated_at", '>=', $base_date->toDatetimeString())
                ->get();

            if ($crops->isNotEmpty()) {
                $base_date = $base_date->subWeek();
                continue;
            }

            $delivery_warehouse_query = $this->db->table('delivery_warehouses')
                ->select(['delivery_destination_code', 'warehouse_code', 'delivery_lead_time', 'shipment_lead_time'])
                ->where('warehouse_code', function ($query) use ($factory) {
                    $query->select('warehouse_code')
                        ->from('factory_warehouses')
                        ->where('factory_code', $factory->factory_code)
                        ->orderBy('priority', 'ASC')
                        ->limit(1);
                });

            $shipment_lead_time = new ShipmentLeadTime();
            $raw = '(CASE WHEN COALESCE(delivery_warehouses.shipment_lead_time, ?) = ? '.
                    'THEN orders.shipping_date BETWEEN ? AND ? '.
                    'ELSE orders.shipping_date BETWEEN ? AND ? END)';
            $values = [
                $shipment_lead_time->getDefaultShipmentLeadTime(),
                $shipment_lead_time->getDefaultShipmentLeadTime(),
                $base_date->subWeek()->startOfWeek()->addDay()->toDateString(),
                $base_date->subWeek()->endOfWeek()->addDay()->toDateString(),
                $base_date->subWeek()->startOfWeek()->toDateString(),
                $base_date->subWeek()->endOfWeek()->toDateString()
            ];

            $orders = $this->db->table('orders')
                ->join('factory_products', function ($join) {
                    $join->on('factory_products.factory_code', '=', 'orders.factory_code')
                        ->on('factory_products.sequence_number', '=', 'orders.factory_product_sequence_number');
                })
                ->join('products', 'products.product_code', '=', 'factory_products.product_code')
                ->leftJoin(
                    $this->db->raw("({$delivery_warehouse_query->toSql()}) AS delivery_warehouses"),
                    'delivery_warehouses.delivery_destination_code',
                    '=',
                    'orders.delivery_destination_code'
                )
                ->setBindings($delivery_warehouse_query->getBindings())
                ->where('orders.factory_code', $factory->factory_code)
                ->where('products.species_code', $species->species_code)
                ->where('factory_products.number_of_heads', $packaging_style['number_of_heads'])
                ->where('factory_products.weight_per_number_of_heads', $packaging_style['weight_per_number_of_heads'])
                ->where('factory_products.input_group', $packaging_style['input_group'])
                ->whereRaw($raw, $values)
                ->where('orders.process_class', '<>', ProcessClass::CANCEL_PROCESS)
                ->where('orders.slip_type', SlipType::NORMAL_SLIP)
                ->where('orders.slip_status_type', '<>', SlipStatusType::RELATION_TEMP_ORDER)
                ->where('orders.factory_cancel_flag', false)
                ->where('orders.created_at', '>=', $base_date->toDatetimeString())
                ->get();

            if ($orders->isNotEmpty()) {
                $base_date = $base_date->subWeek();
                continue;
            }

            break;
        }

        $crop_query = $this->db->table('crop')
            ->select([
                'crop.factory_code',
                'crop.species_code',
                'crop.number_of_heads',
                'crop.weight_per_number_of_heads',
                'crop.input_group',
                $this->db->raw(
                    'SUM(CASE WHEN productized_result_details.product_quantity IS NULL '.
                    'THEN crop.crop_number '.
                    'ELSE productized_result_details.product_quantity END) AS stock_quantity'
                )
            ])
            ->leftJoin('productized_result_details', function ($join) {
                $join->on('productized_result_details.factory_code', '=', 'crop.factory_code')
                    ->on('productized_result_details.species_code', '=', 'crop.species_code')
                    ->on('productized_result_details.harvesting_date', '=', 'crop.date')
                    ->on('productized_result_details.number_of_heads', '=', 'crop.number_of_heads')
                    ->on(
                        'productized_result_details.weight_per_number_of_heads',
                        '=',
                        'crop.weight_per_number_of_heads'
                    )
                    ->on('productized_result_details.input_group', '=', 'crop.input_group');
            })
            ->where('crop.factory_code', $factory->factory_code)
            ->where('crop.species_code', $species->species_code)
            ->where('crop.number_of_heads', $packaging_style['number_of_heads'])
            ->where('crop.weight_per_number_of_heads', $packaging_style['weight_per_number_of_heads'])
            ->where('crop.input_group', $packaging_style['input_group'])
            ->whereBetween('crop.date', [
                $base_date->toDateString(),
                $harvesting_date->subWeek()->endOfWeek()->toDateString()
            ])
            ->groupBy([
                'crop.factory_code',
                'crop.species_code',
                'crop.number_of_heads',
                'crop.weight_per_number_of_heads',
                'crop.input_group'
            ]);

        $delivery_warehouse_query = $this->db->table('delivery_warehouses')
            ->select(['delivery_destination_code', 'warehouse_code', 'delivery_lead_time', 'shipment_lead_time'])
            ->where('warehouse_code', function ($query) use ($factory) {
                $query->select('warehouse_code')
                    ->from('factory_warehouses')
                    ->where('factory_code', $factory->factory_code)
                    ->orderBy('priority', 'ASC')
                    ->limit(1);
            });

        $shipment_lead_time = new ShipmentLeadTime();
        $raw = '(CASE WHEN COALESCE(delivery_warehouses.shipment_lead_time, ?) = ? '.
                'THEN orders.shipping_date BETWEEN ? AND ? '.
                'ELSE orders.shipping_date BETWEEN ? AND ? END)';
        $values = [
            $shipment_lead_time->getDefaultShipmentLeadTime(),
            $shipment_lead_time->getDefaultShipmentLeadTime(),
            $base_date->addDay()->toDateString(),
            $harvesting_date->subWeek()->endOfWeek()->addDay()->toDateString(),
            $base_date->toDateString(),
            $harvesting_date->subWeek()->endOfWeek()->toDateString()
        ];

        $order_query = $this->db->table('orders')
            ->select([
                'orders.factory_code',
                'products.species_code',
                'factory_products.number_of_heads',
                'factory_products.weight_per_number_of_heads',
                'factory_products.input_group',
                $this->db->raw('SUM(orders.order_quantity * factory_products.number_of_cases) AS order_quantity')
            ])
            ->join('factory_products', function ($join) {
                $join->on('factory_products.factory_code', '=', 'orders.factory_code')
                    ->on('factory_products.sequence_number', '=', 'orders.factory_product_sequence_number');
            })
            ->join('products', 'products.product_code', '=', 'factory_products.product_code')
            ->leftJoin(
                $this->db->raw("({$delivery_warehouse_query->toSql()}) AS delivery_warehouses"),
                'delivery_warehouses.delivery_destination_code',
                '=',
                'orders.delivery_destination_code'
            )
            ->setBindings($delivery_warehouse_query->getBindings())
            ->where('orders.factory_code', $factory->factory_code)
            ->where('products.species_code', $species->species_code)
            ->where('factory_products.number_of_heads', $packaging_style['number_of_heads'])
            ->where('factory_products.weight_per_number_of_heads', $packaging_style['weight_per_number_of_heads'])
            ->where('factory_products.input_group', $packaging_style['input_group'])
            ->whereRaw($raw, $values)
            ->where('orders.process_class', '<>', ProcessClass::CANCEL_PROCESS)
            ->where('orders.slip_type', SlipType::NORMAL_SLIP)
            ->where('orders.slip_status_type', '<>', SlipStatusType::RELATION_TEMP_ORDER)
            ->where('orders.factory_cancel_flag', false)
            ->groupBy([
                'orders.factory_code',
                'products.species_code',
                'factory_products.number_of_heads',
                'factory_products.weight_per_number_of_heads',
                'factory_products.input_group'
            ]);

        $disposed_stock_query = $this->db->table('stocks')
            ->select([
                'factory_code',
                'species_code',
                'number_of_heads',
                'weight_per_number_of_heads',
                'input_group',
                $this->db->raw('SUM(disposal_quantity) AS disposal_quantity')
            ])
            ->where('factory_code', $factory->factory_code)
            ->where('species_code', $species->species_code)
            ->where('number_of_heads', $packaging_style['number_of_heads'])
            ->where('weight_per_number_of_heads', $packaging_style['weight_per_number_of_heads'])
            ->where('input_group', $packaging_style['input_group'])
            ->whereBetween('disposal_at', [
                $base_date->toDateString(),
                $harvesting_date->subWeek()->endOfWeek()->toDateString()
            ])
            ->groupBy([
                'factory_code',
                'species_code',
                'number_of_heads',
                'weight_per_number_of_heads',
                'input_group'
            ]);

        $carry_overed_stock = $this->db
            ->table($this->db->raw("({$crop_query->toSql()}) AS crops"))
            ->select([
                $this->db->raw(
                    'crops.stock_quantity - COALESCE(orders.order_quantity, 0) - '.
                    'COALESCE(disposed_stocks.disposal_quantity, 0) AS carry_over_stock_quantity'
                )
            ])
            ->leftJoin($this->db->raw("({$order_query->toSql()}) AS orders"), function ($join) {
                $join->on('orders.factory_code', '=', 'crops.factory_code')
                    ->on('orders.species_code', '=', 'crops.species_code')
                    ->on('orders.number_of_heads', '=', 'crops.number_of_heads')
                    ->on('orders.weight_per_number_of_heads', '=', 'crops.weight_per_number_of_heads')
                    ->on('orders.input_group', '=', 'crops.input_group');
            })
            ->leftJoin($this->db->raw("({$disposed_stock_query->toSql()}) AS disposed_stocks"), function ($join) {
                $join->on('disposed_stocks.factory_code', '=', 'crops.factory_code')
                    ->on('disposed_stocks.species_code', '=', 'crops.species_code')
                    ->on('disposed_stocks.number_of_heads', '=', 'crops.number_of_heads')
                    ->on('disposed_stocks.weight_per_number_of_heads', '=', 'crops.weight_per_number_of_heads')
                    ->on('disposed_stocks.input_group', '=', 'crops.input_group');
            })
            ->setBindings(array_merge(
                $crop_query->getBindings(),
                $order_query->getBindings(),
                $disposed_stock_query->getBindings()
            ))
            ->first();

        if (is_null($carry_overed_stock)) {
            return $carry_overed_stock;
        }

        return $this->model->newInstance((array)$carry_overed_stock);
    }

    /**
     * 繰越在庫データの登録
     *
     * @param  \Illuminate\Support\Collection $carry_overed_stocks
     * @return void
     */
    public function insertCarryOveredStocks(Collection $carry_overed_stocks): void
    {
        $carry_overed_stocks->chunk(1000)->each(function ($chunked) {
            $this->model->insert($chunked->all());
        });
    }
}

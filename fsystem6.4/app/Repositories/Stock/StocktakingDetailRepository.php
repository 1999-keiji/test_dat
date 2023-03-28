<?php

declare(strict_types=1);

namespace App\Repositories\Stock;

use Illuminate\Database\Connection;
use App\Models\Stock\Stocktaking;
use App\Models\Stock\StocktakingDetail;
use App\Models\Stock\Collections\StocktakingDetailCollection;
use App\ValueObjects\Date\HarvestingDate;
use App\ValueObjects\Enum\ProcessClass;
use App\ValueObjects\Enum\SlipType;
use App\ValueObjects\Enum\SlipStatusType;

class StocktakingDetailRepository
{
    /**
     * @var \Illuminate\Database\Connection
     */
    private $db;

    /**
     * @var \App\Models\Stock\StocktakingDetail
     */
    private $model;

    /**
     * @param  \Illuminate\Database\Connection $db
     * @param  \App\Models\Stock\StocktakingDetail $model
     * @return void
     */
    public function __construct(Connection $db, StocktakingDetail $model)
    {
        $this->db = $db;
        $this->model = $model;
    }

    /**
     * 在庫棚卸明細データの取得
     *
     * @param  array $params
     * @return \App\Models\Stock\StocktakingDetail|null
     */
    public function getStocktakingDetail(array $params): ?StocktakingDetail
    {
        $natural_keys = [
            'factory_code',
            'warehouse_code',
            'stocktaking_month',
            'species_code',
            'number_of_heads',
            'weight_per_number_of_heads',
            'input_group',
            'delivery_destination_code',
            'number_of_cases'
        ];

        $query = $this->model->newQuery();
        foreach ($natural_keys as $ck) {
            $query->where($ck, $params[$ck]);
        }

        return $query->first();
    }

    /**
     * 棚卸完了前の在庫棚卸データの取得
     *
     * @param  \App\Models\Stock\Stocktaking $stocktaking
     * @param  array $order
     * @return \App\Models\Stock\Collections\StocktakingDetailCollection
     */
    public function getStocktakingDetails(Stocktaking $stocktaking, array $order): StocktakingDetailCollection
    {
        $order_allocations_query = $this->db->table('orders')
            ->select([
                'product_allocations.factory_code',
                'product_allocations.warehouse_code',
                'product_allocations.species_code',
                $this->db->raw("DATE_FORMAT(product_allocations.harvesting_date, '%Y/%m') AS harvesting_month"),
                'orders.factory_product_sequence_number',
                'orders.delivery_destination_code',
                $this->db->raw('SUM(product_allocations.allocation_quantity) AS allocation_quantity')
            ])
            ->join('product_allocations', 'product_allocations.order_number', '=', 'orders.order_number')
            ->join('factory_products', function ($join) {
                $join->on('factory_products.factory_code', '=', 'orders.factory_code')
                    ->on('factory_products.sequence_number', '=', 'orders.factory_product_sequence_number');
            })
            ->where('product_allocations.factory_code', $stocktaking->factory_code)
            ->where('product_allocations.warehouse_code', $stocktaking->warehouse_code)
            ->where(function ($query) use ($stocktaking) {
                $harvesting_month = HarvestingDate::createFromYearMonth($stocktaking->stocktaking_month);
                $query->whereBetween('product_allocations.harvesting_date', [
                    $harvesting_month->firstOfMonth()->toDateString(),
                    $harvesting_month->endOfMonth()->toDateString()
                ]);
            })
            ->where('orders.process_class', '<>', ProcessClass::CANCEL_PROCESS)
            ->where('orders.slip_type', SlipType::NORMAL_SLIP)
            ->where('orders.slip_status_type', '<>', SlipStatusType::RELATION_TEMP_ORDER)
            ->where('orders.factory_cancel_flag', false)
            ->where(function ($query) {
                $query->whereNull('orders.fixed_shipping_at')
                    ->orWhere(function ($query) {
                        $query->whereRaw('orders.printing_shipping_date > CURRENT_DATE')
                            ->whereNotNull('orders.fixed_shipping_at');
                    });
            })
            ->groupBy('product_allocations.factory_code')
            ->groupBy('product_allocations.warehouse_code')
            ->groupBy('product_allocations.species_code')
            ->groupBy(
                $this->db->raw("DATE_FORMAT(product_allocations.harvesting_date, '%Y/%m')")
            )
            ->groupBy('orders.factory_product_sequence_number')
            ->groupBy('orders.delivery_destination_code');

        $stocks_query = $this->db->table('stocks')
            ->select([
                'factory_code',
                $this->db->raw(sprintf("'%s' AS current_warehouse_code", $stocktaking->warehouse_code)),
                'species_code',
                $this->db->raw("DATE_FORMAT(harvesting_date, '%Y/%m') AS harvesting_month"),
                'number_of_heads',
                'weight_per_number_of_heads',
                'input_group',
                'order_number',
                $this->db->raw('SUM(stock_quantity - disposal_quantity) AS stock_quantity')
            ])
            ->where('factory_code', $stocktaking->factory_code)
            ->where(function ($query) use ($stocktaking) {
                $query->where('warehouse_code', $stocktaking->warehouse_code)
                    ->orWhere('before_warehouse_code', $stocktaking->warehouse_code);
            })
            ->whereNull('order_number')
            ->whereRaw('stock_quantity > 0')
            ->whereRaw('stock_quantity <> disposal_quantity')
            ->where(function ($query) use ($stocktaking) {
                $harvesting_month = HarvestingDate::createFromYearMonth($stocktaking->stocktaking_month);
                $query->whereBetween('harvesting_date', [
                    $harvesting_month->firstOfMonth()->toDateString(),
                    $harvesting_month->endOfMonth()->toDateString()
                ]);
            })
            ->where(function ($query) {
                $query
                    ->where(function ($query) {
                        $query->whereNull('moving_start_at')
                            ->whereNull('moving_complete_at');
                    })
                    ->orWhere(function ($query) {
                        $query->whereRaw('moving_start_at = moving_complete_at')
                            ->orWhereRaw('moving_start_at > CURRENT_DATE');
                    });
            })
            ->groupBy('factory_code')
            ->groupBy('current_warehouse_code')
            ->groupBy('species_code')
            ->groupBy($this->db->raw("DATE_FORMAT(harvesting_date, '%Y/%m')"))
            ->groupBy('number_of_heads')
            ->groupBy('weight_per_number_of_heads')
            ->groupBy('input_group')
            ->groupBy('order_number');

        $union = $this->model
            ->select([
                'stocks.factory_code',
                'stocks.current_warehouse_code AS warehouse_code',
                'stocks.harvesting_month AS stocktaking_month',
                'stocks.species_code',
                'species.species_name',
                $this->db->raw("NULL AS delivery_destination_code"),
                $this->db->raw("'未引当' AS delivery_destination_abbreviation"),
                'stocks.number_of_heads',
                'stocks.weight_per_number_of_heads',
                'stocks.input_group',
                $this->db->raw("0 AS number_of_cases"),
                $this->db->raw("'' AS unit"),
                $this->db->raw('COALESCE(stocktaking_details.stock_quantity, stocks.stock_quantity) AS stock_quantity'),
                'stocks.stock_quantity AS actual_stock_quantity',
                $this->db->raw("COALESCE(stocktaking_details.remark, '') AS remark")
            ])
            ->rightJoin($this->db->raw("({$stocks_query->toSql()}) AS stocks"), function ($join) {
                $join->on('stocks.factory_code', '=', 'stocktaking_details.factory_code')
                    ->on('stocks.current_warehouse_code', '=', 'stocktaking_details.warehouse_code')
                    ->on('stocks.harvesting_month', '=', 'stocktaking_details.stocktaking_month')
                    ->on('stocks.species_code', '=', 'stocktaking_details.species_code')
                    ->on('stocks.number_of_heads', '=', 'stocktaking_details.number_of_heads')
                    ->on('stocks.weight_per_number_of_heads', '=', 'stocktaking_details.weight_per_number_of_heads')
                    ->on('stocks.input_group', '=', 'stocktaking_details.input_group')
                    ->whereNull('stocktaking_details.delivery_destination_code');
            })
            ->setBindings($stocks_query->getBindings())
            ->join('species', 'species.species_code', '=', 'stocks.species_code');

        $query = $this->db
            ->table($this->db->raw("({$order_allocations_query->toSql()}) AS order_allocations"))
            ->setBindings($order_allocations_query->getBindings())
            ->select([
                'order_allocations.factory_code',
                'order_allocations.warehouse_code',
                'order_allocations.harvesting_month AS stocktaking_month',
                'order_allocations.species_code',
                'species.species_name',
                'order_allocations.delivery_destination_code',
                'delivery_destinations.delivery_destination_abbreviation',
                'factory_products.number_of_heads',
                'factory_products.weight_per_number_of_heads',
                'factory_products.input_group',
                'factory_products.number_of_cases',
                'factory_products.unit',
                $this->db->raw(
                    'COALESCE(stocktaking_details.stock_quantity, order_allocations.allocation_quantity) '.
                    'AS stock_quantity'
                ),
                'order_allocations.allocation_quantity AS actual_stock_quantity',
                $this->db->raw("COALESCE(stocktaking_details.remark, '') AS remark")
            ])
            ->join('species', 'species.species_code', '=', 'order_allocations.species_code')
            ->join('factory_products', function ($join) {
                $join->on('factory_products.factory_code', '=', 'order_allocations.factory_code')
                    ->on('factory_products.sequence_number', '=', 'order_allocations.factory_product_sequence_number');
            })
            ->join(
                'delivery_destinations',
                'delivery_destinations.delivery_destination_code',
                '=',
                'order_allocations.delivery_destination_code'
            )
            ->leftJoin('stocktaking_details', function ($join) {
                $join->on('stocktaking_details.factory_code', '=', 'order_allocations.factory_code')
                    ->on('stocktaking_details.warehouse_code', '=', 'order_allocations.warehouse_code')
                    ->on('stocktaking_details.stocktaking_month', '=', 'order_allocations.harvesting_month')
                    ->on('stocktaking_details.species_code', '=', 'order_allocations.species_code')
                    ->on('stocktaking_details.number_of_heads', '=', 'factory_products.number_of_heads')
                    ->on(
                        'stocktaking_details.weight_per_number_of_heads',
                        '=',
                        'factory_products.weight_per_number_of_heads'
                    )
                    ->on('stocktaking_details.input_group', '=', 'factory_products.input_group')
                    ->on(
                        'stocktaking_details.delivery_destination_code',
                        '=',
                        'order_allocations.delivery_destination_code'
                    );
            })
            ->union($union)
            ->orderBy('species_code', 'ASC');

        if (count($order) === 0) {
            $query->orderByRaw('delivery_destination_code IS NULL ASC, delivery_destination_code ASC')
                ->orderBy('number_of_heads', 'ASC')
                ->orderBy('weight_per_number_of_heads', 'ASC')
                ->orderBy('input_group', 'ASC')
                ->orderBy('number_of_cases', 'ASC');
        }
        if (count($order) !== 0) {
            $query->orderBy('number_of_heads', $order['order'])
                ->orderBy('weight_per_number_of_heads', $order['order'])
                ->orderBy('input_group', 'ASC')
                ->orderByRaw('delivery_destination_code IS NULL ASC, delivery_destination_code ASC')
                ->orderBy('number_of_cases', 'ASC');
        }

        return StocktakingDetail::hydrate($query->get()->all());
    }

    /**
     * 棚卸完了後の在庫棚卸データの取得
     *
     * @param  \App\Models\Stock\Stocktaking $stocktaking
     * @param  array $order
     * @return \App\Models\Stock\Collections\StocktakingDetailCollection
     */
    public function getFixedStocktakingDetails(Stocktaking $stocktaking, array $order): StocktakingDetailCollection
    {
        $query = $this->model
            ->select([
                'stocktaking_details.species_code',
                'species.species_name',
                'stocktaking_details.delivery_destination_code',
                $this->db->raw(
                    "COALESCE(delivery_destinations.delivery_destination_abbreviation, '未引当') ".
                    'AS delivery_destination_abbreviation'
                ),
                'stocktaking_details.number_of_heads',
                'stocktaking_details.weight_per_number_of_heads',
                'stocktaking_details.input_group',
                'stocktaking_details.number_of_cases',
                'stocktaking_details.unit',
                'stocktaking_details.stock_quantity',
                'stocktaking_details.actual_stock_quantity',
                'stocktaking_details.remark'
            ])
            ->join('species', 'species.species_code', '=', 'stocktaking_details.species_code')
            ->leftJoin(
                'delivery_destinations',
                'delivery_destinations.delivery_destination_code',
                '=',
                'stocktaking_details.delivery_destination_code'
            )
            ->where('stocktaking_details.factory_code', $stocktaking->factory_code)
            ->where('stocktaking_details.warehouse_code', $stocktaking->warehouse_code)
            ->where('stocktaking_details.stocktaking_month', $stocktaking->stocktaking_month)
            ->orderBy('stocktaking_details.species_code', 'ASC');

        if (count($order) === 0) {
            $query
                ->orderByRaw(
                    'stocktaking_details.delivery_destination_code IS NULL ASC, '.
                    'stocktaking_details.delivery_destination_code ASC'
                )
                ->orderBy('stocktaking_details.number_of_heads', 'ASC')
                ->orderBy('stocktaking_details.weight_per_number_of_heads', 'ASC')
                ->orderBy('stocktaking_details.input_group', 'ASC')
                ->orderBy('stocktaking_details.number_of_cases', 'ASC');
        }
        if (count($order) !== 0) {
            $query->orderBy('stocktaking_details.number_of_heads', $order['order'])
                ->orderBy('stocktaking_details.weight_per_number_of_heads', $order['order'])
                ->orderBy('stocktaking_details.input_group', 'ASC')
                ->orderByRaw(
                    'stocktaking_details.delivery_destination_code IS NULL ASC, '.
                    'stocktaking_details.delivery_destination_code ASC'
                )
                ->orderBy('stocktaking_details.number_of_cases', 'ASC');
        }

        return $query->get();
    }

    /**
     * 在庫保管形態ごとの在庫棚卸データの取得
     *
     * @param  \App\Models\Stock\Stocktaking $stocktaking
     * @param  array $dates
     * @return \App\Models\Stock\Collections\StocktakingDetailCollection
     */
    public function getStocktakingDetailsGroupByStockStyle(
        Stocktaking $stocktaking,
        array $dates
    ): StocktakingDetailCollection {
        $stock_result_by_warehouse_query = $this->db->table('stock_result_by_warehouses')
            ->select([
                'factory_code',
                'species_code',
                'warehouse_code',
                $this->db->raw(sprintf("'%s' AS stocktaking_month", $stocktaking->stocktaking_month)),
                'number_of_heads',
                'weight_per_number_of_heads',
                'input_group'
            ])
            ->where('factory_code', $stocktaking->factory_code)
            ->where('warehouse_code', $stocktaking->warehouse_code)
            ->whereBetween('harvesting_date', [head($dates)->format('Y-m-d'), last($dates)->format('Y-m-d')])
            ->groupBy('factory_code')
            ->groupBy('species_code')
            ->groupBy('warehouse_code')
            ->groupBy('number_of_heads')
            ->groupBy('weight_per_number_of_heads')
            ->groupBy('input_group');

        $right_join_query = $this->model
            ->select([
                'stock_result_by_warehouses.factory_code',
                'stock_result_by_warehouses.species_code',
                'species.species_name',
                'stock_result_by_warehouses.warehouse_code',
                'stock_result_by_warehouses.number_of_heads',
                'stock_result_by_warehouses.weight_per_number_of_heads',
                'stock_result_by_warehouses.input_group',
                $this->db->raw('0 AS number_of_cases'),
                $this->db->raw('COALESCE(stocktaking_details.actual_stock_quantity, 0) AS actual_stock_quantity'),
                $this->db->raw(
                    'COALESCE((stocktaking_details.actual_stock_quantity * '.
                    'stock_result_by_warehouses.weight_per_number_of_heads), 0) AS stock_weight'
                ),
            ])
            ->rightJoin(
                $this->db->raw("({$stock_result_by_warehouse_query->toSql()}) AS stock_result_by_warehouses"),
                function ($join) {
                    $join->on('stock_result_by_warehouses.factory_code', '=', 'stocktaking_details.factory_code')
                        ->on('stock_result_by_warehouses.species_code', '=', 'stocktaking_details.species_code')
                        ->on('stock_result_by_warehouses.warehouse_code', '=', 'stocktaking_details.warehouse_code')
                        ->on(
                            'stock_result_by_warehouses.stocktaking_month',
                            '=',
                            'stocktaking_details.stocktaking_month'
                        )
                        ->on('stock_result_by_warehouses.number_of_heads', '=', 'stocktaking_details.number_of_heads')
                        ->on(
                            'stock_result_by_warehouses.weight_per_number_of_heads',
                            '=',
                            'stocktaking_details.weight_per_number_of_heads'
                        )
                        ->on('stock_result_by_warehouses.input_group', '=', 'stocktaking_details.input_group');
                }
            )
            ->leftJoin('species', 'species.species_code', '=', 'stock_result_by_warehouses.species_code')
            ->setBindings($stock_result_by_warehouse_query->getBindings())
            ->whereNull('stocktaking_details.delivery_destination_code');

        $left_join_query = $this->model
            ->select([
                'stocktaking_details.factory_code',
                'stocktaking_details.species_code',
                'species.species_name',
                'stocktaking_details.warehouse_code',
                'stocktaking_details.number_of_heads',
                'stocktaking_details.weight_per_number_of_heads',
                'stocktaking_details.input_group',
                $this->db->raw('0 AS number_of_cases'),
                'stocktaking_details.actual_stock_quantity',
                $this->db->raw(
                    'COALESCE((stocktaking_details.actual_stock_quantity * '.
                    'stocktaking_details.weight_per_number_of_heads), 0) AS stock_weight'
                ),
            ])
            ->join('species', 'species.species_code', '=', 'stocktaking_details.species_code')
            ->leftJoin(
                $this->db->raw("({$stock_result_by_warehouse_query->toSql()}) AS stock_result_by_warehouses"),
                function ($join) {
                    $join->on('stock_result_by_warehouses.factory_code', '=', 'stocktaking_details.factory_code')
                        ->on('stock_result_by_warehouses.species_code', '=', 'stocktaking_details.species_code')
                        ->on('stock_result_by_warehouses.warehouse_code', '=', 'stocktaking_details.warehouse_code')
                        ->on(
                            'stock_result_by_warehouses.stocktaking_month',
                            '=',
                            'stocktaking_details.stocktaking_month'
                        )
                        ->on('stock_result_by_warehouses.number_of_heads', '=', 'stocktaking_details.number_of_heads')
                        ->on(
                            'stock_result_by_warehouses.weight_per_number_of_heads',
                            '=',
                            'stocktaking_details.weight_per_number_of_heads'
                        )
                        ->on('stock_result_by_warehouses.input_group', '=', 'stocktaking_details.input_group');
                }
            )
            ->setBindings($stock_result_by_warehouse_query->getBindings())
            ->where('stocktaking_details.factory_code', $stocktaking->factory_code)
            ->where('stocktaking_details.warehouse_code', $stocktaking->warehouse_code)
            ->where('stocktaking_details.stocktaking_month', $stocktaking->stocktaking_month)
            ->whereNull('stocktaking_details.delivery_destination_code');

        return $this->model
            ->select([
                'stocktaking_details.factory_code',
                'stocktaking_details.species_code',
                'species.species_name',
                'stocktaking_details.warehouse_code',
                'stocktaking_details.number_of_heads',
                'stocktaking_details.weight_per_number_of_heads',
                'stocktaking_details.input_group',
                'stocktaking_details.number_of_cases',
                $this->db->raw('SUM(stocktaking_details.actual_stock_quantity) AS actual_stock_quantity'),
                $this->db->raw(
                    'SUM(stocktaking_details.actual_stock_quantity * stocktaking_details.weight_per_number_of_heads) '.
                    'AS stock_weight'
                )
            ])
            ->join('species', 'species.species_code', '=', 'stocktaking_details.species_code')
            ->where('stocktaking_details.factory_code', $stocktaking->factory_code)
            ->where('stocktaking_details.warehouse_code', $stocktaking->warehouse_code)
            ->where('stocktaking_details.stocktaking_month', $stocktaking->stocktaking_month)
            ->whereNotNull('stocktaking_details.delivery_destination_code')
            ->groupBy('stocktaking_details.factory_code')
            ->groupBy('stocktaking_details.species_code')
            ->groupBy('stocktaking_details.warehouse_code')
            ->groupBy('stocktaking_details.number_of_heads')
            ->groupBy('stocktaking_details.weight_per_number_of_heads')
            ->groupBy('stocktaking_details.input_group')
            ->groupBy('stocktaking_details.number_of_cases')
            ->union($right_join_query)
            ->union($left_join_query)
            ->orderBy('species_code', 'ASC')
            ->orderByRaw('(CASE WHEN number_of_cases <> 0 THEN 0 ELSE 1 END) ASC')
            ->orderBy('number_of_heads')
            ->orderBy('weight_per_number_of_heads')
            ->orderBy('input_group')
            ->orderBy('number_of_cases')
            ->get();
    }

    /**
     * 在庫棚卸明細データの登録
     *
     * @param  array $params
     * @return \App\Models\Stock\StocktakingDetail
     */
    public function create(array $params): StocktakingDetail
    {
        unset($params['species_name'], $params['delivery_destination_abbreviation']);
        return $this->model->create($params);
    }

    /**
     * 在庫棚卸明細データの更新
     *
     * @param  \App\Models\Stock\StocktakingDetail $stocktaking_detail
     * @param  array $params
     * @return \App\Models\Stock\StocktakingDetail
     */
    public function update(StocktakingDetail $stocktaking_detail, array $params): StocktakingDetail
    {
        $stocktaking_detail->fill($params)->save();
        return $stocktaking_detail;
    }
}

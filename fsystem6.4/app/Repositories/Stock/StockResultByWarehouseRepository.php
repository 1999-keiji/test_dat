<?php

declare(strict_types=1);

namespace App\Repositories\Stock;

use stdClass;
use Illuminate\Database\Connection;
use App\Models\Master\Factory;
use App\Models\Master\Species;
use App\Models\Master\Warehouse;
use App\Models\Shipment\ProductizedResultDetail;
use App\Models\Stock\Stock;
use App\Models\Stock\StockResultByWarehouse;
use App\Models\Stock\StocktakingDetail;
use App\Models\Stock\Collections\StockResultByWarehouseCollection;

class StockResultByWarehouseRepository
{
    /**
     * @var \Illuminate\Database\Connection
     */
    private $db;

    /**
     * @var \App\Models\Stock\StockResultByWarehouse
     */
    private $model;

    /**
     * @param  \Illuminate\Database\Connection $db
     * @param  \App\Models\Stock\StockResultByWarehouse $model
     * @return void
     */
    public function __construct(Connection $db, StockResultByWarehouse $model)
    {
        $this->db = $db;
        $this->model = $model;
    }

    /**
     * 収穫日ごとの在庫数を取得
     *
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Master\Species $species
     * @param  array $harvesting_date_term
     * @param  array $packaging_style
     * @param  \App\Models\Master\Warehouse $warehouse
     * @return \App\Models\Stock\Collections\StockResultByWarehouseCollection
     */
    public function getProductStocksPerHarvestingDate(
        Factory $factory,
        Species $species,
        array $harvesting_date_term,
        array $packaging_style,
        Warehouse $warehouse
    ): StockResultByWarehouseCollection {
        $product_allocation_query = $this->db->table('product_allocations')
            ->select([
                'product_allocations.factory_code',
                'product_allocations.species_code',
                'product_allocations.harvesting_date',
                'product_allocations.warehouse_code',
                'factory_products.number_of_heads',
                'factory_products.weight_per_number_of_heads',
                'factory_products.input_group',
                $this->db->raw('SUM(product_allocations.allocation_quantity) AS allocation_quantity')
            ])
            ->join('orders', 'orders.order_number', '=', 'product_allocations.order_number')
            ->join('factory_products', function ($join) use ($factory) {
                $join->on('factory_products.factory_code', '=', 'orders.factory_code')
                    ->on('factory_products.sequence_number', '=', 'orders.factory_product_sequence_number')
                    ->where('factory_products.factory_code', $factory->factory_code);
            })
            ->where('product_allocations.factory_code', $factory->factory_code)
            ->where('product_allocations.species_code', $species->species_code)
            ->where('product_allocations.warehouse_code', $warehouse->warehouse_code)
            ->groupBy([
                'product_allocations.factory_code',
                'product_allocations.species_code',
                'product_allocations.harvesting_date',
                'product_allocations.warehouse_code',
                'factory_products.number_of_heads',
                'factory_products.weight_per_number_of_heads',
                'factory_products.input_group'
            ]);

        $moving_stock_query = $this->db->table('stocks')
            ->select([
                'factory_code',
                'species_code',
                'harvesting_date',
                'warehouse_code',
                'number_of_heads',
                'weight_per_number_of_heads',
                'input_group',
                $this->db->raw('SUM(stock_quantity) AS stock_quantity')
            ])
            ->where('factory_code', $factory->factory_code)
            ->where('species_code', $species->species_code)
            ->where('warehouse_code', $warehouse->warehouse_code)
            ->whereNull('order_number')
            ->whereRaw('moving_complete_at > CURRENT_DATE()')
            ->groupBy([
                'factory_code',
                'species_code',
                'harvesting_date',
                'warehouse_code',
                'number_of_heads',
                'weight_per_number_of_heads',
                'input_group'
            ]);

        return $this->model
            ->select([
                'stock_result_by_warehouses.harvesting_date',
                'stock_result_by_warehouses.product_stock_quantity',
                'stock_result_by_warehouses.adjustment_quantity',
                $this->db->raw('COALESCE(moving_stocks.stock_quantity, 0) AS moving_stock_quantity'),
                $this->db->raw('COALESCE(product_allocations.allocation_quantity, 0) AS allocation_quantity'),
                $this->db->raw(
                    '(stock_result_by_warehouses.product_stock_quantity + '.
                    'stock_result_by_warehouses.adjustment_quantity - '.
                    'COALESCE(moving_stocks.stock_quantity, 0) - '.
                    'COALESCE(product_allocations.allocation_quantity, 0)) AS stock_quantity'
                )
            ])
            ->leftJoin(
                $this->db->raw("({$product_allocation_query->toSql()}) AS product_allocations"),
                function ($join) {
                    $join->on('product_allocations.factory_code', '=', 'stock_result_by_warehouses.factory_code')
                        ->on('product_allocations.species_code', '=', 'stock_result_by_warehouses.species_code')
                        ->on('product_allocations.harvesting_date', '=', 'stock_result_by_warehouses.harvesting_date')
                        ->on('product_allocations.warehouse_code', '=', 'stock_result_by_warehouses.warehouse_code')
                        ->on('product_allocations.number_of_heads', '=', 'stock_result_by_warehouses.number_of_heads')
                        ->on(
                            'product_allocations.weight_per_number_of_heads',
                            '=',
                            'stock_result_by_warehouses.weight_per_number_of_heads'
                        )
                        ->on('product_allocations.input_group', '=', 'stock_result_by_warehouses.input_group');
                }
            )
            ->leftJoin(
                $this->db->raw("({$moving_stock_query->toSql()}) AS moving_stocks"),
                function ($join) {
                    $join->on('moving_stocks.factory_code', '=', 'stock_result_by_warehouses.factory_code')
                        ->on('moving_stocks.species_code', '=', 'stock_result_by_warehouses.species_code')
                        ->on('moving_stocks.harvesting_date', '=', 'stock_result_by_warehouses.harvesting_date')
                        ->on('moving_stocks.warehouse_code', '=', 'stock_result_by_warehouses.warehouse_code')
                        ->on('moving_stocks.number_of_heads', '=', 'stock_result_by_warehouses.number_of_heads')
                        ->on(
                            'moving_stocks.weight_per_number_of_heads',
                            '=',
                            'moving_stocks.weight_per_number_of_heads'
                        )
                        ->on('moving_stocks.input_group', '=', 'stock_result_by_warehouses.input_group');
                }
            )
            ->setBindings(array_merge($product_allocation_query->getBindings(), $moving_stock_query->getBindings()))
            ->where('stock_result_by_warehouses.factory_code', $factory->factory_code)
            ->where('stock_result_by_warehouses.species_code', $species->species_code)
            ->whereBetween('stock_result_by_warehouses.harvesting_date', $harvesting_date_term)
            ->where('stock_result_by_warehouses.warehouse_code', $warehouse->warehouse_code)
            ->where('stock_result_by_warehouses.number_of_heads', $packaging_style['number_of_heads'] ?? null)
            ->where(
                'stock_result_by_warehouses.weight_per_number_of_heads',
                $packaging_style['weight_per_number_of_heads'] ?? null
            )
            ->where('stock_result_by_warehouses.input_group', $packaging_style['input_group'] ?? null)
            ->orderBy('stock_result_by_warehouses.harvesting_date', 'ASC')
            ->get();
    }

    /**
     * 在庫棚卸後の生産在庫の取得
     *
     * @param  \App\Models\Stock\StocktakingDetail $stocktaking_detail
     * @param  array $dates
     * @return \App\Models\Stock\Collections\StockResultByWarehouseCollection
     */
    public function getProductedStocks(
        StocktakingDetail $stocktaking_detail,
        array $dates
    ): StockResultByWarehouseCollection {
        return $this->model
            ->select([
                'harvesting_date',
                $this->db->raw('(product_stock_quantity - adjustment_quantity) AS stock_quantity')
            ])
            ->where('factory_code', $stocktaking_detail->factory_code)
            ->where('species_code', $stocktaking_detail->species_code)
            ->whereBetween('harvesting_date', [head($dates)->toDateString(), last($dates)->toDateString()])
            ->where('warehouse_code', $stocktaking_detail->warehouse_code)
            ->where('number_of_heads', $stocktaking_detail->number_of_heads)
            ->where('weight_per_number_of_heads', $stocktaking_detail->weight_per_number_of_heads)
            ->where('input_group', $stocktaking_detail->input_group)
            ->get();
    }

    /**
     * 製品化実績明細を基に倉庫別在庫実績データを登録
     *
     * @param  \App\Models\Shipment\ProductizedResultDetail $productized_result_detail
     * @param  \App\Models\Master\Warehouse $warehouse
     * @return \App\Models\Stock\StockResultByWarehouse
     */
    public function createProductedStock(
        ProductizedResultDetail $productized_result_detail,
        Warehouse $warehouse
    ): StockResultByWarehouse {
        return $this->model->create([
            'factory_code' => $productized_result_detail->factory_code,
            'species_code' => $productized_result_detail->species_code,
            'harvesting_date' => $productized_result_detail->harvesting_date->toDateString(),
            'warehouse_code' => $warehouse->warehouse_code,
            'number_of_heads' => $productized_result_detail->number_of_heads,
            'weight_per_number_of_heads' => $productized_result_detail->weight_per_number_of_heads,
            'input_group' => $productized_result_detail->input_group,
            'product_stock_quantity' => $productized_result_detail->product_quantity
        ]);
    }

    /**
     * 製品化実績明細を基に倉庫別在庫実績データを更新
     *
     * @param  \App\Models\Shipment\ProductizedResultDetail $productized_result_detail
     * @param  \App\Models\Master\Warehouse $warehouse
     * @return \App\Models\Stock\StockResultByWarehouse
     */
    public function updateProductedStock(ProductizedResultDetail $productized_result_detail): StockResultByWarehouse
    {
        $stock_result_by_warehouse = $this->model
            ->where('factory_code', $productized_result_detail->factory_code)
            ->where('species_code', $productized_result_detail->species_code)
            ->where('harvesting_date', $productized_result_detail->harvesting_date->toDateString())
            ->where('number_of_heads', $productized_result_detail->number_of_heads)
            ->where('weight_per_number_of_heads', $productized_result_detail->weight_per_number_of_heads)
            ->where('input_group', $productized_result_detail->input_group)
            ->where(function ($query) {
                $query->where('product_stock_quantity', '<>', 0)
                    ->orWhere(function ($query) {
                        $query->where('product_stock_quantity', 0)
                            ->where('adjustment_quantity', 0);
                    });
            })
            ->orderBy('product_stock_quantity', 'DESC')
            ->first();

        $stock_result_by_warehouse->product_stock_quantity = $productized_result_detail->product_quantity;
        $stock_result_by_warehouse->save();
        return $stock_result_by_warehouse;
    }

    /**
     * 在庫移動の結果として生じた倉庫別在庫実績データを登録
     *
     * @param  \App\Models\Stock\Stock $stock
     * @return \App\Models\Stock\StockResultByWarehouse
     */
    public function createMovedStock(Stock $stock): StockResultByWarehouse
    {
        return $this->model->create([
            'factory_code' => $stock->factory_code,
            'species_code' => $stock->species_code,
            'harvesting_date' => $stock->harvesting_date,
            'warehouse_code' => $stock->warehouse_code,
            'number_of_heads' => $stock->number_of_heads,
            'weight_per_number_of_heads' => $stock->weight_per_number_of_heads,
            'input_group' => $stock->input_group,
            'adjustment_quantity' => $stock->stock_quantity
        ]);
    }
}

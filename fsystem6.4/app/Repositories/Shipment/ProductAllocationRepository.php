<?php

declare(strict_types=1);

namespace App\Repositories\Shipment;

use Illuminate\Auth\AuthManager;
use Illuminate\Database\Connection;
use Cake\Chronos\Chronos;
use App\Models\Shipment\ProductAllocation;
use App\Models\Shipment\Collections\ProductAllocationCollection;
use App\Models\Stock\StocktakingDetail;

class ProductAllocationRepository
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
     * @var \App\Models\Shipment\ProductAllocation
     */
    private $model;

    /**
     * @param  \Illuminate\Auth\AuthManager $auth
     * @param  \Illuminate\Database\Connection $db
     * @param  \App\Models\Shipment\ProductAllocation $model
     * @return void
     */
    public function __construct(AuthManager $auth, Connection $db, ProductAllocation $model)
    {
        $this->auth = $auth;
        $this->db = $db;
        $this->model = $model;
    }

    /**
     * 顧客別の注文とそれに対する製品引当実績データを取得
     *
     * @param  array $params
     * @return \App\Models\Shipment\Collections\ProductAllocationCollection
     */
    public function getOrdersAndProductAllocationsPerCustomer(array $params): ProductAllocationCollection
    {
        return $this->model
            ->select([
                'orders.shipping_date',
                'product_allocations.harvesting_date',
                'orders.order_number',
                'orders.delivery_date',
                'orders.customer_code',
                'customers.customer_abbreviation',
                'orders.end_user_code',
                'orders.delivery_destination_code',
                'delivery_destinations.delivery_destination_abbreviation',
                'products.species_code',
                'species.species_name',
                'orders.product_code',
                'products.product_name',
                'product_allocations.allocation_quantity',
                $this->db->raw(
                    '(factory_products.weight_per_number_of_heads * product_allocations.allocation_quantity) '.
                    'AS shipping_weight'
                ),
                'orders.order_unit',
                'orders.order_amount',
                'orders.currency_code',
            ])
            ->join('orders', function ($join) {
                $join->on('product_allocations.order_number', '=', 'orders.order_number')
                    ->whereNotNull('orders.fixed_shipping_at');
            })
            ->join('customers', 'orders.customer_code', '=', 'customers.customer_code')
            ->join(
                'delivery_destinations',
                'orders.delivery_destination_code',
                '=',
                'delivery_destinations.delivery_destination_code'
            )
            ->join('products', 'orders.product_code', '=', 'products.product_code')
            ->join('species', 'products.species_code', '=', 'species.species_code')
            ->join('factory_products', function ($join) {
                $join->on('orders.factory_code', '=', 'factory_products.factory_code')
                    ->on('orders.factory_product_sequence_number', '=', 'factory_products.sequence_number');
            })
            ->where('orders.factory_code', $params['factory_code'])
            ->whereBetween('orders.delivery_date', array_values($params['delivery_date']))
            ->where(function ($query) use ($params) {
                if ($shipping_date_from = $params['shipping_date']['from']) {
                    $query->where('orders.shipping_date', '>=', $shipping_date_from);
                }
            })
            ->where(function ($query) use ($params) {
                if ($shipping_date_to = $params['shipping_date']['to']) {
                    $query->where('orders.shipping_date', '<=', $shipping_date_to);
                }
            })
            ->where(function ($query) use ($params) {
                if ($customer_code = $params['customer_code']) {
                    $query->where('orders.customer_code', $customer_code);
                }
            })
            ->where(function ($query) use ($params) {
                if ($end_user_code = $params['end_user_code']) {
                    $query->where('orders.end_user_code', $end_user_code);
                }
            })
            ->orderBy('orders.shipping_date', 'ASC')
            ->orderBy('product_allocations.harvesting_date', 'ASC')
            ->orderBy('orders.order_number', 'ASC')
            ->get();
    }

    /**
     * 在庫棚卸後の引当数量を取得
     *
     * @param  \App\Models\Stock\StocktakingDetail
     * @param  array $date
     * @return \App\Models\Shipment\Collections\ProductAllocationCollection
     */
    public function getAllocatedStocksPerStockStyle(
        StocktakingDetail $stock_style,
        array $dates
    ): ProductAllocationCollection {
        return $this->model
            ->select([
                $this->db->raw('DATE(product_allocations.last_allocated_at) AS allocated_on'),
                $this->db->raw('SUM(product_allocations.allocation_quantity) AS allocation_quantity')
            ])
            ->join('orders', 'product_allocations.order_number', '=', 'orders.order_number')
            ->join('factory_products', function ($join) {
                $join->on('factory_products.factory_code', '=', 'orders.factory_code')
                    ->on('factory_products.sequence_number', '=', 'orders.factory_product_sequence_number');
            })
            ->where('product_allocations.factory_code', $stock_style->factory_code)
            ->where('product_allocations.species_code', $stock_style->species_code)
            ->where('product_allocations.warehouse_code', $stock_style->warehouse_code)
            ->where('factory_products.number_of_heads', $stock_style->number_of_heads)
            ->where('factory_products.weight_per_number_of_heads', $stock_style->weight_per_number_of_heads)
            ->where('factory_products.input_group', $stock_style->input_group)
            ->where(function ($query) use ($stock_style) {
                if ($stock_style->hasAllocated()) {
                    $query->where('factory_products.number_of_cases', $stock_style->number_of_cases);
                }
            })
            ->whereBetween('product_allocations.last_allocated_at', [
                head($dates)->startOfDay()->toDatetimeString(),
                last($dates)->endOfDay()->toDatetimeString()
            ])
            ->groupBy($this->db->raw('DATE(product_allocations.last_allocated_at)'))
            ->get();
    }

    /**
     * 製品引当データの登録
     *
     * @param  array $product_allocations
     * @return void
     */
    public function insertProductAllocations(array $product_allocations): void
    {
        $last_allocated_by = $this->auth->id();
        $last_allocated_at = Chronos::now()->format('Y-m-d H:i:s');

        $this->model->insert(array_map(function ($pa) use ($last_allocated_by, $last_allocated_at) {
            return array_merge(
                $pa,
                array_fill_keys(['last_allocated_by', 'created_by', 'updated_by'], $last_allocated_by),
                array_fill_keys(['last_allocated_at', 'created_at', 'updated_at'], $last_allocated_at)
            );
        }, $product_allocations));
    }
}

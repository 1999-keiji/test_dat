<?php

declare(strict_types=1);

namespace App\Repositories\Stock;

use Illuminate\Database\Connection;
use App\Exceptions\OptimisticLockException;
use App\Models\Order\Order;
use App\Models\Order\ReturnedProduct;
use App\Models\Stock\Stock;
use App\Models\Stock\StockHistory;
use App\Models\Stock\StockResultByWarehouse;
use App\Models\Stock\Collections\StockCollection;
use App\ValueObjects\Date\HarvestingDate;
use App\ValueObjects\Enum\AllocationStatus;
use App\ValueObjects\Enum\DisposalStatus;
use App\ValueObjects\Enum\ProcessClass;
use App\ValueObjects\Enum\SlipType;
use App\ValueObjects\Enum\SlipStatusType;
use App\ValueObjects\Enum\StockStatus;

class StockRepository
{
    /**
     * @var \Illuminate\Database\Connection
     */
    private $db;

    /**
     * @var \App\Models\Stock\Stock
     */
    private $model;

    /**
     * @var \App\Models\Stock\StockHistory
     */
    private $stock_history_model;

    /**
     * @param  \Illuminate\Database\Connection $db
     * @param  \App\Models\Stock\Stock $model
     * @param  \App\Models\Stock\StockHistory $stock_history_model
     * @return void
     */
    public function __construct(Connection $db, Stock $model, StockHistory $stock_history_model)
    {
        $this->db = $db;
        $this->model = $model;
        $this->stock_history_model = $stock_history_model;
    }

    /**
     * 在庫データの登録
     *
     * @param  array $params
     * @return \App\Models\Stock\Stock
     */
    public function create(array $params): Stock
    {
        return $this->model->create($params);
    }

    /**
     * 在庫データの更新
     *
     * @param  \App\Models\Stock\Stock $stock
     * @return \App\Models\Stock\Stock
     */
    public function update(Stock $stock, array $params): Stock
    {
        $stock->fill($params)->save();

        $stock->setRelations([]);
        return $stock;
    }

    /**
     * 在庫データの検索
     *
     * @param  int $stock_id
     * @return \App\Models\Stock\Stock
     */
    public function find(int $stock_id): Stock
    {
        return $this->model->find($stock_id);
    }

    /**
     * 未引当在庫データの取得
     *
     * @param  array $params
     * @return \App\Models\Stock\Collections\StockCollection
     */
    public function findNotAllocatedStocks(array $params): StockCollection
    {
        return $this->model
            ->select('*')
            ->where('factory_code', $params['factory_code'])
            ->where('species_code', $params['species_code'])
            ->where('harvesting_date', $params['harvesting_date'])
            ->where('number_of_heads', $params['number_of_heads'])
            ->where('weight_per_number_of_heads', $params['weight_per_number_of_heads'])
            ->where('input_group', $params['input_group'])
            ->whereNull('order_number')
            ->where(function ($query) use ($params) {
                if (array_key_exists('warehouse_code', $params)) {
                    $query->where('warehouse_code', $params['warehouse_code']);
                }
            })
            ->where(function ($query) {
                $query->whereNull('moving_complete_at')
                    ->orWhereRaw('moving_complete_at <= CURRENT_DATE()');
            })
            ->where(function ($query) use ($params) {
                if (array_key_exists('stock_status', $params)) {
                    $query->where('stock_status', $params['stock_status']);
                }
            })
            ->get();
    }

    /**
     * 在庫サマリーデータの取得
     *
     * @param  array $params
     * @param  array $order
     * @return \App\Models\Stock\Collections\StockCollection
     */
    public function searchStockSummary(array $params, array $order): StockCollection
    {
        $sub_query = $this->db->table('orders')
            ->select([
                'orders.order_number',
                'orders.factory_code',
                $this->db->raw(
                    "(CASE WHEN orders.fixed_shipping_at IS NOT NULL AND orders.printing_shipping_date <= CURRENT_DATE()
                    AND orders.delivery_date > CURRENT_DATE() THEN '積送在庫'
                    ELSE NULL END) AS storage_warehouse"
                ),
                'orders.fixed_shipping_at',
                'stocks.warehouse_code',
                $this->db->raw("DATE_FORMAT(orders.delivery_date, '%Y/%m/%d') AS delivery_date"),
                $this->db->raw("DATE_FORMAT(orders.printing_shipping_date, '%Y/%m/%d') AS shipping_date")
            ])
            ->join('stocks', 'orders.order_number', '=', 'stocks.order_number')
            ->join('warehouses', 'stocks.warehouse_code', '=', 'warehouses.warehouse_code')
            ->where('orders.process_class', '<>', ProcessClass::CANCEL_PROCESS)
            ->where('orders.slip_type', SlipType::NORMAL_SLIP)
            ->where('orders.slip_status_type', '<>', SlipStatusType::RELATION_TEMP_ORDER)
            ->where('orders.factory_cancel_flag', false)
            ->where(function ($query) {
                $query->whereNull('orders.fixed_shipping_at')
                    ->orWhere(function ($query) {
                        $query->whereRaw('orders.delivery_date > CURRENT_DATE')
                            ->whereNotNull('orders.fixed_shipping_at');
                    });
            });

        $query = $this->model
            ->select([
                'stocks.factory_code',
                'factories.factory_abbreviation',
                'stocks.warehouse_code',
                $this->db->raw(
                    "(CASE WHEN sub_query.storage_warehouse IS NULL THEN warehouses.warehouse_abbreviation
                    ELSE '積送中' END) AS storage_warehouse"
                ),
                'species.species_code',
                'species.species_name',
                $this->db->raw('SUM(stocks.stock_weight - stocks.disposal_weight) AS stock_weight'),
                $this->db->raw(
                    "(CASE WHEN sub_query.shipping_date IS NULL THEN '未引当' ELSE '引当済' END) AS allocation_status"
                )
            ])
            ->join('factories', 'stocks.factory_code', '=', 'factories.factory_code')
            ->join('warehouses', 'stocks.warehouse_code', '=', 'warehouses.warehouse_code')
            ->join('species', 'stocks.species_code', '=', 'species.species_code')
            ->leftJoin(
                $this->db->raw("({$sub_query->distinct()->toSql()}) AS sub_query"),
                function ($join) {
                    $join->on('stocks.order_number', '=', 'sub_query.order_number');
                }
            )
            ->setBindings($sub_query->getBindings())
            ->leftjoin('orders', 'stocks.order_number', '=', 'orders.order_number')
            ->where('stocks.stock_quantity', '>', 0)
            ->where('stocks.factory_code', $params['factory_code'])
            ->where(function ($query) use ($params) {
                if (! is_null($params['warehouse_code'] ?? null)) {
                    $query->where('stocks.warehouse_code', $params['warehouse_code']);
                }
            })
            ->where(function ($query) use ($params) {
                if (! is_null($params['species_code'] ?? null)) {
                    $query->where('stocks.species_code', $params['species_code']);
                }
            })
            ->where(function ($query) use ($params) {
                if (! is_null($params['allocation_status'])) {
                    if ((int)$params['allocation_status'] === AllocationStatus::ALLOCATED) {
                        $query->whereNotNull('sub_query.factory_code');
                    }
                    if ((int)$params['allocation_status'] === AllocationStatus::UNALLOCATED) {
                        $query->whereNull('sub_query.factory_code');
                    }
                }
            })
            ->where(function ($query) {
                $query->whereNull('orders.fixed_shipping_at')
                    ->orWhere(function ($query) {
                        $query->whereRaw('orders.delivery_date > CURRENT_DATE')
                            ->whereNotNull('orders.fixed_shipping_at');
                    });
            })
            ->groupBy(
                'stocks.factory_code',
                'stocks.warehouse_code',
                'stocks.species_code',
                'sub_query.storage_warehouse',
                'allocation_status'
            );

        if (count($order) === 0) {
            $query->orderBy('stocks.factory_code', 'ASC')
                ->orderBy('stocks.warehouse_code', 'ASC')
                ->orderBy('stocks.species_code', 'ASC');
        }
        if (count($order) !== 0) {
            $query->orderBy($order['sort'], $order['order']);
        }

        return $query->get();
    }

    /**
     * 在庫データの検索
     *
     * @param  array $params
     * @param  array $order
     * @param  bool $is_paginated
     * @return \Illuminate\Pagination\LengthAwarePaginator|\App\Models\Stock\Collections\StockCollection
     */
    public function searchStocks(array $params, array $order = [], bool $is_paginated = false)
    {
        $sub_query = $this->db->table('orders')
            ->select([
                'orders.order_number',
                'orders.factory_code',
                'orders.delivery_date',
                'orders.delivery_destination_code',
                'orders.fixed_shipping_at',
                $this->db->raw("DATE_FORMAT(orders.printing_shipping_date, '%Y/%m/%d') AS shipping_date"),
            ])
            ->join('stocks', 'orders.order_number', '=', 'stocks.order_number')
            ->join('warehouses', 'warehouses.warehouse_code', '=', 'stocks.warehouse_code')
            ->where('orders.process_class', '<>', ProcessClass::CANCEL_PROCESS)
            ->where('orders.slip_type', SlipType::NORMAL_SLIP)
            ->where('orders.slip_status_type', '<>', SlipStatusType::RELATION_TEMP_ORDER)
            ->where('orders.factory_cancel_flag', false);

        $query = $this->model
            ->select([
                'stocks.order_number',
                'stocks.stock_id',
                'stocks.factory_code',
                'stocks.warehouse_code',
                'stocks.species_code',
                'stocks.stock_status',
                'stocks.number_of_heads',
                'stocks.weight_per_number_of_heads',
                'stocks.input_group',
                'stocks.stock_quantity',
                'stocks.stock_weight',
                'stocks.disposal_quantity',
                'stocks.disposal_weight',
                'species.species_name',
                $this->db->raw("DATE_FORMAT(stocks.harvesting_date, '%Y/%m/%d') AS harvesting_date"),
                $this->db->raw(
                    '(CASE WHEN sub_query.shipping_date <= CURRENT_DATE AND '.
                    'sub_query.delivery_date >= CURRENT_DATE AND '.
                    "sub_query.fixed_shipping_at IS NOT NULL THEN '積送中' ".
                    'WHEN stocks.moving_start_at <> stocks.moving_complete_at AND '.
                    '(stocks.moving_start_at <= CURRENT_DATE AND stocks.moving_start_at < stocks.moving_complete_at) '.
                    "THEN CONCAT('(倉庫間移動)', before_warehouses.warehouse_abbreviation) ".
                    'WHEN stocks.moving_start_at <> stocks.moving_complete_at AND '.
                    'stocks.moving_start_at > CURRENT_DATE THEN '.
                    "CONCAT('(倉庫間移動)', before_warehouses.warehouse_abbreviation) ".
                    'ELSE warehouses.warehouse_abbreviation END) as storage_warehouse '
                ),
                $this->db->raw(
                    '(CASE WHEN stocks.moving_start_at <> stocks.moving_complete_at AND '.
                    '(stocks.moving_start_at <= CURRENT_DATE AND stocks.moving_start_at < stocks.moving_complete_at) '.
                    'AND stocks.order_number IS NULL '.
                    "THEN DATE_FORMAT(stocks.moving_complete_at, '%Y/%m/%d') ".
                    "ELSE DATE_FORMAT(sub_query.delivery_date, '%Y/%m/%d') END) ".
                    'AS delivery_date '
                ),
                $this->db->raw(
                    '(CASE WHEN stocks.moving_start_at <> stocks.moving_complete_at AND '.
                    '(stocks.moving_start_at <= CURRENT_DATE AND stocks.moving_start_at < stocks.moving_complete_at) '.
                    'AND stocks.order_number IS NULL '.
                    'THEN DATEDIFF(stocks.moving_complete_at, stocks.moving_start_at) '.
                    'ELSE delivery_warehouses.delivery_lead_time END) '.
                    'AS delivery_lead_time '
                ),
                $this->db->raw(
                    '(CASE WHEN stocks.moving_start_at <> stocks.moving_complete_at AND '.
                    '(stocks.moving_start_at <= CURRENT_DATE AND stocks.moving_start_at < stocks.moving_complete_at) '.
                    'AND stocks.order_number IS NULL '.
                    'THEN warehouses.warehouse_abbreviation '.
                    'ELSE delivery_destinations.delivery_destination_abbreviation END) '.
                    'as delivery_destination_abbreviation '
                ),
            ])
            ->join('warehouses as warehouses', 'stocks.warehouse_code', '=', 'warehouses.warehouse_code')
            ->leftjoin(
                'warehouses as before_warehouses',
                'stocks.before_warehouse_code',
                '=',
                'before_warehouses.warehouse_code'
            )
            ->join('species', 'stocks.species_code', '=', 'species.species_code')
            ->leftJoin(
                $this->db->raw("({$sub_query->distinct()->toSql()}) AS sub_query"),
                function ($join) {
                    $join->on('stocks.order_number', '=', 'sub_query.order_number');
                }
            )
            ->setBindings($sub_query->getBindings())
            ->leftJoin(
                'delivery_destinations',
                'sub_query.delivery_destination_code',
                '=',
                'delivery_destinations.delivery_destination_code'
            )
            ->leftJoin('delivery_warehouses', function ($join) {
                $join->on('stocks.warehouse_code', '=', 'delivery_warehouses.warehouse_code')
                    ->on('sub_query.delivery_destination_code', '=', 'delivery_warehouses.delivery_destination_code');
            })
            ->leftjoin('orders', 'stocks.order_number', '=', 'orders.order_number')
            ->where('stocks.factory_code', $params['factory_code'])
            ->where(function ($query) {
                $query->where('stocks.stock_quantity', '>', 0)
                    ->whereRaw('stocks.stock_quantity <> stocks.disposal_quantity');
            })
            ->where(function ($query) use ($params) {
                if (! is_null($params['warehouse_code'] ?? null)) {
                    $query->where('stocks.warehouse_code', $params['warehouse_code']);
                }
            })
            ->where(function ($query) use ($params) {
                if (! is_null($params['stock_status'] ?? null)) {
                    $query->where('stocks.stock_status', $params['stock_status']);
                }
            })
            ->where(function ($query) use ($params) {
                if (! is_null($params['species_code'] ?? null)) {
                    $query->where('stocks.species_code', $params['species_code']);
                }
            })
            ->where(function ($query) use ($params) {
                if (! is_null($params['number_of_heads'] ?? null)) {
                    $query->where('stocks.number_of_heads', $params['number_of_heads']);
                }
                if (! is_null($params['weight_per_number_of_heads'] ?? null)) {
                    $query->where('stocks.weight_per_number_of_heads', $params['weight_per_number_of_heads']);
                }
                if (! is_null($params['input_group'] ?? null)) {
                    $query->where('stocks.input_group', $params['input_group']);
                }
            })
            ->where(function ($query) use ($params) {
                if (! is_null($params['harvesting_date_from'] ?? null)) {
                    $query->where('stocks.harvesting_date', '>=', $params['harvesting_date_from']);
                }
                if (! is_null($params['harvesting_date_to'] ?? null)) {
                    $query->where('stocks.harvesting_date', '<=', $params['harvesting_date_to']);
                }
            })
            ->where(function ($query) use ($params) {
                if (! is_null($params['allocation_status'])) {
                    if ((int)$params['allocation_status'] === AllocationStatus::ALLOCATED) {
                        $query->whereNotNull('sub_query.factory_code');
                    }
                    if ((int)$params['allocation_status'] === AllocationStatus::UNALLOCATED) {
                        $query->whereNull('sub_query.factory_code');
                    }
                }
            })
            ->where(function ($query) use ($params) {
                if (! is_null($params['delivery_destination_code'] ?? null)) {
                    $query->where('sub_query.delivery_destination_code', $params['delivery_destination_code']);
                }
            })
            ->where(function ($query) use ($params) {
                if (! is_null($params['delivery_date_from'] ?? null)) {
                    $query->whereDate('sub_query.delivery_date', '>=', $params['delivery_date_from']);
                }
                if (! is_null($params['delivery_date_to'] ?? null)) {
                    $query->whereDate('sub_query.delivery_date', '<=', $params['delivery_date_to']);
                }
            })
            ->where(function ($query) use ($params) {
                if ((int)$params['disposal_status'] === DisposalStatus::STOCK) {
                    $query->where('stocks.disposal_quantity', 0);
                }
                if ((int)$params['disposal_status'] === DisposalStatus::PART_DISPOSAL) {
                    $query->where('stocks.disposal_quantity', '>=', 0)
                        ->whereRaw('stocks.stock_quantity <> stocks.disposal_quantity');
                }
            })
            ->where(function ($query) {
                $query->whereNull('orders.fixed_shipping_at')
                    ->orWhere(function ($query) {
                        $query->whereRaw('orders.delivery_date > CURRENT_DATE')
                            ->whereNotNull('orders.fixed_shipping_at');
                    });
            });

        if (count($order) === 0) {
            $query->orderBy('stocks.warehouse_code', 'ASC')
                ->orderBy('stocks.stock_status', 'DESC')
                ->orderBy('stocks.species_code', 'ASC')
                ->orderBy('stocks.harvesting_date', 'ASC')
                ->orderBy('sub_query.delivery_destination_code', 'ASC')
                ->orderBy('sub_query.delivery_date', 'ASC')
                ->orderBy('stocks.number_of_heads')
                ->orderBy('stocks.weight_per_number_of_heads')
                ->orderBy('stocks.input_group');
        }
        if (count($order) !== 0) {
            $query->orderBy($order['sort'], $order['order']);
        }

        return $is_paginated ? $query->paginate() : $query->get();
    }

    /**
     * 廃棄在庫の取得
     *
     * @param  array $params
     * @return \App\Models\Stock\Collections\StockCollection
     */
    public function searchDisposedStocks(array $params): StockCollection
    {
        return $this->model
            ->select([
                'stocks.stock_id',
                'stocks.species_code',
                'species.species_name',
                'stocks.number_of_heads',
                'stocks.weight_per_number_of_heads',
                'stocks.input_group',
                'stocks.harvesting_date',
                'stocks.stock_status',
                'stocks.stock_quantity',
                'stocks.stock_weight',
                'stocks.disposal_quantity',
                'stocks.disposal_weight',
                'stocks.disposal_at',
                'stocks.disposal_remark',
                'stocks.updated_at',
            ])
            ->join('species', 'species.species_code', '=', 'stocks.species_code')
            ->where('stocks.factory_code', $params['factory_code'])
            ->where('stocks.warehouse_code', $params['warehouse_code'])
            ->where(function ($query) use ($params) {
                $harvesting_month = HarvestingDate::createFromYearMonth($params['harvesting_month']);
                $query->whereBetween('stocks.harvesting_date', [
                    $harvesting_month->startOfMonth()->format('Y-m-d'),
                    $harvesting_month->endOfMonth()->format('Y-m-d')
                ]);
            })
            ->where(function ($query) use ($params) {
                if (! is_null($params['species_code'] ?? null)) {
                    $query->where('stocks.species_code', $params['species_code']);
                }
            })
            ->where(function ($query) use ($params) {
                if (! is_null($params['number_of_heads'] ?? null)) {
                    $query->where('stocks.number_of_heads', $params['number_of_heads']);
                }
                if (! is_null($params['weight_per_number_of_heads'] ?? null)) {
                    $query->where('stocks.weight_per_number_of_heads', $params['weight_per_number_of_heads']);
                }
                if (! is_null($params['input_group'] ?? null)) {
                    $query->where('stocks.input_group', $params['input_group']);
                }
            })
            ->whereRaw('stocks.stock_quantity > 0')
            ->whereNull('stocks.order_number')
            ->where(function ($query) use ($params) {
                if (! is_null($params['disposal_status'])) {
                    if ((int)$params['disposal_status'] === DisposalStatus::STOCK) {
                        $query->whereRaw('stocks.stock_quantity <> stocks.disposal_quantity');
                    }
                    if ((int)$params['disposal_status'] === DisposalStatus::DISPOSAL) {
                        $query->whereRaw('stocks.disposal_quantity <> 0');
                    }
                }
            })
            ->orderBy('stocks.species_code', 'ASC')
            ->orderBy('stocks.number_of_heads', 'ASC')
            ->orderBy('stocks.weight_per_number_of_heads', 'ASC')
            ->orderBy('stocks.input_group', 'ASC')
            ->orderBy('stocks.harvesting_date', 'ASC')
            ->get();
    }

    /**
     * 移動中の在庫データの取得
     *
     * @return \App\Models\Stock\Collections\StockCollection
     */
    public function searchMovingStocks(): StockCollection
    {
        return $this->model
            ->select('*')
            ->whereRaw('(moving_start_at <> moving_complete_at)')
            ->whereRaw('(moving_start_at <= CURRENT_DATE AND moving_complete_at > CURRENT_DATE)')
            ->get();
    }

    /**
     * 在庫状況の検索
     *
     * @param  array $params
     * @param  string $base_date
     * @return \App\Models\Stock\Collections\StockCollection
     */
    public function searchStockStates(array $params, ?string $base_date = null): StockCollection
    {
        if (! is_null($base_date)) {
            $base_date = "'{$base_date}'";
        }
        if (is_null($base_date)) {
            $base_date = 'CURRENT_DATE';
        }

        $order_allocations_query = $this->db->table('product_allocations')
            ->select([
                'product_allocations.order_number',
                'product_allocations.harvesting_date',
                'orders.factory_product_sequence_number',
                'factory_products.number_of_cases',
                'orders.delivery_destination_code',
                'orders.delivery_date',
                'orders.printing_shipping_date AS shipping_date',
                'orders.fixed_shipping_at'
            ])
            ->join('orders', 'orders.order_number', '=', 'product_allocations.order_number')
            ->join('factory_products', function ($join) {
                $join->on('factory_products.factory_code', '=', 'orders.factory_code')
                    ->on('factory_products.sequence_number', '=', 'orders.factory_product_sequence_number');
            })
            ->where(function ($query) use ($params) {
                if (! is_null($params['factory_code'] ?? null)) {
                    $query->where('orders.factory_code', $params['factory_code']);
                }
            })
            ->where('orders.process_class', '<>', ProcessClass::CANCEL_PROCESS)
            ->where('orders.slip_type', SlipType::NORMAL_SLIP)
            ->where('orders.slip_status_type', '<>', SlipStatusType::RELATION_TEMP_ORDER)
            ->where('orders.factory_cancel_flag', false);

        return $this->model
            ->select([
                'stocks.stock_id',
                'stocks.factory_code',
                'factories.factory_abbreviation',
                'stocks.warehouse_code',
                $this->db->raw(sprintf(
                    '(CASE WHEN order_allocations.shipping_date <= %s AND '.
                    'order_allocations.delivery_date > %s AND '.
                    "order_allocations.fixed_shipping_at IS NOT NULL THEN '積送中' ".
                    'WHEN stocks.moving_complete_at IS NOT NULL AND stocks.moving_complete_at > %s THEN ( '.
                    "CASE WHEN stocks.moving_start_at <= %s THEN '倉庫間移動中' ".
                    'ELSE departure_warehouses.warehouse_abbreviation END'.
                    ') ELSE warehouses.warehouse_abbreviation END) as warehouse_abbreviation',
                    $base_date,
                    $base_date,
                    $base_date,
                    $base_date
                )),
                'stocks.stock_status',
                'stocks.species_code',
                'species.species_name AS species_abbreviation',
                'stocks.number_of_heads',
                'stocks.weight_per_number_of_heads',
                'stocks.input_group',
                'stocks.stock_quantity AS original_stock_quantity',
                'stocks.disposal_quantity',
                $this->db->raw('(stocks.stock_quantity - stocks.disposal_quantity) AS stock_quantity'),
                $this->db->raw('(stocks.stock_quantity * stocks.number_of_heads) AS original_stock_number'),
                $this->db->raw(
                    '((stocks.stock_quantity - stocks.disposal_quantity) * stocks.number_of_heads) AS stock_number'
                ),
                'stocks.stock_weight AS original_stock_weight',
                'disposal_weight',
                $this->db->raw('(stocks.stock_weight - stocks.disposal_weight) AS stock_weight'),
                $this->db->raw("DATE_FORMAT(stocks.harvesting_date, '%Y/%m/%d') AS harvesting_date"),
                'stocks.order_number',
                'order_allocations.delivery_destination_code',
                'delivery_destinations.delivery_destination_abbreviation',
                'order_allocations.factory_product_sequence_number',
                'order_allocations.number_of_cases',
                $this->db->raw("DATE_FORMAT(order_allocations.delivery_date, '%Y/%m/%d') AS delivery_date"),
                $this->db->raw("DATE_FORMAT(order_allocations.shipping_date, '%Y/%m/%d') AS shipping_date"),
                'order_allocations.fixed_shipping_at',
                'delivery_warehouses.delivery_lead_time',
                'stocks.before_warehouse_code',
                'stocks.moving_start_at',
                'stocks.moving_complete_at'
            ])
            ->join('factories', 'factories.factory_code', '=', 'stocks.factory_code')
            ->join('warehouses', 'warehouses.warehouse_code', '=', 'stocks.warehouse_code')
            ->leftJoin(
                'warehouses AS departure_warehouses',
                'departure_warehouses.warehouse_code',
                '=',
                'stocks.before_warehouse_code'
            )
            ->join('species', 'species.species_code', '=', 'stocks.species_code')
            ->leftJoin(
                $this->db->raw("({$order_allocations_query->toSql()}) AS order_allocations"),
                function ($join) {
                    $join->on('order_allocations.order_number', '=', 'stocks.order_number')
                        ->on('order_allocations.harvesting_date', '=', 'stocks.harvesting_date');
                }
            )
            ->setBindings($order_allocations_query->getBindings())
            ->leftJoin(
                'delivery_destinations',
                'delivery_destinations.delivery_destination_code',
                '=',
                'order_allocations.delivery_destination_code'
            )
            ->leftJoin('delivery_warehouses', function ($join) {
                $join
                    ->on(
                        'delivery_warehouses.delivery_destination_code',
                        '=',
                        'order_allocations.delivery_destination_code'
                    )
                    ->on('delivery_warehouses.warehouse_code', '=', 'stocks.warehouse_code');
            })
            ->where(function ($query) use ($params) {
                if (! is_null($params['factory_code'] ?? null)) {
                    $query->where('stocks.factory_code', $params['factory_code']);
                }
            })
            ->where('stocks.stock_quantity', '>', 0)
            ->whereRaw('stocks.stock_quantity <> stocks.disposal_quantity')
            ->where(function ($query) use ($params) {
                if (! is_null($params['warehouse_code'] ?? null)) {
                    $query->where('stocks.warehouse_code', $params['warehouse_code']);
                }
            })
            ->where(function ($query) use ($params) {
                if (! is_null($params['stock_status'] ?? null)) {
                    $query->where('stocks.stock_status', $params['stock_status']);
                }
            })
            ->where(function ($query) use ($params) {
                if (! is_null($params['species_code'] ?? null)) {
                    $query->where('stocks.species_code', $params['species_code']);
                }
            })
            ->where(function ($query) use ($params) {
                if (! is_null($params['number_of_heads'] ?? null)) {
                    $query->where('stocks.number_of_heads', $params['number_of_heads']);
                }
                if (! is_null($params['weight_per_number_of_heads'] ?? null)) {
                    $query->where('stocks.weight_per_number_of_heads', $params['weight_per_number_of_heads']);
                }
                if (! is_null($params['input_group'] ?? null)) {
                    $query->where('stocks.input_group', $params['input_group']);
                }
            })
            ->where(function ($query) use ($base_date) {
                $query->whereNull('order_allocations.fixed_shipping_at')
                    ->orWhere(function ($query) use ($base_date) {
                        $query->whereRaw(sprintf('order_allocations.delivery_date > %s', $base_date))
                            ->whereNotNull('order_allocations.fixed_shipping_at');
                    });
            })
            ->orderBy('stocks.factory_code', 'ASC')
            ->orderBy('stocks.species_code', 'ASC')
            ->orderBy('stocks.warehouse_code', 'ASC')
            ->orderBy('stocks.number_of_heads', 'ASC')
            ->orderBy('stocks.weight_per_number_of_heads', 'ASC')
            ->orderBy('stocks.input_group', 'ASC')
            ->orderBy('stocks.harvesting_date', 'ASC')
            ->orderBy('order_allocations.delivery_destination_code', 'ASC')
            ->orderBy('order_allocations.delivery_date', 'ASC')
            ->get();
    }

    /**
     * 日付切り替え時点の繰越在庫を取得
     *
     * @return \App\Models\Stock\Collections\StockCollection
     */
    public function getCarryOveredStocksOnEndOfDay(): StockCollection
    {
        return $this->model
            ->select([
                'factory_code',
                'species_code',
                'number_of_heads',
                'weight_per_number_of_heads',
                'input_group',
                $this->db->raw(
                    'SUM(stock_quantity - (CASE WHEN (disposal_at IS NOT NULL AND disposal_at < CURRENT_DATE) '.
                    'THEN disposal_quantity ELSE 0 END)) AS carry_over_stock_quantity'
                ),
                $this->db->raw(
                    'SUM(stock_weight - (CASE WHEN (disposal_at IS NOT NULL AND disposal_at < CURRENT_DATE) '.
                    'THEN disposal_weight ELSE 0 END)) AS carry_over_stock_weight'
                )
            ])
            ->whereNull('order_number')
            ->groupBy([
                'factory_code',
                'species_code',
                'number_of_heads',
                'weight_per_number_of_heads',
                'input_group'
            ])
            ->get();
    }

    /**
     * 製品化実績を在庫として登録
     *
     * @param  \App\Models\Stock\StockResultByWarehouse $stock_result_by_warehouse
     * @return \App\Models\Stock\Stock $stock
     */
    public function createProductedStock(StockResultByWarehouse $stock_result_by_warehouse): Stock
    {
        $stock = $this->create([
            'factory_code' => $stock_result_by_warehouse->factory_code,
            'warehouse_code' => $stock_result_by_warehouse->warehouse_code,
            'species_code' => $stock_result_by_warehouse->species_code,
            'harvesting_date' => $stock_result_by_warehouse->harvesting_date,
            'number_of_heads' => $stock_result_by_warehouse->number_of_heads,
            'weight_per_number_of_heads' => $stock_result_by_warehouse->weight_per_number_of_heads,
            'input_group' => $stock_result_by_warehouse->input_group,
            'stock_quantity' => $stock_result_by_warehouse->product_stock_quantity,
            'stock_weight' => $stock_result_by_warehouse->product_stock_quantity
                * $stock_result_by_warehouse->weight_per_number_of_heads
        ]);

        $this->stock_history_model->create([
            'factory_code' => $stock->factory_code,
            'screen' => config('constant.stock.screens.productized_results_input'),
            'warehouse_code' => $stock->warehouse_code,
            'species_code' => $stock->species_code,
            'stock_status' => StockStatus::NORMAL,
            'number_of_heads' => $stock->number_of_heads,
            'weight_per_number_of_heads' => $stock->weight_per_number_of_heads,
            'input_group' => $stock->input_group,
            'harvesting_date' => $stock->harvesting_date,
            'expiration_date' => $stock->getExpiredOn()->format('Y-m-d'),
            'stock_quantity' => $stock->stock_quantity,
            'transistion_quantity' => $stock->stock_quantity,
            'stock_id' => $stock->stock_id
        ]);

        return $stock;
    }

    /**
     * 製品化数量の減算
     *
     * @param  \App\Models\Stock\Stock $stock
     * @return void
     */
    public function subtractProductedStock(Stock $stock): void
    {
        if ($stock->getDiffOfStockQuantity() < 0) {
            $this->stock_history_model->create([
                'factory_code' => $stock->factory_code,
                'screen' => config('constant.stock.screens.productized_results_input'),
                'warehouse_code' => $stock->warehouse_code,
                'species_code' => $stock->species_code,
                'stock_status' => $stock->stock_status,
                'number_of_heads' => $stock->number_of_heads,
                'weight_per_number_of_heads' => $stock->weight_per_number_of_heads,
                'input_group' => $stock->input_group,
                'harvesting_date' => $stock->harvesting_date,
                'expiration_date' => $stock->getExpiredOn()->format('Y-m-d'),
                'stock_quantity' => $stock->stock_quantity,
                'transistion_quantity' => $stock->getDiffOfStockQuantity(),
                'stock_id' => $stock->stock_id
            ]);

            $stock->save();
        }
    }

    /**
     * 製品化実績入力状態の解除
     *
     * @param  \App\Models\Stock\Stock $stock
     * @return void
     */
    public function resetProductedStock(Stock $stock): void
    {
        $this->stock_history_model->create([
            'factory_code' => $stock->factory_code,
            'screen' => config('constant.stock.screens.productized_results_input'),
            'warehouse_code' => $stock->warehouse_code,
            'species_code' => $stock->species_code,
            'stock_status' => $stock->stock_status,
            'number_of_heads' => $stock->number_of_heads,
            'weight_per_number_of_heads' => $stock->weight_per_number_of_heads,
            'input_group' => $stock->input_group,
            'harvesting_date' => $stock->harvesting_date,
            'expiration_date' => $stock->getExpiredOn()->format('Y-m-d'),
            'stock_quantity' => 0,
            'transistion_quantity' => $stock->stock_quantity * -1,
            'stock_id' => $stock->stock_id
        ]);

        $stock->delete();
    }

    /**
     * 返品された商品を不良在庫として登録
     *
     * @param  \App\Models\Order\ReturnedProduct $returned_product
     * @return \App\Models\Stock\Stock $stock
     */
    public function createDefectiveStockByReturnedProdyct(ReturnedProduct $returned_product): Stock
    {
        $origin_stock = $returned_product->order->stocks->sortByHarvestingDate()->first();
        $factory_product = $returned_product->factory_product;

        $stock = $this->create([
            'factory_code' => $origin_stock->factory_code,
            'warehouse_code' => $origin_stock->warehouse_code,
            'species_code' => $origin_stock->species_code,
            'harvesting_date' => $origin_stock->harvesting_date,
            'number_of_heads' => $factory_product->number_of_heads,
            'weight_per_number_of_heads' => $factory_product->weight_per_number_of_heads,
            'input_group' => $factory_product->input_group,
            'stock_quantity' => $returned_product->quantity * $factory_product->number_of_cases,
            'stock_weight' => $returned_product->quantity
                * $factory_product->number_of_cases
                * $factory_product->weight_per_number_of_heads,
            'stock_status' => StockStatus::DEFECTIVE
        ]);

        $this->stock_history_model->create([
            'factory_code' => $stock->factory_code,
            'screen' => config('constant.stock.screens.return_products_input'),
            'warehouse_code' => $stock->warehouse_code,
            'species_code' => $stock->species_code,
            'stock_status' => $stock->stock_status,
            'number_of_heads' => $stock->number_of_heads,
            'weight_per_number_of_heads' => $stock->weight_per_number_of_heads,
            'input_group' => $stock->input_group,
            'harvesting_date' => $stock->harvesting_date,
            'expiration_date' => $stock->getExpiredOn()->format('Y-m-d'),
            'stock_quantity' => $stock->stock_quantity,
            'transistion_quantity' => $stock->stock_quantity,
            'stock_id' => $stock->stock_id
        ]);

        return $stock;
    }

    /**
     * 在庫数から引当分を減算
     *
     * @param  \App\Models\Stock\Stock $stock
     * @return \App\Models\Stock\Stock $stock
     */
    public function subtractStockQuantity(Stock $stock): Stock
    {
        $transistion_quantity = $stock->getDiffOfStockQuantity();
        if ($transistion_quantity === 0) {
            return $stock;
        }

        $this->stock_history_model->create([
            'factory_code' => $stock->factory_code,
            'screen' => config('constant.stock.screens.product_allocations'),
            'warehouse_code' => $stock->warehouse_code,
            'species_code' => $stock->species_code,
            'stock_status' => $stock->stock_status,
            'number_of_heads' => $stock->number_of_heads,
            'weight_per_number_of_heads' => $stock->weight_per_number_of_heads,
            'input_group' => $stock->input_group,
            'harvesting_date' => $stock->harvesting_date,
            'expiration_date' => $stock->getExpiredOn()->format('Y-m-d'),
            'stock_quantity' => $stock->getStockQuantityExceptDisposed(),
            'transistion_quantity' => $transistion_quantity,
            'stock_id' => $stock->stock_id
        ]);

        $stock->save();
        return $stock;
    }

    /**
     * 引当在庫の登録
     *
     * @param  array $stocks
     * @return void
     */
    public function insertAllocationStocks(array $stocks): void
    {
        foreach ($stocks as $s) {
            $stock = $this->create(array_merge(
                array_except($s, ['order']),
                ['order_number' => $s['order']->order_number]
            ));

            $this->stock_history_model->create([
                'factory_code' => $stock->factory_code,
                'screen' => config('constant.stock.screens.product_allocations'),
                'warehouse_code' => $stock->warehouse_code,
                'species_code' => $stock->species_code,
                'stock_status' => StockStatus::NORMAL,
                'number_of_heads' => $stock->number_of_heads,
                'weight_per_number_of_heads' => $stock->weight_per_number_of_heads,
                'input_group' => $stock->input_group,
                'harvesting_date' => $stock->harvesting_date,
                'expiration_date' => $stock->getExpiredOn()->format('Y-m-d'),
                'allocation_flag' => true,
                'delivery_destination_code' => $s['order']->delivery_destination_code,
                'delivery_lead_time' => $s['order']->getDiffInDaysShippingAndDelivery(),
                'stock_quantity' => $stock->stock_quantity,
                'transistion_quantity' => $stock->stock_quantity,
                'stock_id' => $stock->stock_id
            ]);
        }
    }

    /**
     * 引当の解除
     *
     * @param  \App\Models\Stock\Stock $stock
     * @return void
     */
    public function resetAllocation(Stock $stock): void
    {
        $origin_stock = $this
            ->findNotAllocatedStocks(array_only($stock->toArray(), [
                'factory_code',
                'warehouse_code',
                'species_code',
                'harvesting_date',
                'number_of_heads',
                'weight_per_number_of_heads',
                'input_group',
                'stock_status'
            ]))
            ->first();

        if (is_null($origin_stock)) {
            $origin_stock = $this->create([
                'factory_code' => $stock->factory_code,
                'warehouse_code' => $stock->warehouse_code,
                'species_code' => $stock->species_code,
                'harvesting_date' => $stock->harvesting_date,
                'number_of_heads' => $stock->number_of_heads,
                'weight_per_number_of_heads' => $stock->weight_per_number_of_heads,
                'input_group' => $stock->input_group,
                'stock_quantity' => 0,
                'stock_weight' => 0,
                'stock_status' => StockStatus::NORMAL
            ]);
        }

        $origin_stock->stock_quantity += $stock->stock_quantity;
        $origin_stock->stock_weight += $stock->stock_weight;

        $this->stock_history_model->create([
            'factory_code' => $origin_stock->factory_code,
            'screen' => config('constant.stock.screens.product_allocations'),
            'warehouse_code' => $origin_stock->warehouse_code,
            'species_code' => $origin_stock->species_code,
            'stock_status' => $origin_stock->stock_status,
            'number_of_heads' => $origin_stock->number_of_heads,
            'weight_per_number_of_heads' => $origin_stock->weight_per_number_of_heads,
            'input_group' => $origin_stock->input_group,
            'harvesting_date' => $origin_stock->harvesting_date,
            'expiration_date' => $origin_stock->getExpiredOn()->format('Y-m-d'),
            'stock_quantity' => $origin_stock->getStockQuantityExceptDisposed(),
            'transistion_quantity' => $origin_stock->getDiffOfStockQuantity(),
            'stock_id' => $origin_stock->stock_id
        ]);

        $origin_stock->save();

        $order = $stock->order;
        $this->stock_history_model->create([
            'factory_code' => $stock->factory_code,
            'screen' => config('constant.stock.screens.product_allocations'),
            'warehouse_code' => $stock->warehouse_code,
            'species_code' => $stock->species_code,
            'stock_status' => StockStatus::NORMAL,
            'number_of_heads' => $stock->number_of_heads,
            'weight_per_number_of_heads' => $stock->weight_per_number_of_heads,
            'input_group' => $stock->input_group,
            'harvesting_date' => $stock->harvesting_date,
            'expiration_date' => $stock->getExpiredOn()->format('Y-m-d'),
            'allocation_flag' => true,
            'delivery_destination_code' => $order->delivery_destination_code,
            'delivery_lead_time' => $order->getDiffInDaysShippingAndDelivery(),
            'stock_quantity' => 0,
            'transistion_quantity' => $stock->stock_quantity * -1,
            'stock_id' => $stock->stock_id
        ]);

        $stock->delete();
    }

    /**
     * 在庫を出荷状態にする
     *
     * @param  \App\Models\Stock\Stock $stock
     * @param  \App\Models\Order\Order $order
     * @return \App\Models\Stock\Stock $stock
     */
    public function shipStock(Stock $stock, Order $order): Stock
    {
        $stock->delivery_complete_flag = true;
        $stock->save();

        $this->stock_history_model->create([
            'factory_code' => $stock->factory_code,
            'screen' => config('constant.stock.screens.shipment_fix'),
            'warehouse_code' => $stock->warehouse_code,
            'species_code' => $stock->species_code,
            'stock_status' => $stock->stock_status,
            'number_of_heads' => $stock->number_of_heads,
            'weight_per_number_of_heads' => $stock->weight_per_number_of_heads,
            'input_group' => $stock->input_group,
            'harvesting_date' => $stock->harvesting_date,
            'expiration_date' => $stock->getExpiredOn()->format('Y-m-d'),
            'delivery_date' => $order->delivery_date,
            'allocation_flag' => true,
            'delivery_destination_code' => $order->delivery_destination_code,
            'delivery_lead_time' => $order->getDiffInDaysShippingAndDelivery(),
            'stock_quantity' => $stock->stock_quantity,
            'transistion_quantity' => $stock->stock_quantity * -1,
            'stock_id' => $stock->stock_id
        ]);

        return $stock;
    }

    /**
     * 在庫保管倉庫の変更
     *
     * @param  \App\Models\Stock\Stock $stock
     * @param  array $params
     * @return \App\Models\Stock\Stock $stock
     */
    public function moveStockWholly(Stock $stock, array $params): Stock
    {
        $base_params = [
            'factory_code' => $stock->factory_code,
            'screen' => config('constant.stock.screens.stock_move'),
            'species_code' => $stock->species_code,
            'stock_status' => $stock->stock_status,
            'number_of_heads' => $stock->number_of_heads,
            'weight_per_number_of_heads' => $stock->weight_per_number_of_heads,
            'input_group' => $stock->input_group,
            'harvesting_date' => $stock->harvesting_date,
            'expiration_date' => $stock->getExpiredOn()->format('Y-m-d'),
            'stock_id' => $stock->stock_id
        ];

        $this->stock_history_model->create(array_merge($base_params, [
            'warehouse_code' => $stock->warehouse_code,
            'stock_quantity' => 0,
            'transistion_quantity' => $stock->getStockQuantityExceptDisposed() * -1,
        ]));

        $stock = $this->update($stock, array_merge($params, [
            'before_warehouse_code' => $stock->warehouse_code
        ]));

        $this->stock_history_model->create(array_merge($base_params, [
            'warehouse_code' => $params['warehouse_code'],
            'stock_quantity' => $stock->stock_quantity,
            'transistion_quantity' => $stock->stock_quantity,
        ]));

        return $stock;
    }

    /**
     * 在庫保管倉庫の一部変更
     *
     * @param  \App\Models\Stock\Stock $stock
     * @param  array $params
     * @return \App\Models\Stock\Stock $stock
     */
    public function moveStockPartially(Stock $stock, array $params): Stock
    {
        $base_params = [
            'factory_code' => $stock->factory_code,
            'screen' => config('constant.stock.screens.stock_move'),
            'species_code' => $stock->species_code,
            'stock_status' => $stock->stock_status,
            'number_of_heads' => $stock->number_of_heads,
            'weight_per_number_of_heads' => $stock->weight_per_number_of_heads,
            'input_group' => $stock->input_group,
            'harvesting_date' => $stock->harvesting_date,
            'expiration_date' => $stock->getExpiredOn()->format('Y-m-d')
        ];

        $this->stock_history_model->create(array_merge($base_params, [
            'warehouse_code' => $stock->warehouse_code,
            'stock_quantity' => $stock->stock_quantity - $params['stock_quantity'],
            'transistion_quantity' => $params['stock_quantity'] * -1,
            'stock_id' => $stock->stock_id
        ]));

        $this->update($stock, [
            'stock_quantity' => $stock->stock_quantity - $params['stock_quantity'],
            'stock_weight' => ($stock->stock_quantity - $params['stock_quantity']) * $stock->weight_per_number_of_heads
        ]);

        $stock = $this->create([
            'factory_code' => $stock->factory_code,
            'warehouse_code' => $params['warehouse_code'],
            'species_code' => $stock->species_code,
            'harvesting_date' => $stock->harvesting_date,
            'number_of_heads' => $stock->number_of_heads,
            'weight_per_number_of_heads' => $stock->weight_per_number_of_heads,
            'input_group' => $stock->input_group,
            'stock_quantity' => $params['stock_quantity'],
            'stock_weight' => $params['stock_quantity'] * $stock->weight_per_number_of_heads,
            'before_warehouse_code' => $stock->warehouse_code,
            'moving_start_at' => $params['moving_start_at'],
            'moving_complete_at' => $params['moving_complete_at']
        ]);

        $this->stock_history_model->create(array_merge($base_params, [
            'warehouse_code' => $params['warehouse_code'],
            'stock_quantity' => $stock->stock_quantity,
            'transistion_quantity' => $stock->stock_quantity,
            'stock_id' => $stock->stock_id
        ]));

        return $stock;
    }

    /**
     * 在庫の商品規格の置き換え
     *
     * @param  \App\Models\Stock\Stock $stock
     * @param  array $params
     * @return \App\Models\Stock\Stock $stock
     */
    public function replaceStock(Stock $stock, array $params): Stock
    {
        $base_params = [
            'factory_code' => $stock->factory_code,
            'warehouse_code' => $stock->warehouse_code,
            'species_code' => $stock->species_code,
            'harvesting_date' => $stock->harvesting_date,
            'stock_status' => $stock->stock_status
        ];

        $this->stock_history_model->create(array_merge($base_params, [
            'screen' => config('constant.stock.screens.stock_adjustment'),
            'number_of_heads' => $stock->number_of_heads,
            'weight_per_number_of_heads' => $stock->weight_per_number_of_heads,
            'input_group' => $stock->input_group,
            'expiration_date' => $stock->getExpiredOn()->format('Y-m-d'),
            'stock_quantity' => 0,
            'transistion_quantity' => $stock->getStockQuantityExceptDisposed() * -1,
            'stock_id' => $stock->stock_id
        ]));

        $this->update($stock, [
            'stock_quantity' => $stock->disposal_quantity,
            'stock_weight' => $stock->disposal_weight
        ]);

        $stock = $this->findNotAllocatedStocks(array_merge($base_params, [
            'number_of_heads' => $params['number_of_heads'],
            'weight_per_number_of_heads' => $params['weight_per_number_of_heads'],
            'input_group' => $params['input_group'],
        ]))
            ->first();

        $stock_quantity = $params['stock_quantity'];
        if (! is_null($stock)) {
            $stock_quantity = $stock->stock_quantity + $params['stock_quantity'];
            $stock = $this->update($stock, [
                'stock_quantity' => $stock_quantity,
                'stock_weight' => $stock->stock_weight +
                    ($params['stock_quantity'] * $params['weight_per_number_of_heads']),
            ]);
        }
        if (is_null($stock)) {
            $stock = $this->create($base_params + [
                'number_of_heads' => $params['number_of_heads'],
                'weight_per_number_of_heads' => $params['weight_per_number_of_heads'],
                'input_group' => $params['input_group'] ,
                'stock_quantity' => $params['stock_quantity'],
                'stock_weight' => $params['stock_quantity'] * $params['weight_per_number_of_heads']
            ]);
        }

        $this->stock_history_model->create(array_merge($base_params, [
            'screen' => config('constant.stock.screens.stock_adjustment'),
            'number_of_heads' => $stock->number_of_heads,
            'weight_per_number_of_heads' => $stock->weight_per_number_of_heads,
            'input_group' => $stock->input_group,
            'expiration_date' => $stock->getExpiredOn()->format('Y-m-d'),
            'stock_quantity' => $stock_quantity,
            'transistion_quantity' => $params['stock_quantity'],
            'stock_id' => $stock->stock_id
        ]));

        return $stock;
    }

    /**
     * 在庫の分割
     *
     * @param  \App\Models\Stock\Stock $stock
     * @param  int $stock_quantity
     * @return \App\Models\Stock\Stock $stock
     */
    public function separateStock(Stock $stock, int $stock_quantity): Stock
    {
        $created_stock = $this->create([
            'factory_code' => $stock->factory_code,
            'warehouse_code' => $stock->warehouse_code,
            'species_code' => $stock->species_code,
            'harvesting_date' => $stock->harvesting_date,
            'number_of_heads' => $stock->number_of_heads,
            'weight_per_number_of_heads' => $stock->weight_per_number_of_heads,
            'input_group' => $stock->input_group,
            'stock_quantity' => $stock_quantity,
            'stock_weight' => $stock_quantity * $stock->weight_per_number_of_heads,
            'stock_status' => $stock->stock_status
        ]);

        $this->stock_history_model->create([
            'factory_code' => $created_stock->factory_code,
            'screen' => config('constant.stock.screens.stock_adjustment'),
            'warehouse_code' => $created_stock->warehouse_code,
            'species_code' => $created_stock->species_code,
            'stock_status' => $created_stock->stock_status,
            'number_of_heads' => $created_stock->number_of_heads,
            'weight_per_number_of_heads' => $created_stock->weight_per_number_of_heads,
            'input_group' => $created_stock->input_group,
            'harvesting_date' => $created_stock->harvesting_date,
            'expiration_date' => $created_stock->getExpiredOn()->format('Y-m-d'),
            'stock_quantity' => $created_stock->stock_quantity,
            'transistion_quantity' => $created_stock->stock_quantity,
            'stock_id' => $created_stock->stock_id
        ]);

        $stock_quantity = $stock->stock_quantity - $stock_quantity;
        $stock = $this->update($stock, [
            'stock_quantity' => $stock_quantity,
            'stock_weight' => $stock_quantity * $stock->weight_per_number_of_heads
        ]);

        $this->stock_history_model->create([
            'factory_code' => $stock->factory_code,
            'screen' => config('constant.stock.screens.stock_adjustment'),
            'warehouse_code' => $stock->warehouse_code,
            'species_code' => $stock->species_code,
            'stock_status' => $stock->stock_status,
            'number_of_heads' => $stock->number_of_heads,
            'weight_per_number_of_heads' => $stock->weight_per_number_of_heads,
            'input_group' => $stock->input_group,
            'harvesting_date' => $stock->harvesting_date,
            'expiration_date' => $stock->getExpiredOn()->format('Y-m-d'),
            'stock_quantity' => $stock->getStockQuantityExceptDisposed(),
            'transistion_quantity' => $stock_quantity * -1,
            'stock_id' => $stock->stock_id
        ]);

        return $created_stock;
    }

    /**
     * 在庫状態の変更
     *
     * @param  \App\Models\Stock\Stock $stock
     * @param  \App\ValueObjects\Enum\StockStatus $stock_status
     * @return \App\Models\Stock\Stock $stock
     */
    public function changeStockStatus(Stock $stock, StockStatus $stock_status): Stock
    {
        $this->stock_history_model->create([
            'factory_code' => $stock->factory_code,
            'screen' => config('constant.stock.screens.stock_adjustment'),
            'warehouse_code' => $stock->warehouse_code,
            'species_code' => $stock->species_code,
            'stock_status' => $stock_status,
            'number_of_heads' => $stock->number_of_heads,
            'weight_per_number_of_heads' => $stock->weight_per_number_of_heads,
            'input_group' => $stock->input_group,
            'harvesting_date' => $stock->harvesting_date,
            'expiration_date' => $stock->getExpiredOn()->format('Y-m-d'),
            'stock_quantity' => $stock->getStockQuantityExceptDisposed(),
            'transistion_quantity' => 0,
            'stock_id' => $stock->stock_id
        ]);

        return $this->update($stock, [
            'stock_status' => $stock_status
        ]);
    }

    /**
     * 廃棄情報の登録
     *
     * @param  int $stock_id
     * @return \App\Models\Stock\Stock $stock
     */
    public function disposeStock(int $stock_id, array $params): Stock
    {
        $stock = $this->model->find($stock_id);
        if (is_null($stock)) {
            throw new OptimisticLockException('target stock does not exist. stock id: '.$stock_id);
        }

        $previous_disposal_quantity = $stock->disposal_quantity;
        $stock = $this->update($stock, [
            'disposal_quantity' => (int)$params['disposal_quantity'],
            'disposal_weight' => (int)$params['disposal_weight'],
            'disposal_at' => $params['disposal_at'] ?: null,
            'disposal_remark' => $params['disposal_remark'] ?: '',
            'updated_at' => $params['updated_at']
        ]);

        $base_params = [
            'factory_code' => $stock->factory_code,
            'screen' => config('constant.stock.screens.stock_destruction'),
            'warehouse_code' => $stock->warehouse_code,
            'species_code' => $stock->species_code,
            'stock_status' => $stock->stock_status,
            'number_of_heads' => $stock->number_of_heads,
            'weight_per_number_of_heads' => $stock->weight_per_number_of_heads,
            'input_group' => $stock->input_group,
            'harvesting_date' => $stock->harvesting_date,
            'expiration_date' => $stock->getExpiredOn()->format('Y-m-d'),
            'stock_id' => $stock->stock_id
        ];

        if ($previous_disposal_quantity !== 0) {
            $this->stock_history_model->create(array_merge($base_params, [
                'stock_quantity' => $stock->stock_quantity,
                'transistion_quantity' => $previous_disposal_quantity
            ]));
        }

        if ($stock->disposal_quantity !== 0) {
            $this->stock_history_model->create(array_merge($base_params, [
                'stock_quantity' => $stock->stock_quantity - $params['disposal_quantity'],
                'transistion_quantity' => $params['disposal_quantity'] * -1
            ]));
        }

        $stock->previous_disposal_quantity = $previous_disposal_quantity;
        return $stock;
    }
}

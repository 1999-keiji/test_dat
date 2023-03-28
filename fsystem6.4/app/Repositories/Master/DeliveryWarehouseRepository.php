<?php

declare(strict_types=1);

namespace App\Repositories\Master;

use Illuminate\Pagination\LengthAwarePaginator;
use Cake\Chronos\Chronos;
use App\Models\Master\DeliveryDestination;
use App\Models\Master\Warehouse;
use App\Models\Master\DeliveryWarehouse;
use App\Models\Master\Collections\DeliveryWarehouseCollection;
use App\Models\Master\Collections\FactoryWarehouseCollection;
use App\Models\Master\FactoryWarehouse;

class DeliveryWarehouseRepository
{
    /**
     * @var \App\Models\Master\DeliveryWarehouse
     */
    private $model;

    /**
     * @param  \App\Models\Master\DeliveryWarehouse $model
     * @return void
     */
    public function __construct(DeliveryWarehouse $model)
    {
        $this->model = $model;
    }

    /**
     * 納入倉庫マスタの取得
     *
     * @param  $primary_key
     * @return \App\Models\Master\DeliveryWarehouse
     */
    public function find($primary_key): DeliveryWarehouse
    {
        $query = $this->model->newQuery();
        foreach ($this->model->getKeyName() as $key) {
            $query->where($key, $primary_key[$key]);
        }

        return $query->first();
    }

    /**
     * 納入倉庫マスタの検索
     *
     * @param  array $params
     * @param  array $order
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function search(array $params, array $order): LengthAwarePaginator
    {
        $query = $this->model
            ->select([
                'delivery_warehouses.delivery_destination_code',
                'delivery_destinations.delivery_destination_abbreviation',
                'delivery_warehouses.warehouse_code',
                'warehouses.warehouse_abbreviation',
                'delivery_warehouses.delivery_lead_time',
                'delivery_warehouses.shipment_lead_time'
            ])
            ->join(
                'delivery_destinations',
                'delivery_warehouses.delivery_destination_code',
                '=',
                'delivery_destinations.delivery_destination_code'
            )
            ->join('warehouses', 'delivery_warehouses.warehouse_code', '=', 'warehouses.warehouse_code')
            ->where(function ($query) use ($params) {
                if ($delivery_destination_code = $params['delivery_destination_code']) {
                    $query->where('delivery_warehouses.delivery_destination_code', $delivery_destination_code);
                }
            })
            ->where(function ($query) use ($params) {
                if ($delivery_destination_name = $params['delivery_destination_name']) {
                    $query
                        ->where(
                            'delivery_destinations.delivery_destination_name',
                            'LIKE',
                            "%{$delivery_destination_name}%"
                        )
                        ->orWhere(
                            'delivery_destinations.delivery_destination_name2',
                            'LIKE',
                            "%{$delivery_destination_name}%"
                        )
                        ->orWhere(
                            'delivery_destinations.delivery_destination_abbreviation',
                            'LIKE',
                            "%{$delivery_destination_name}%"
                        );
                }
            })
            ->where(function ($query) use ($params) {
                if ($warehouse_code = $params['warehouse_code']) {
                    $query->where('delivery_warehouses.warehouse_code', $warehouse_code);
                }
            })
            ->where(function ($query) use ($params) {
                if ($warehouse_name = $params['warehouse_name']) {
                    $query->where('warehouses.warehouse_name', 'LIKE', "%{$warehouse_name}%")
                        ->orWhere('warehouses.warehouse_abbreviation', 'LIKE', "%{$warehouse_name}%");
                }
            })
            ->where(function ($query) use ($params) {
                if ($params['delivery_lead_time'] == 0) {
                    $query->whereNull('delivery_warehouses.delivery_lead_time');
                }
            });

        if (array_key_exists('sort', $order) && array_key_exists('order', $order)) {
            $query->orderBy($order['sort'], $order['order']);
        }

        return $query->paginate();
    }

    /**
     * 倉庫に紐づく納入倉庫マスタを取得
     *
     * @param  \App\Models\Master\Warehouse $warehouse
     * @param  array $order
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getDeliveryWarehousesByWarehouse(Warehouse $warehouse, array $order): LengthAwarePaginator
    {
        $query = $this->model
            ->select([
                'delivery_warehouses.delivery_destination_code',
                'delivery_destinations.delivery_destination_abbreviation',
                'delivery_warehouses.warehouse_code',
                'delivery_warehouses.delivery_lead_time',
                'delivery_warehouses.shipment_lead_time'
            ])
            ->join('delivery_destinations', function ($join) {
                $join->on(
                    'delivery_warehouses.delivery_destination_code',
                    '=',
                    'delivery_destinations.delivery_destination_code'
                )
                ->where(function ($query) {
                    $query->where('delivery_destinations.base_plus_delete_flag', false)
                        ->orWhereNull('delivery_destinations.base_plus_delete_flag');
                });
            })
            ->where('delivery_warehouses.warehouse_code', $warehouse->warehouse_code);

        if (array_key_exists('sort', $order) && array_key_exists('order', $order)) {
            $query->orderBy($order['sort'], $order['order']);
        }

        return $query->paginate();
    }

    /**
     * 納入倉庫マスタの登録
     *
     * @param  array $params
     * @return \App\Models\Master\DeliveryWarehouse
     */
    public function create(array $params): DeliveryWarehouse
    {
        return $this->model->create($params);
    }

    /**
     * 納入倉庫マスタの更新
     *
     * @param  \App\Models\Master\DeliveryWarehouse $delivery_warehouse
     * @param  array $params
     * @return \App\Models\Master\DeliveryWarehouse $delivery_warehouse
     */
    public function update(DeliveryWarehouse $delivery_warehouse, array $params): DeliveryWarehouse
    {
        $delivery_warehouse->fill($params)->save();
        return $delivery_warehouse;
    }

    /**
     * 納入先マスタと倉庫マスタの紐づけ
     *
     * @param  \App\Models\Master\DeliveryDestination $delivery_destination
     * @param  \App\Models\Master\Collections\FactoryWarehouseCollection $factory_warehouses
     * @return void
     */
    public function linkWarehouses(
        DeliveryDestination $delivery_destination,
        FactoryWarehouseCollection $factory_warehouses
    ): void {
        foreach ($factory_warehouses as $fh) {
            $this->model->create([
                'delivery_destination_code' => $delivery_destination->delivery_destination_code,
                'warehouse_code' => $fh->warehouse_code,
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ]);
        }
    }

    /**
     * VVF基幹発注取込と注文書Excel取込より、配送リードタイム取得
     *
     */
    public function getDeliveryLeadTimeForPurchaseOrderExcelImport($factory_code): DeliveryWarehouseCollection
    {
        return $this->model
            ->select([
                'delivery_warehouses.delivery_lead_time'
            ])
            ->join('factory_warehouses', function ($join) use ($factory_code) {
                $join->on('delivery_warehouses.warehouse_code', '=', 'factory_warehouses.warehouse_code');
                $join->where('factory_warehouses.factory_code', $factory_code);
            })
            ->orderBy('factory_warehouses.priority', 'asc')
            ->limit(1)
            ->get();
    }
}

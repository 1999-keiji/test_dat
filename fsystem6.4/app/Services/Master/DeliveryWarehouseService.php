<?php

declare(strict_types=1);

namespace App\Services\Master;

use Illuminate\Pagination\LengthAwarePaginator;
use App\Exceptions\PageOverException;
use App\Models\Master\Warehouse;
use App\Models\Master\DeliveryWarehouse;
use App\Models\Master\Collections\DeliveryWarehouseCollection;
use App\Repositories\Master\DeliveryWarehouseRepository;

class DeliveryWarehouseService
{
    /**
     * @var \App\Repositories\Master\DeliveryWarehouseRepository
     */
    private $delivery_warehouse_repo;

    /**
     * @param  \App\Repositories\Master\DeliveryWarehouseRepository $delivery_warehouse_repo
     * @return void
     */
    public function __construct(DeliveryWarehouseRepository $delivery_warehouse_repo)
    {
        $this->delivery_warehouse_repo = $delivery_warehouse_repo;
    }

    /**
     * 納入倉庫マスタを条件に応じて検索
     *
     * @param  array $params
     * @param  array $order
     * @param  int $page
     * @return \Illuminate\Pagination\LengthAwarePaginator
     * @throws \App\Exceptions\PageOverException
     */
    public function search(array $params, array $order, int $page): LengthAwarePaginator
    {
        $params = [
            'delivery_destination_code' => $params['delivery_destination_code'] ?? null,
            'delivery_destination_name' => $params['delivery_destination_name'] ?? null,
            'warehouse_code' => $params['warehouse_code'] ?? null,
            'warehouse_name' => $params['warehouse_name'] ?? null,
            'delivery_lead_time' => $params['delivery_lead_time'] ?? null
        ];

        $delivery_warehouses = $this->delivery_warehouse_repo->search($params, $order);
        if ($page > $delivery_warehouses->lastPage() && $delivery_warehouses->lastPage() !== 0) {
            throw new PageOverException('target page does not exist.');
        }

        return $delivery_warehouses;
    }

    /**
     * 倉庫に紐づく納入倉庫マスタを取得
     *
     * @param  \App\Models\Master\Warehouse $warehouse
     * @param  array $order
     * @param  int $page
     * @return \Illuminate\Pagination\LengthAwarePaginator
     * @throws \App\Exceptions\PageOverException
     */
    public function getDeliveryWarehousesByWarehouse(
        Warehouse $warehouse,
        array $order,
        int $page
    ): LengthAwarePaginator {
        $delivery_warehouses = $this->delivery_warehouse_repo->getDeliveryWarehousesByWarehouse($warehouse, $order);
        if ($page > $delivery_warehouses->lastPage() && $delivery_warehouses->lastPage() !== 0) {
            throw new PageOverException('target page not exists.');
        }

        return $delivery_warehouses;
    }

    /**
     * 納入倉庫マスタの登録
     *
     * @param  array $params
     * @return \App\Models\Master\DeliveryWarehouse
     */
    public function createDeliveryWarehouse(array $params): DeliveryWarehouse
    {
        return $this->delivery_warehouse_repo->create($params);
    }

    /**
     * 納入倉庫マスタの更新
     *
     * @param  \App\Models\Master\DeliveryWarehouse $delivery_warehouse
     * @param  array $params
     * @return \App\Models\Master\DeliveryWarehouse $delivery_warehouse
     */
    public function updateDeliveryWarehouse(DeliveryWarehouse $delivery_warehouse, array $params): DeliveryWarehouse
    {
        return $this->delivery_warehouse_repo->update($delivery_warehouse, $params);
    }

    /**
     * 納入倉庫マスタの削除
     *
     * @param  \App\Models\Master\DeliveryWarehouse $delivery_warehouse
     * @return void
     */
    public function deleteDeliveryWarehouse(DeliveryWarehouse $delivery_warehouse): void
    {
        $delivery_warehouse->delete();
    }
}

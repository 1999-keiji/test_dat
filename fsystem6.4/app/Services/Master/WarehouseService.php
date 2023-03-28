<?php

declare(strict_types=1);

namespace App\Services\Master;

use Illuminate\Pagination\LengthAwarePaginator;
use App\Exceptions\PageOverException;
use App\Models\Master\DeliveryDestination;
use App\Models\Master\Factory;
use App\Models\Master\Warehouse;
use App\Models\Master\Collections\FactoryWarehouseCollection;
use App\Models\Master\Collections\WarehouseCollection;
use App\Repositories\Master\FactoryWarehouseRepository;
use App\Repositories\Master\WarehouseRepository;

class WarehouseService
{
    /**
     * @var \App\Repositories\Master\WarehouseRepository
     */
    private $warehouse_repo;

    /**
     * @var \App\Repositories\Master\FactoryWarehouseRepository
     */
    private $factory_warehouse_repo;

    /**
     * @param  \App\Repositories\Master\WarehouseRepository $warehouse_repositry
     * @param  \App\Repositories\Master\FactoryWarehouseRepository $factory_warehouse_repositry
     * @return void
     */
    public function __construct(
        WarehouseRepository $warehouse_repositry,
        FactoryWarehouseRepository $factory_warehouse_repositry
    ) {
        $this->warehouse_repo = $warehouse_repositry;
        $this->factory_warehouse_repo = $factory_warehouse_repositry;
    }
    
    /**
     * 倉庫マスタの取得
     *
     * @param  string $warehouse_code
     * @return \App\Models\Master\Warehouse
     */
    public function find(string $warehouse_code): Warehouse
    {
        return $this->warehouse_repo->find($warehouse_code);
    }

    /**
     * 倉庫マスタを条件に応じて検索
     *
     * @param  array $params
     * @param  int $page
     * @return \Illuminate\Pagination\LengthAwarePaginator
     * @throws \App\Exceptions\PageOverException
     */
    public function searchWarehouses(array $params, int $page): LengthAwarePaginator
    {
        $params = [
            'warehouse_code' => $params['warehouse_code'] ?? null,
            'warehouse_name' => $params['warehouse_name'] ?? null
        ];

        $warehouses = $this->warehouse_repo->search($params);
        if ($page > $warehouses->lastPage() && $warehouses->lastPage() !== 0) {
            throw new PageOverException('target page not exists.');
        }

        return $warehouses;
    }

    /**
     * 倉庫マスタの登録
     *
     * @param  array $params
     * @return \App\Models\Master\Warehouse
     */
    public function createWarehouse(array $params): Warehouse
    {
        return $this->warehouse_repo->create($params);
    }

    /**
     * 倉庫マスタの更新
     *
     * @param  \App\Models\Master\Warehouse $warehouse
     * @param  array $params
     * @return \App\Models\Master\Warehouse $warehouse
     */
    public function updateWarehouse(Warehouse $warehouse, array $params): Warehouse
    {
        if ($params['prefecture_code'] === null) {
            $params['prefecture_code'] = '';
        }

        return $this->warehouse_repo->update($warehouse, $params);
    }

    /**
     * すべての工場倉庫マスタを取得
     *
     * @return \App\Models\Master\Collections\WarehouseCollection
     */
    public function getAllWarehouse(): WarehouseCollection
    {
        return $this->warehouse_repo->all();
    }

    /**
     * 指定された納入先に未紐づけの倉庫を取得
     *
     * @param  \App\Models\Master\DeliveryDestination $delivery_destination
     * @return \App\Models\Master\Collections\WarehouseCollection
     */
    public function getNotLinkedWarehouses(DeliveryDestination $delivery_destination): WarehouseCollection
    {
        return $this->warehouse_repo->all()->getNotLinkedWarehouses($delivery_destination);
    }

    /**
     * 指定された工場に未紐づけの倉庫を取得
     *
     * @param  \App\Models\Master\Factory
     * @return \App\Models\Master\Collections\WarehouseCollection
     */
    public function getNotLinkedWarehousesByFactory(Factory $factory): WarehouseCollection
    {
        return $this->warehouse_repo->all()->getNotLinkedWarehousesByFactory($factory);
    }

    /**
     * 各工場のデフォルト倉庫を取得
     *
     * @return \App\Models\Master\Collections\FactoryWarehouseCollection
     */
    public function getDefaultWarehouses(): FactoryWarehouseCollection
    {
        return $this->factory_warehouse_repo->getDefaultWarehouses();
    }

    /**
     * 倉庫マスタの削除
     *
     * @param  \App\Models\Master\Warehouse $warehouse
     * @return void
     */
    public function deleteWarehouse(Warehouse $warehouse): void
    {
        $warehouse->delete();
    }
}

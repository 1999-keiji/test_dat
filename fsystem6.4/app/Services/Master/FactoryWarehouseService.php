<?php

declare(strict_types=1);

namespace App\Services\Master;

use Illuminate\Database\Connection;
use App\Models\Master\Factory;
use App\Models\Master\FactoryWarehouse;
use App\Repositories\Master\FactoryWarehouseRepository;

class FactoryWarehouseService
{
    /**
     * @var \Illuminate\Database\Connection
     */
    private $db;

    /**
     * @var \App\Repositories\Master\FactoryWarehouseRepository
     */
    private $factory_warehouse_repo;

    /**
     * @param  \Illuminate\Database\Connection $db
     * @param  \App\Repositories\Master\FactoryWarehouseRepository $factory_warehouse_repo
     * @return void
     */
    public function __construct(Connection $db, FactoryWarehouseRepository $factory_warehouse_repo)
    {
        $this->db = $db;
        $this->factory_warehouse_repo = $factory_warehouse_repo;
    }

    /**
     * 工場と倉庫を紐づける
     *
     * @param  \App\Models\Master\Factory $factory
     * @param  string $warehouse_code
     * @return void
     */
    public function linkWarehouse(Factory $factory, string $warehouse_code): void
    {
        $this->factory_warehouse_repo->create(
            $factory->factory_code,
            $warehouse_code,
            $factory->factory_warehouses->count() + 1
        );
    }

    /**
     * 工場倉庫の優先度を変更する
     *
     * @param \App\Models\Master\Factory $factory
     * @param array $priorities
     * @return void
     */
    public function updateFactoryWarehouse(Factory $factory, array $priorities): void
    {
        $this->db->transaction(function () use ($factory, $priorities) {
            $factory->factory_warehouses->each(function ($fw) use ($priorities) {
                $this->factory_warehouse_repo->update($fw, (int)$priorities[$fw->warehouse_code]);
            });
        });
    }

    /**
     * 工場倉庫を削除する
     *
     * @param \App\Models\Master\Factory $factory
     * @param \App\Models\Master\FactoryWarehouse $factory_warehouse
     * @return void
     */
    public function deleteFactoryWarehouse(Factory $factory, FactoryWarehouse $factory_warehouse): void
    {
        $this->db->transaction(function () use ($factory, $factory_warehouse) {
            $factory_warehouse->delete();
            $factory->factory_warehouses->sortByPriority()->each(function ($fw, $idx) {
                $this->factory_warehouse_repo->update($fw, $idx + 1);
            });
        });
    }

    /**
     * 指定された工場コードに紐づく保管倉庫を取得
     *
     * @param  array
     * @return array
     */
    public function getWarehousesWithFactoryCodeForSearchingApi(array $params): array
    {
        $params = [
            'factory_code' => $params['factory_code'] ?? null,
        ];

        return $this->factory_warehouse_repo->getWarehouseWithFactoryCode($params)->toResponseForSearchingApi();
    }
}

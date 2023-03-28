<?php

declare(strict_types=1);

namespace App\Repositories\Master;

use Illuminate\Database\Connection;
use App\Models\Master\FactoryWarehouse;
use App\Models\Master\Collections\FactoryWarehouseCollection;

class FactoryWarehouseRepository
{
    /**
     * @var \Illuminate\Database\Connection
     */
    private $db;

    /**
     * @var \App\Models\Master\FactoryWarehouse
     */
    private $model;

    /**
     * @param  \App\Models\Master\FactoryWarehouse $model
     * @return void
     */
    public function __construct(Connection $db, FactoryWarehouse $model)
    {
        $this->db = $db;
        $this->model = $model;
    }

    /**
     * 各工場のデフォルト倉庫を取得
     *
     * @return \App\Models\Master\Collections\FactoryWarehouseCollection
     */
    public function getDefaultWarehouses(): FactoryWarehouseCollection
    {
        $table = $this->model->getTable();
        $query = $this->db->table($table)
            ->selectRaw('factory_code, MIN(priority) AS priority')
            ->groupBy('factory_code')
            ->toSql();

        return $this->model
            ->select(["{$table}.factory_code", "{$table}.warehouse_code"])
            ->join($this->db->raw("({$query}) AS `default`"), function ($join) use ($table) {
                $join->on('default.factory_code', '=', "{$table}.factory_code")
                    ->on('default.priority', '=', "{$table}.priority");
            })
            ->get();
    }

    /**
     * 指定された工場に紐づく倉庫を検索
     *
     * @param  array $params
     * @return \App\Models\Master\Collections\FactoryWarehouseCollection
     */
    public function getWarehouseWithFactoryCode(array $params): FactoryWarehouseCollection
    {
        return $this->model
            ->select([
                'warehouses.warehouse_code',
                'warehouses.warehouse_name',
                'warehouses.warehouse_abbreviation'
            ])
            ->join('warehouses', function ($join) {
                $join->on('warehouses.warehouse_code', '=', 'factory_warehouses.warehouse_code');
            })
            ->where('factory_warehouses.factory_code', $params['factory_code'])
            ->orderBy('factory_warehouses.priority', 'ASC')
            ->get();
    }

    /**
     * 工場倉庫マスタの登録
     *
     * @param  string $factory_code
     * @param  string $warehouse_code
     * @param  int $priority
     * @return void
     */
    public function create(string $factory_code, string $warehouse_code, int $priority): void
    {
        $this->model->create([
            'factory_code' => $factory_code,
            'warehouse_code' => $warehouse_code,
            'priority' => $priority
        ]);
    }

    /**
     * 工場倉庫マスタの更新
     *
     * @param  \App\Models\Master\FactoryWarehouse $factory_warehouse
     * @param  int $priority
     * @return \App\Models\Master\FactoryWarehouse
     */
    public function update(FactoryWarehouse $factory_warehouse, int $priority): FactoryWarehouse
    {
        $factory_warehouse->fill(compact('priority'))->save();
        return $factory_warehouse;
    }
}

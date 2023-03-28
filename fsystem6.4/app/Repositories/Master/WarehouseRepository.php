<?php

declare(strict_types=1);

namespace App\Repositories\Master;

use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Master\Warehouse;
use App\Models\Master\Collections\WarehouseCollection;

class WarehouseRepository
{
    /**
     * @var \App\Models\Master\Warehouse
     */
    private $model;

    /**
     * @param  \App\Models\Master\Warehouse
     * @return void
     */
    public function __construct(Warehouse $model)
    {
        $this->model = $model;
    }


    /**
     * 倉庫マスタを取得
     *
     * @param  string $warehouse_code
     * @return \App\Models\Master\Warehouse
     */
    public function find(string $warehouse_code): Warehouse
    {
        return $this->model->find($warehouse_code);
    }

    /**
     * 倉庫マスタの検索
     *
     * @param  array $params
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function search(array $params): LengthAwarePaginator
    {
        return $this->model->select([
            'warehouse_code',
            'warehouse_name'
        ])
            ->where(function ($query) use ($params) {
                if ($warehouse_code = $params['warehouse_code']) {
                    $query->where('warehouse_code', $warehouse_code);
                }
            })
            ->where(function ($query) use ($params) {
                if ($warehouse_name = $params['warehouse_name']) {
                    $query->where('warehouse_name', 'LIKE', "%{$warehouse_name}%")
                        ->orWhere('warehouse_abbreviation', 'LIKE', "%{$warehouse_name}%");
                }
            })
            ->sortable(['warehouse_code' => 'ASC'])
            ->paginate();
    }

    /**
     * すべての倉庫マスタを取得
     *
     * @return \App\Models\Master\Collections\WarehouseCollection
     */
    public function all(): WarehouseCollection
    {
        return $this->model
            ->select([
                'warehouse_code',
                'warehouse_name',
                'warehouse_abbreviation'
            ])
            ->where('can_display', true)
            ->orderBy('warehouse_code', 'ASC')
            ->get();
    }

    /**
     * 倉庫マスタの登録
     *
     * @param  array $params
     * @return \App\Models\Master\Warehouse
     */
    public function create(array $params): Warehouse
    {
        return $this->model->create(array_filter($params, 'is_not_null'));
    }

    /**
     * 倉庫マスタの更新
     *
     * @param  \App\Models\Master\Warehouse $warehouse
     * @param  array $params
     * @return \App\Models\Master\Warehouse $warehouse
     */
    public function update(Warehouse $warehouse, array $params): Warehouse
    {
        $warehouse->fill(array_filter($params, 'is_not_null'))->save();
        return $warehouse;
    }
}

<?php

declare(strict_types=1);

namespace App\Repositories\Master;

use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Master\Factory;
use App\Models\Master\Collections\FactoryCollection;

class FactoryRepository
{
    /**
     * @var \App\Models\Master\Factory
     */
    private $model;

    /**
     * @param  \App\Models\Master\Factory $model
     * @return void
     */
    public function __construct(Factory $model)
    {
        $this->model = $model;
    }

    /**
     * 工場マスタを取得
     *
     * @param  string $factory_code
     * @return \App\Models\Master\Factory
     */
    public function find(string $factory_code): Factory
    {
        return $this->model->find($factory_code);
    }

    /**
     * すべての工場マスタを取得
     *
     * @return \App\Models\Master\Collections\FactoryCollection
     */
    public function all(): FactoryCollection
    {
        return $this->model
            ->select([
                'factory_code',
                'factory_name',
                'factory_abbreviation',
                'corporation_code',
                'supplier_code'
            ])
            ->orderBy('factory_code', 'ASC')
            ->get();
    }

    /**
     * 工場マスタを条件に応じて検索
     *
     * @param  array $params
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function search($params): LengthAwarePaginator
    {
        return $this->model
            ->select([
                'factory_code',
                'factory_name',
                'corporation_code'
            ])
            ->where(function ($query) use ($params) {
                if ($corporation_code = $params['corporation_code'] ?? null) {
                    $query->where('corporation_code', $corporation_code);
                }
            })
            ->where(function ($query) use ($params) {
                if ($factory_code = $params['factory_code'] ?? null) {
                    $query->where('factory_code', $factory_code);
                }
            })
            ->where(function ($query) use ($params) {
                if ($factory_name = $params['factory_name'] ?? null) {
                    $query->where('factory_name', 'LIKE', "%{$factory_name}%")
                        ->orWhere('factory_abbreviation', 'LIKE', "%{$factory_name}%");
                }
            })
            ->affiliatedFactories('factories')
            ->sortable(['factory_code' => 'ASC'])
            ->with('corporation')
            ->paginate();
    }

    /**
     * 仕入先コードで工場マスタを取得
     *
     * @param  string $supplier_code
     * @return \App\Models\Master\Factory
     */
    public function getFactoryBySupplierCode(string $supplier_code): ?Factory
    {
        return $this->model
            ->where('supplier_code', $supplier_code)
            ->first();
    }

    /**
     * 工場マスタの登録
     *
     * @param  array $params
     * @return \App\Models\Master\Factory
     */
    public function create(array $params): Factory
    {
        return $this->model->create(array_filter($params, 'is_not_null'));
    }

    /**
     * 工場マスタの更新
     *
     * @param  array $params
     * @return \App\Models\Master\Factory
     */
    public function update(Factory $factory, array $params): Factory
    {
        $factory->fill(array_filter($params, 'is_not_null'))->save();
        return $factory;
    }
}

<?php

declare(strict_types=1);

namespace App\Repositories\Master;

use App\Models\Master\Supplier;

class SupplierRepository
{
    /**
     * @var \App\Models\Master\Supplier
     */
    private $model;

    /**
     * @param \App\Models\Master\Supplier $model
     * @return void
     */
    public function __construct(Supplier $model)
    {
        $this->model = $model;
    }

    /**
     * 仕入先コードと適用開始日により一意のレコードを取得
     *
     * @param  array $params
     * @return \App\Models\Master\Supplier
     */
    public function getSupplier($params)
    {
        return $this->model
            ->where('supplier_code', $params['supplier_code'])
            ->where('application_started_on', $params['application_started_on'])
            ->first();
    }

    /**
     * 仕入先マスタの登録
     *
     * @param  array $params
     * @return \App\Models\Master\Supplier
     */
    public function create(array $params): Supplier
    {
        return $this->model->create(array_filter($params, 'is_not_null'));
    }

    /**
     * 仕入先マスタの更新
     *
     * @param \App\Models\Master\Supplier $supplier
     * @param  array $params
     * @return \App\Models\Master\Supplier $supplier
     */
    public function update(Supplier $supplier, array $params): Supplier
    {
        $supplier->fill(array_filter($params, 'is_not_null'))->save();
        return $supplier;
    }
}

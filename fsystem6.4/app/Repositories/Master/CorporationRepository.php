<?php

declare(strict_types=1);

namespace App\Repositories\Master;

use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Master\Corporation;
use App\Models\Master\Collections\CorporationCollection;

class CorporationRepository
{
    /**
     * @var \App\Models\Master\Corporation
     */
    private $model;

    /**
     * @param  \App\Models\Master\Corporation $model
     * @return void
     */
    public function __construct(Corporation $model)
    {
        $this->model = $model;
    }

    /**
     * すべての法人マスタを取得
     *
     * @return \App\Models\Master\Collections\CorporationCollection
     */
    public function all(): CorporationCollection
    {
        return $this->model
            ->select([
                'corporation_code',
                'corporation_name',
                'corporation_abbreviation'
            ])
            ->orderBy('corporation_code', 'ASC')
            ->get();
    }

    /**
     * 法人マスタの検索
     *
     * @param  array $params
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function search(array $params): LengthAwarePaginator
    {
        return $this->model
            ->select([
                'corporation_code',
                'corporation_name'
            ])
            ->where(function ($query) use ($params) {
                if ($corporation_code = $params['corporation_code'] ?? null) {
                    $query->where('corporation_code', $corporation_code);
                }
            })
            ->where(function ($query) use ($params) {
                if ($corporation_name = $params['corporation_name'] ?? null) {
                    $query->where('corporation_name', 'LIKE', "%{$corporation_name}%")
                        ->orWhere('corporation_abbreviation', 'LIKE', "%{$corporation_name}%");
                }
            })
            ->with(['factories'])
            ->sortable(['corporation_code' => 'ASC'])
            ->paginate();
    }

    /**
     * 法人マスタの登録
     *
     * @param  array $params
     * @return \App\Models\Master\Corporation
     */
    public function create(array $params): Corporation
    {
        return $this->model->create(array_filter($params, 'is_not_null'));
    }

    /**
     * 法人マスタの更新
     *
     * @param  \App\Models\Master\Corporation $corporation
     * @param  array $params
     * @return \App\Models\Master\Corporation $corporation
     */
    public function update(Corporation $corporation, array $params): Corporation
    {
        $corporation->fill(array_filter($params, 'is_not_null'))->save();
        return $corporation;
    }
}

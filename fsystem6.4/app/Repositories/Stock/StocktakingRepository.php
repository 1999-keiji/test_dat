<?php

declare(strict_types=1);

namespace App\Repositories\Stock;

use App\Models\Stock\Stocktaking;

class StocktakingRepository
{
    /**
     * @var \App\Models\Stock\Stocktaking
     */
    private $model;

    /**
     * @param  \App\Models\Stock\Stocktaking $model
     * @return void
     */
    public function __construct(Stocktaking $model)
    {
        $this->model = $model;
    }

    /**
     * 在庫棚卸データの取得
     *
     * @param  array $params
     * @return \App\Models\Stock\Stocktaking|null
     */
    public function getStocktaking(array $params): ?Stocktaking
    {
        return $this->model
            ->selectRaw(
                'factory_code, warehouse_code, stocktaking_month, '.
                "DATE_FORMAT(stocktaking_comp_at, '%Y/%m/%d %H:%i:%s') AS stocktaking_comp_at"
            )
            ->where('factory_code', $params['factory_code'])
            ->where('warehouse_code', $params['warehouse_code'])
            ->where('stocktaking_month', $params['stocktaking_month'])
            ->first();
    }
}

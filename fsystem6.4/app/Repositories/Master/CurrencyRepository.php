<?php

declare(strict_types=1);

namespace App\Repositories\Master;

use App\Models\Master\Currency;
use App\Models\Master\Collections\CurrencyCollection;

class CurrencyRepository
{
    /**
     * @var \App\Models\Master\Currency
     */
    private $model;

    /**
     * @param  \App\Models\Master\Currency $model
     * @return void
     */
    public function __construct(Currency $model)
    {
        $this->model = $model;
    }

    /**
     * すべての通貨マスタを取得
     *
     * @return \App\Models\Master\Collections\CuurencyCollection
     */
    public function all(): CurrencyCollection
    {
        return $this->model
            ->select(['currency_code', 'order_unit_decimals', 'order_amount_decimals'])
            ->orderBy('currency_code', 'ASC')
            ->get();
    }
}

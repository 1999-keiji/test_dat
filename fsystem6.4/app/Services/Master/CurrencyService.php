<?php

declare(strict_types=1);

namespace App\Services\Master;

use App\Models\Master\Collections\CurrencyCollection;
use App\Repositories\Master\CurrencyRepository;

class CurrencyService
{
    /**
     * @var \App\Repositories\Master\CurrencyRepository
     */
    private $currency_repo;

    /**
     * @param  \App\Repositories\Master\CurrencyRepository $currency_repositry
     * @return void
     */
    public function __construct(CurrencyRepository $currency_repositry)
    {
        $this->currency_repo = $currency_repositry;
    }

    /**
     * すべての品種マスタを取得
     *
     * @return \App\Models\Master\Collections\CurrencyCollection
     */
    public function getAllCurrencies(): CurrencyCollection
    {
        return $this->currency_repo->all();
    }
}

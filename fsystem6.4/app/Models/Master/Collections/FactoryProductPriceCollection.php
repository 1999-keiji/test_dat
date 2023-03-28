<?php

declare(strict_types=1);

namespace App\Models\Master\Collections;

use Illuminate\Database\Eloquent\Collection;
use App\Models\Master\FactoryProductPrice;
use App\ValueObjects\Date\Date;
use App\ValueObjects\Decimal\UnitPrice;

class FactoryProductPriceCollection extends Collection
{
    /**
     * API用にレスポンスパラメータの形式にコンバート
     *
     * @return array
     */
    public function toResponseForSearchingApi(): array
    {
        return $this
            ->map(function ($fp) {
                return $fp->toArray();
            })
            ->all();
    }

    /**
     * 通貨コードと適用開始日で抽出
     *
     * @param  string $currency_code
     * @param  string $application_started_on
     * @return \App\Models\Master\FactoryProductPrice
     */
    public function filterByCurrencyAndApplicationDate(
        string $currency_code,
        string $application_started_on
    ): ?FactoryProductPrice {
        return $this->where('currency_code', $currency_code)
            ->filter(function ($fpp) use ($application_started_on) {
                return $fpp->application_started_on->format('Ymd') === $application_started_on;
            })
            ->first();
    }

    /**
     * 適用される単価を取得
     *
     * @param  string $currency_code
     * @param  \App\ValueObjects\Date\Date $date
     * @return \App\ValueObjects\Decimal\UnitPrice $unit_price
     */
    public function getAppliedUnitPrice(string $currency_code, Date $date): ?UnitPrice
    {
        return $this->where('currency_code', $currency_code)
            ->filter(function ($fpsp) use ($date) {
                return $fpsp->application_started_on->lte($date);
            })
            ->sortByDesc('application_started_on')
            ->first()
            ->unit_price ?? null;
    }
}

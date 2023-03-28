<?php

declare(strict_types=1);

namespace App\Models\Plan\Collections;

use stdClass;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Plan\ForecastedProductRate;
use App\ValueObjects\Date\Date;
use App\ValueObjects\Date\HarvestingDate;

class ForecastedProductRateCollection extends Collection
{
    /**
     * 工場を条件に抽出
     *
     * @param  \stdClass $factory
     * @return \App\Models\Plan\Collections\ForecastedProductRateCollection
     */
    public function filterByFactory(stdClass $factory): ForecastedProductRateCollection
    {
        return $this->where('factory_code', $factory->factory_code);
    }

    /**
     * 収穫日を条件に抽出
     *
     * @param  \App\ValueObjects\Date\Date $date
     * @return \App\Models\Plan\ForecastedProductRate
     */
    public function filterByHarvestingDate(Date $date): ?ForecastedProductRate
    {
        return $this->where('harvesting_date', $date->format('Y-m-d'))->first();
    }

    /**
     * 収穫年月を条件に取得
     *
     * @param  string $harvesting_month
     * @return \App\Models\Plan\ForecastedProductRate
     */
    public function filterByHarvestingMonth(string $harvesting_month): ?ForecastedProductRate
    {
        return $this->where('harvesting_month', $harvesting_month)->first();
    }

    /**
     * 指定された収穫日よりも過去のデータのみを抽出
     *
     * @param  \App\ValueObjects\Date\Date $date
     * @return \App\Models\Plan\Collections\ForecastedProductRateCollection
     */
    public function filterOldByHarvestingDate(Date $date): ForecastedProductRateCollection
    {
        return $this->filter(function ($fpr) use ($date) {
            return HarvestingDate::parse($fpr->harvesting_date)->lt($date);
        });
    }

    /**
     * 収穫日が最新のデータを取得
     *
     * @return \App\Models\Plan\ForecastedProductRate
     */
    public function getLatest(): ?ForecastedProductRate
    {
        return $this->sortByDesc('harvesting_date')->first();
    }

    /**
     * 指定された収穫日よりも過去のデータを週ごとに集約して取得
     *
     * @param  \App\ValueObjects\Date\Date $date
     * @return \App\Models\Plan\Collections\ForecastedProductRateCollection
     */
    public function getLatestGroupedForecastedProducts(Date $date): ?ForecastedProductRateCollection
    {
        return $this
            ->filter(function ($fpr) use ($date) {
                return HarvestingDate::parse($fpr->harvesting_date)->lt($date->startOfWeek());
            })
            ->groupBy(function ($fpr) {
                return HarvestingDate::parse($fpr->harvesting_date)->format('W');
            })
            ->sortByDesc(function ($grouped, $week) {
                return $week;
            })
            ->first();
    }
}

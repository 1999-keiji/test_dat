<?php

declare(strict_types=1);

namespace App\Models\Shipment\Collections;

use Illuminate\Database\Eloquent\Collection;
use App\Models\Master\Factory;
use App\Models\Shipment\ProductizedResult;
use App\ValueObjects\Date\HarvestingDate;

class ProductizedResultCollection extends Collection
{
    /**
     * 品種でグルーピング
     *
     * @return \App\Models\Shipment\Collections\ProductizedResultCollection
     */
    public function groupBySpecies(): ProductizedResultCollection
    {
        return $this->groupBy('species_code');
    }

    /**
     * 指定された収穫日の製品化実績を取得する
     *
     * @param  \App\ValueObjects\Date\HarvestingDate $date
     * @return \App\Models\Shipment\ProductizedResult
     */
    public function findByHarvestingDate(HarvestingDate $date): ?ProductizedResult
    {
        return $this
            ->filter(function ($pr) use ($date) {
                return $pr->harvesting_date->format('Ymd') === $date->format('Ymd');
            })
            ->first();
    }

    /**
     * 製品化実績が入力できないものを排除
     *
     * @param  \App\Models\Master\Factory $factory
     * @return \App\Models\Shipment\Collections\ProductizedResultCollection
     */
    public function rejectNotInputtable(Factory $factory): ProductizedResultCollection
    {
        return $this->reject(function ($pr) use ($factory) {
            if (! is_null($pr->producted_quantity)) {
                return false;
            }

            $harvesting_date = HarvestingDate::parse($pr->harvesting_date->format('Y-m-d'));
            if ($harvesting_date->isWorkingDay($factory)) {
                return false;
            }

            return $pr->forecasted_crop_failure === 0 && $pr->forecasted_advanced_harvest === 0;
        });
    }
}

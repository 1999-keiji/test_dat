<?php

declare(strict_types=1);

namespace App\Models\Plan\Collections;

use stdClass;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Plan\Crop;
use App\ValueObjects\Date\HarvestingDate;

class CropCollection extends Collection
{
    /**
     * 工場を条件に抽出
     *
     * @param  \stdClass $factory
     * @return \App\Models\Plan\Collections\CropCollection
     */
    public function filterByFactory(stdClass $factory): CropCollection
    {
        return $this->where('factory_code', $factory->factory_code);
    }

    /**
     * 収穫日を条件にデータを取得
     *
     * @param  \App\ValueObjects\Date\HarvestingDate $harvesting_date
     * @return \App\Models\Plan\Crop
     */
    public function filterByHarvestingDate(HarvestingDate $harvesting_date): ?Crop
    {
        return $this->where('harvesting_date', $harvesting_date->format('Y-m-d'))->first();
    }

    /**
     * 収穫年月を条件にデータを取得
     *
     * @param  string $harvesting_month
     * @return \App\Models\Plan\Crop
     */
    public function filterByHarvestingMonth(string $harvesting_month): ?Crop
    {
        return $this->where('harvesting_month', $harvesting_month)->first();
    }
}

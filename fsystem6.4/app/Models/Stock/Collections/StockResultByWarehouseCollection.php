<?php

declare(strict_types=1);

namespace App\Models\Stock\Collections;

use Illuminate\Database\Eloquent\Collection;
use App\Models\Stock\StockResultByWarehouse;
use App\ValueObjects\Date\HarvestingDate;

class StockResultByWarehouseCollection extends Collection
{
    /**
     * 収穫日で抽出
     *
     * @param  \App\ValueObjects\Date\HarvestingDate $harvesting_date
     * @return \App\Models\Stock\StockResultByWarehouse|null
     */
    public function filterByHarvestingDate(HarvestingDate $harvesting_date): ?StockResultByWarehouse
    {
        return $this->firstWhere('harvesting_date', $harvesting_date->format('Y-m-d'));
    }
}

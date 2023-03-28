<?php
declare(strict_types=1);

namespace App\Models\Shipment\Collections;

use Illuminate\Database\Eloquent\Collection;
use App\Models\Shipment\ProductAllocation;
use App\ValueObjects\Date\Date;

class ProductAllocationCollection extends Collection
{
    /**
     * 引当数の合計を取得
     *
     * @return int
     */
    public function toSumOfAllocationQuantity(): int
    {
        return $this->pluck('allocation_quantity')->sum();
    }

    /**
     * 引当日で抽出
     *
     * @param  \App\ValueObjects\Date\Date $date
     * @return \App\Models\Shipment\ProductAllocation
     */
    public function filterByAllocatedDate(Date $date): ?ProductAllocation
    {
        return $this->where('allocated_on', $date->toDateString())->first();
    }
}

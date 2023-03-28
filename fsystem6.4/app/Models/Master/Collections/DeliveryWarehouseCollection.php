<?php

declare(strict_types=1);

namespace App\Models\Master\Collections;

use Illuminate\Database\Eloquent\Collection;
use App\Models\Master\DeliveryWarehouse;
use App\Models\Master\Factory;

class DeliveryWarehouseCollection extends Collection
{
    /**
     * 工場を条件に抽出
     *
     * @param  \App\Models\Master\Factory $factory
     * @return \App\Models\Master\DeliveryWarehouse
     */
    public function filterByFactory(Factory $factory): ?DeliveryWarehouse
    {
        $factory_warehouses = $factory->factory_warehouses;
        return $this
            ->filter(function ($dw) use ($factory_warehouses) {
                return in_array($dw->warehouse_code, $factory_warehouses->pluck('warehouse_code')->all(), true);
            })
            ->sortBy(function ($dw) use ($factory_warehouses) {
                return $factory_warehouses->where('warehouse_code', $dw->warehouse_code)->first()->priority;
            })
            ->first();
    }
}

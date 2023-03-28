<?php

declare(strict_types=1);

namespace App\Models\Master\Collections;

use Illuminate\Database\Eloquent\Collection;
use App\Models\Master\Factory;
use App\Models\Master\DeliveryDestination;
use App\Models\Master\Warehouse;

class WarehouseCollection extends Collection
{
    /**
     * 倉庫コードで抽出
     *
     * @return \App\Models\Master\Warehouse|null
     */
    public function findByWarehouseCode(string $warehouse_code): ?Warehouse
    {
        return $this->firstWhere('warehouse_code', $warehouse_code);
    }

    /**
     * 指定された納入先に未紐づけの倉庫を取得
     *
     * @param  \App\Models\Master\DeliveryDestination $delivery_destination
     * @return \App\Models\Master\Collections\WarehouseCollection
     */
    public function getNotLinkedWarehouses(DeliveryDestination $delivery_destination): WarehouseCollection
    {
        $warehouse_code_list = $delivery_destination->delivery_warehouses->pluck('warehouse_code')->all();
        return $this
            ->reject(function ($w) use ($warehouse_code_list) {
                return in_array($w->warehouse_code, $warehouse_code_list, true);
            })
            ->values();
    }

    /**
     * 指定された工場に未紐づけの倉庫を取得
     *
     * @param  \App\Models\Master\Factory $factory
     * @return \App\Models\Master\Collections\WarehouseCollection
     */
    public function getNotLinkedWarehousesByFactory(Factory $factory): WarehouseCollection
    {
        $warehouse_code_list = $factory->factory_warehouses->pluck('warehouse_code')->all();
        return $this
            ->reject(function ($w) use ($warehouse_code_list) {
                return in_array($w->warehouse_code, $warehouse_code_list, true);
            })
            ->values();
    }
}

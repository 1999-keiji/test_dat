<?php

declare(strict_types=1);

namespace App\Models\Master\Collections;

use Illuminate\Database\Eloquent\Collection;

class FactoryWarehouseCollection extends Collection
{
    /**
     * 優先度順に並び替え
     *
     * @return \App\Models\Master\Collections\FactoryWarehouseCollection
     */
    public function sortByPriority(): FactoryWarehouseCollection
    {
        return $this->sortBy('priority');
    }

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
}

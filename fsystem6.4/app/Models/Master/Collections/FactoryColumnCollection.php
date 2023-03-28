<?php

declare(strict_types=1);

namespace App\Models\Master\Collections;

use Illuminate\Database\Eloquent\Collection;

class FactoryColumnCollection extends Collection
{
    /**
     * 循環の情報を返却
     *
     * @return array
     */
    public function circulations(): array
    {
        return $this
            ->groupBy('circulation')
            ->map(function ($grouped, $circulation) {
                return [
                    'circulation' => $circulation,
                    'count' => $grouped->count()
                ];
            })
            ->values()
            ->all();
    }

    /**
     * 列の情報を返却
     *
     * @return array
     */
    public function columns(): array
    {
        return $this
            ->sortBy('column')
            ->map(function ($fc) {
                return [
                    'column' => $fc->column,
                    'column_name' => $fc->column_name,
                    'circulation' => $fc->circulation
                ];
            })
            ->all();
    }

    /**
     * 列と循環のMAPに加工
     *
     * @return array
     */
    public function toMapOfColumnAndCirculation()
    {
        return $this->pluck('circulation', 'column')->all();
    }
}

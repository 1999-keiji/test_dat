<?php

declare(strict_types=1);

namespace App\Models\Stock\Collections;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as BaseCollection;
use App\Models\Stock\StockState;
use App\ValueObjects\Date\Date;

class StockStateCollection extends Collection
{
    /**
     * 品種ごとにグルーピング
     *
     * @return \Illuminate\Support\Collection
     */
    public function groupBySpecies(): BaseCollection
    {
        return $this->groupBy('species_code')
            ->map(function ($grouped, $species_code) {
                return (object)[
                    'species_code' => $species_code,
                    'species_name' => $grouped->first()->species_abbreviation,
                    'stocks' => $grouped
                ];
            })
            ->values();
    }

    /**
     * 在庫株数の合計を取得
     *
     * @return int
     */
    public function sumOfStockNumber(): int
    {
        return (int)$this->sum('stock_number');
    }

    /**
     * 在庫重量の合計を取得
     *
     * @return int
     */
    public function sumOfStockWeight(): int
    {
        return $this->sum('stock_weight');
    }
}

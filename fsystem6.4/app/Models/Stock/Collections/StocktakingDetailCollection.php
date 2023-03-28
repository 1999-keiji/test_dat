<?php

declare(strict_types=1);

namespace App\Models\Stock\Collections;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as BaseCollection;

class StocktakingDetailCollection extends Collection
{
    /**
     * 品種単位でグルーピング
     *
     * @return \Illuminate\Support\Collection
     */
    public function groupBySpecies(): BaseCollection
    {
        return $this->groupBy('species_code')
            ->map(function ($grouped, $species_code) {
                return (object)[
                    'species_code' => $species_code,
                    'species_name' => $grouped->first()->species_name,
                    'stock_styles' => $grouped
                ];
            })
            ->values();
    }

    /**
     * 重量の合計を取得
     *
     * @return int
     */
    public function totalOfWeight(): int
    {
        return $this
            ->map(function ($sd) {
                return $sd->getStocktakingWeight();
            })
            ->sum();
    }

    /**
     * 株数の合計を取得
     *
     * @return int
     */
    public function totalOfStockQuantity(): int
    {
        return $this
            ->map(function ($sd) {
                return $sd->getSumOfStockQuantity();
            })
            ->sum();
    }
}

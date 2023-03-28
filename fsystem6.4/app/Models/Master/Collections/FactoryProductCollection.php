<?php

declare(strict_types=1);

namespace App\Models\Master\Collections;

use Illuminate\Database\Eloquent\Collection;
use App\Models\Master\Species;

class FactoryProductCollection extends Collection
{
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

    /**
     * 商品規格ごとにグルーピング
     *
     * @return \App\Models\Master\Collections\FactoryProductCollection
     */
    public function groupByPackagingStyle(): FactoryProductCollection
    {
        return $this->groupBy(function ($fp) {
            return implode('-', $fp->getPackagingStyle());
        });
    }

    /**
     * 指定された品種の商品規格を取得
     *
     * @param  \App\Models\Master\Species $species
     * @return array
     */
    public function getPackagingStylesBySpecies(Species $species): array
    {
        return $this
            ->filter(function ($fp) use ($species) {
                return $fp->product->species_code === $species->species_code;
            })
            ->sortBy(function ($fp) {
                return $fp->getPackagingStyle();
            })
            ->groupByPackagingStyle()
            ->map(function ($grouped) {
                return $grouped->first()->getPackagingStyle();
            })
            ->values()
            ->all();
    }

    /**
     * 指定された品種の商品規格を取得(作業指示書用)
     *
     * @param  \App\Models\Master\Species $species
     * @return array
     */
    public function getPackagingStylesBySpeciesForWorkInstruction(Species $species): array
    {
        return $this
            ->filter(function ($fp) use ($species) {
                return $fp->product->species_code === $species->species_code;
            })
            ->sortBy(function ($fp) {
                return $fp->getPackagingStyle();
            })
            ->groupByPackagingStyle()
            ->map(function ($grouped) {
                return $grouped->first()->getPackagingStyle();
            })
            ->values()
            ->map(function ($packaging_style) {
                $weight_per_number_of_heads = $packaging_style['weight_per_number_of_heads'].'g';
                if ($packaging_style['weight_per_number_of_heads'] >= 1000) {
                    $weight_per_number_of_heads = round($packaging_style['weight_per_number_of_heads'] / 1000).'kg';
                }

                return implode(' ', [
                    $weight_per_number_of_heads,
                    config('constant.master.factory_products.input_group')[$packaging_style['input_group']]
                ]);
            })
            ->all();
    }
}

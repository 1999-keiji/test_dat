<?php

declare(strict_types=1);

namespace App\Models\Master\Collections;

use Illuminate\Database\Eloquent\Collection;
use App\Models\Master\FactorySpecies;

class FactorySpeciesCollection extends Collection
{
    /**
     * API用にレスポンスパラメータの形式にコンバート
     *
     * @param  bool $can_select_on_simulation
     * @return array
     */
    public function toResponseForSearchingApi(bool $can_select_on_simulation): array
    {
        return $this
            ->filter(function ($fp) use ($can_select_on_simulation) {
                if ($can_select_on_simulation) {
                    return (bool)$fp->can_select_on_simulation;
                }

                return true;
            })
            ->map(function ($fp) {
                return $fp->toArray();
            })
            ->all();
    }

    /**
     * 工場品種コードで絞り込み
     *
     * @param  string $factory_species_code
     * @return \App\Models\Master\FactorySpecies
     */
    public function findByFactorySpeciesCode(string $factory_species_code): FactorySpecies
    {
        return $this->where('factory_species_code', $factory_species_code)->first();
    }

    /**
     * 品種コードで絞り込み
     *
     * @param  string $species_code
     * @return \App\Models\Master\Collections\FactorySpeciesCollection
     */
    public function filterBySpecies(string $species_code): FactorySpeciesCollection
    {
        return $this->where('species_code', $species_code);
    }

    /**
     * 品種でグルーピング
     *
     * @return \App\Models\Master\Collections\FactorySpeciesCollection
     */
    public function groupBySpecies(): FactorySpeciesCollection
    {
        return $this->groupBy('species_code');
    }

    /**
     * 品種コード順で並び替え
     *
     * @return \App\Models\Master\Collections\FactorySpeciesCollection
     */
    public function sortBySpecies(): FactorySpeciesCollection
    {
        return $this->sortBy(function ($fs) {
            return implode('-', [$fs->species_code, $fs->factory_species_code]);
        });
    }

    /**
     * 平均重量の取得
     *
     * @return float
     */
    public function getAverageWeight(): float
    {
        return $this->avg('weight');
    }

    /**
     * 品種ごとの平均重量を取得
     *
     * @return array
     */
    public function getAverageWeightsPerSpecies(): array
    {
        return $this->groupBy('species_code')
            ->map(function ($grouped, $species_code) {
                return [
                    'species_code' => $species_code,
                    'average_weight' => $grouped->getAverageWeight(),
                ];
            })
            ->pluck('average_weight', 'species_code')
            ->all();
    }
}

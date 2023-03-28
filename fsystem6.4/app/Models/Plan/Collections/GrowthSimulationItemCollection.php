<?php

declare(strict_types=1);

namespace App\Models\Plan\Collections;

use Illuminate\Database\Eloquent\Collection;
use App\Models\Master\FactoryGrowingStage;
use App\Models\Master\FactorySpecies;
use App\Models\Plan\GrowthSimulationItem;
use App\ValueObjects\Date\HarvestingDate;
use App\ValueObjects\Date\SimulationDate;
use App\ValueObjects\Enum\GrowingStage;

class GrowthSimulationItemCollection extends Collection
{
    /**
     * 入力グループごとに集約
     *
     * @return \App\Models\Plan\Collections\GrowthSimulationItemCollection
     */
    public function groupByInputGroup(): GrowthSimulationItemCollection
    {
        return $this
            ->map(function ($gsi) {
                $gsi->growing_stage_name = $gsi->factory_growing_stage->growing_stage_name ?? '収穫';
                return $gsi;
            })
            ->groupBy('detail_id');
    }

    /**
     * 生産シミュレーションにおける最初の移植日を取得
     *
     * @return \App\ValueObjects\Date\SimulationDate
     */
    public function getFirstPortingDate(): SimulationDate
    {
        $date = $this->where('growing_stage', GrowingStage::PORTING)
            ->sortBy(function ($gsi) {
                return implode('|', [$gsi->growing_stages_sequence_number, $gsi->date, $gsi->detail_id]);
            })
            ->first()
            ->date ?? null;

        if (is_null($date)) {
            return $this->getFirstPlantingDate();
        }

        return new SimulationDate($date);
    }

    /**
     * 生産シミュレーションにおける最初の定植日を取得
     *
     * @return \App\ValueObjects\Date\SimulationDate
     */
    public function getFirstPlantingDate(): SimulationDate
    {
        $date = $this->where('growing_stage', GrowingStage::PLANTING)
            ->sortBy(function ($gsi) {
                return implode('|', [$gsi->date, $gsi->detail_id]);
            })
            ->first()
            ->date;

        return new SimulationDate($date);
    }

    /**
     * 生産シミュレーションにおける最初の収穫日を取得
     *
     * @return \App\ValueObjects\Date\SimulationDate
     */
    public function getFirstHarvestingDate(): SimulationDate
    {
        $date = $this->where('growing_stage', GrowingStage::HARVESTING)
            ->sortBy(function ($gsi) {
                return implode('|', [$gsi->date, $gsi->detail_id]);
            })
            ->first()
            ->date;

        return new SimulationDate($date);
    }

    /**
     * 生産シミュレーションにおける最後の収穫日を取得
     *
     * @return \App\ValueObjects\Date\SimulationDate
     */
    public function getLastHarvestingDate(): SimulationDate
    {
        $date = $this->where('growing_stage', GrowingStage::HARVESTING)
            ->sortByDesc(function ($gsi) {
                return implode('|', [$gsi->date, $gsi->detail_id]);
            })
            ->first()
            ->date;

        return new SimulationDate($date);
    }

    /**
     * シミュレーションにおける収穫日(収穫年月)と収穫株数のMAPを取得
     *
     * @param  string $group_key
     * @param  \App\Models\Master\FactorySpecies $factory_species
     * @return array
     */
    public function filterHarvestingQuantities(string $group_key, FactorySpecies $factory_species): array
    {
        return $this->where('growing_stage', GrowingStage::HARVESTING)
            ->groupBy(function ($gsi) use ($group_key) {
                if ($group_key === 'date') {
                    return $gsi->date;
                }

                return HarvestingDate::parse($gsi->date)->format('Ym');
            })
            ->map(function ($grouped, $key) use ($factory_species) {
                return [
                    'quantity' => $grouped->sum('stock_number'),
                    'weight' => $grouped->sum('stock_number') * $factory_species->weight
                ];
            })
            ->all();
    }

    /**
     * 生育ステージと日付でデータを抽出
     *
     * @param  \App\Models\Master\FactoryGrowingStage $factory_growing_stage
     * @param  \App\ValueObjects\Date\SimulationDate
     * @return \App\Models\Plan\Collections\GrowthSimulationItemCollection
     */
    public function filterByGrwoingStageAndDate(
        FactoryGrowingStage $factory_growing_stage,
        SimulationDate $simulation_date
    ): GrowthSimulationItemCollection {
        return $this->where('growing_stages_sequence_number', $factory_growing_stage->sequence_number)
            ->filter(function ($gsi) use ($simulation_date) {
                return SimulationDate::parse($gsi->date)
                    ->lte($simulation_date);
            });
    }
}

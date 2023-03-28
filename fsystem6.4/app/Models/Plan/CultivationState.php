<?php

declare(strict_types=1);

namespace App\Models\Plan;

use App\Models\Model;
use App\Models\Plan\Collections\CultivationStateCollection;

class CultivationState extends Model
{
    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array $models
     * @return \App\Models\Plan\Collections\CultivationStateCollection
     */
    public function newCollection(array $models = []): CultivationStateCollection
    {
        return new CultivationStateCollection($models);
    }

    /**
     * 指定パターンの移動パネル数を取得する
     *
     * @param  int $pattern
     * @return int|null
     */
    public function getMovingPanelCountPattern(int $pattern): ?int
    {
        return $this['moving_panel_count_pattern_'.$pattern];
    }

    /**
     * 指定フロア指定パターンの移動ベッド数を取得する
     *
     * @param  int $floor
     * @param  int $pattern
     * @return int|null
     */
    public function getMovingBedCountFloorPattern(int $floor, int $pattern): ?int
    {
        return $this['moving_bed_count_floor_'.$floor.'_pattern_'.$pattern];
    }

    /**
     * 指定フロアの栽培株数を取得
     *
     * @param  int $floor
     * @return int
     */
    public function getGrowingStockQuantityByFloor(int $floor): int
    {
        $stocks = 0;
        for ($i = 1; $i <= 10; $i++) {
            $stocks += $this->getMovingBedCountFloorPattern($floor, $i) *
                $this->getMovingPanelCountPattern($i) *
                $this->number_of_holes;
        }

        return $stocks;
    }

    /**
     * 栽培株数の合計を取得
     *
     * @return int
     */
    public function getSumOfGrowingStockQuantity(): int
    {
        $stocks = 0;
        foreach (range(1, $this->floor_number) as $floor) {
            $stocks += $this->getGrowingStockQuantityByFloor($floor);
        }

        return $stocks;
    }

    /**
     * 指定フロアのパネル数を取得
     *
     * @param  int $floor
     * @return int
     */
    public function getPanelQuantityByFloor(int $floor): int
    {
        $panel_quantity = 0;
        for ($i = 1; $i <= 10; $i++) {
            $panel_quantity += $this->getMovingBedCountFloorPattern($floor, $i) *
                $this->getMovingPanelCountPattern($i);
        }

        return $panel_quantity;
    }

    /**
     * パネル数の合計を取得
     *
     * @return int
     */
    public function getSumOfPanelQuantity(): int
    {
        $panel_quantity = 0;
        foreach (range(1, $this->floor_number) as $floor) {
            $panel_quantity += $this->getPanelQuantityByFloor($floor);
        }

        return $panel_quantity;
    }
}

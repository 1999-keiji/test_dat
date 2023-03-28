<?php

declare(strict_types=1);

namespace App\Models\Plan\Collections;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as BaseCollection;
use App\Models\Master\FactoryGrowingStage;
use App\Models\Master\FactorySpecies;
use App\Models\Plan\PanelState;
use App\ValueObjects\Date\Date;
use App\ValueObjects\Date\HarvestingDate;

class PanelStateCollection extends Collection
{
    /**
     * 工場品種でデータを抽出
     *
     * @param  \App\Models\Master\FactorySpecies $factory_species
     * @return \App\Models\Plan\Collections\PanelStateCollection
     */
    public function filterByFactorySpecies(FactorySpecies $factory_species): PanelStateCollection
    {
        return $this->where('factory_code', $factory_species->factory_code)
            ->where('factory_species_code', $factory_species->factory_species_code);
    }

    /**
     * 工場生育ステージでデータを抽出
     *
     * @param  \App\Models\Master\FactoryGrowingStage $factory_growing_stage
     * @return \App\Models\Plan\Collections\PanelStateCollection
     */
    public function filterByFactoryGrowingStage(FactoryGrowingStage $factory_growing_stage): PanelStateCollection
    {
        return $this->where('factory_code', $factory_growing_stage->factory_code)
            ->where('factory_species_code', $factory_growing_stage->factory_species_code)
            ->where('growing_stage_sequence_number', $factory_growing_stage->sequence_number);
    }

    /**
     * 段でデータを抽出
     *
     * @param  int $bed_row
     * @return \App\Models\Plan\Collections\PanelStateCollection
     */
    public function filterByRow(int $bed_row): PanelStateCollection
    {
        return $this->where('bed_row', $bed_row)->values();
    }

    /**
     * 日付を条件にデータを取得
     *
     * @param  \App\ValueObjects\Date\Date $date
     * @return \stdClass|\App\Models\Plan\PanelState
     */
    public function filterByDate(Date $date)
    {
        return $this->where('date', $date->format('Y-m-d'))->first();
    }

    /**
     * 収穫日を条件にデータを取得
     *
     * @param  \App\ValueObjects\Date\HarvestingDate $harvesting_date
     * @return \App\Models\Plan\PanelState
     */
    public function filterByHarvestingDate(HarvestingDate $harvesting_date): ?PanelState
    {
        return $this->where('harvesting_date', $harvesting_date->format('Y-m-d'))->first();
    }

    /**
     * 収穫年月を条件にデータを取得
     *
     * @param  string $harvesting_month
     * @return \App\Models\Plan\PanelState
     */
    public function filterByHarvestingMonth(string $harvesting_month): ?PanelState
    {
        return $this->where('harvesting_month', $harvesting_month)->first();
    }

    /**
     * 階でデータを抽出
     *
     * @param  int $floor
     * @return \App\Models\Plan\Collections\PanelStateCollection
     */
    public function filterByFloor(int $floor): PanelStateCollection
    {
        return $this->where('floor', $floor);
    }

    /**
     * サイクルパターンでデータを抽出
     *
     * @param  string $pattern
     * @return \App\Models\Plan\Collections\PanelStateCollection
     */
    public function filterByPattern(string $pattern): PanelStateCollection
    {
        return $this->where('cycle_pattern', $pattern);
    }

    /**
     * ベッドの最も手前にあるデータを抽出
     *
     * @return \App\Models\Plan\Collections\PanelStateCollection
     */
    public function filterFrontestPanels(): PanelStateCollection
    {
        return $this->where('x_current_bed_position', 1)
            ->where('y_current_bed_position', 1)
            ->values();
    }

    /**
     * ベッドに投入されてから日数の経過していないデータを抽出
     *
     * @return \App\Models\Plan\Collections\PanelStateCollection
     */
    public function filterJustDropped(): PanelStateCollection
    {
        return $this->filter(function ($ps) {
            return $ps->date === $ps->stage_start_date;
        });
    }

    /**
     * 播種の次のステージのデータを抽出
     *
     * @return \App\Models\Plan\Collections\PanelStateCollection
     */
    public function filterJustAfterSeeding(): PanelStateCollection
    {
        return $this->where('growing_stage_sequence_number', 2);
    }

    /**
     * 品種ごとにグルーピング
     *
     * @return \App\Models\Plan\Collections\PanelStateCollection
     */
    public function groupBySpecies(): PanelStateCollection
    {
        return $this->groupBy('species_code');
    }

    /**
     * 工場品種ごとにグルーピング
     *
     * @return \App\Models\Plan\Collections\PanelStateCollection
     */
    public function groupByFactorySpecies(): PanelStateCollection
    {
        return $this->groupBy('factory_species_code');
    }

    /**
     * 日付ごとにグルーピング
     *
     * @return \App\Models\Plan\Collections\PanelStateCollection
     */
    public function groupByDate(): PanelStateCollection
    {
        return $this->groupBy('date');
    }

    /**
     * 段ごとにグルーピング
     *
     * @return \App\Models\Plan\Collections\PanelStateCollection
     */
    public function groupByRow(): PanelStateCollection
    {
        return $this->groupBy('bed_row');
    }

    /**
     * 列ごとにグルーピング
     *
     * @return \App\Models\Plan\Collections\PanelStateCollection
     */
    public function groupByColumn(): PanelStateCollection
    {
        return $this->groupBy('bed_column');
    }

    /**
     * 生育ステージ連番ごとにグルーピング
     *
     * @return \App\Models\Plan\Collections\PanelStateCollection
     */
    public function groupByGrowingStageSequenceNumber()
    {
        return $this->groupBy('growing_stage_sequence_number');
    }

    /**
     * 工場ごとにグルーピング
     *
     * @return \Illuminate\Support\Collection
     */
    public function groupByFactory(): BaseCollection
    {
        return $this->groupBy('factory_code')
            ->map(function ($grouped, $factory_code) {
                return (object)[
                    'factory_code' => $factory_code,
                    'factory_abbreviation' => $grouped->first()->factory_abbreviation,
                    'panel_states' => $grouped
                ];
            })
            ->sortBy(function ($f, $key) {
                return $key;
            })
            ->values();
    }

    /**
     * 日付単位の合計を算出
     *
     * @param  mixed $week
     * @param  string $date
     * @param  string $target
     * @return int|float
     */
    public function toSumPerDate($week, string $date, string $target)
    {
        return $this->pluck("summary.{$target}.{$week}.{$date}")->sum();
    }

    /**
     * 週単位の合計を算出
     *
     * @param  mixed $week
     * @param  string $target
     * @return int|float
     */
    public function toSumPerWeek($week, string $target)
    {
        return $this->pluck("summary.{$target}.{$week}.total")->sum();
    }

    /**
     * 年月単位の合計を算出
     *
     * @param  string $month
     * @param  string $target
     * @return int|float
     */
    public function toSumPerMonth(string $month, string $target)
    {
        return $this->pluck("summary.{$target}.{$month}")->sum();
    }

    /**
     * 繰越在庫の合計を算出
     *
     * @param  string $weights
     * @param  string $target
     * @return int|float
     */
    public function toSumCarryOverStocks(string $weights, string $target)
    {
        return $this->pluck("summary.{$target}.{$weights}")->sum();
    }
}

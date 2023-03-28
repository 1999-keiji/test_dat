<?php

declare(strict_types=1);

namespace App\Models\Plan\Collections;

use Illuminate\Database\Eloquent\Collection;
use App\Models\Plan\PlannedCultivationStatusWork;
use App\ValueObjects\Date\SimulationDate;
use App\ValueObjects\Enum\DisplayKubun;
use App\ValueObjects\Enum\GrowingStage;

class PlannedCultivationStatusWorkCollection extends Collection
{
    /**
     * 生育ステージ連番で抽出
     *
     * @param  int $growing_stage_sequence_number
     * @return \App\Models\Plan\Collections\PlannedCultivationStatusWorkCollection
     */
    public function filterByGrowingStageSequenceNumber(
        int $growing_stage_sequence_number
    ): PlannedCultivationStatusWorkCollection {
        return $this->where('growing_stages_sequence_number', $growing_stage_sequence_number);
    }

    /**
     * 指定された日付と生育ステージ連番のデータを取得
     *
     * @param  \App\ValueObjects\Date\SimulationDate $simulation_date
     * @param  int $growing_stage_sequence_number
     * @return \App\Models\Plan\PlannedCultivationStatusWork
     */
    public function findByDateAndGrowingStageSequenceNumber(
        SimulationDate $simulation_date,
        int $growing_stage_sequence_number
    ): ?PlannedCultivationStatusWork {
        return $this->where('date', $simulation_date->format('Y-m-d'))
            ->where('growing_stages_sequence_number', $growing_stage_sequence_number)
            ->first();
    }

    /**
     * 指定された生育ステージ連番と曜日のデータを取得
     *
     * @param  int $growing_stage_sequence_number
     * @param  int $day_of_the_week
     * @return \App\Models\Plan\PlannedCultivationStatusWork|null
     */
    public function findByGrowingStageSequenceNumberAndDayOfTheWeek(
        int $growing_stage_sequence_number,
        int $day_of_the_week
    ): ?PlannedCultivationStatusWork {
        return $this->where('growing_stages_sequence_number', $growing_stage_sequence_number)
            ->where('day_of_the_week', $day_of_the_week)
            ->first();
    }

    /**
     * 指定された生産シミュレーション日付のデータを取得(播種ステージを除く)
     *
     * @param  \App\ValueObjects\Enum\DisplayKubun $display_kubun
     * @param  \App\ValueObjects\Date\SimulationDate $simulation_date
     * @return \App\Models\Plan\Collections\PlannedCultivationStatusWorkCollection
     */
    public function filterBySimulationDateExceptSeeding(
        DisplayKubun $display_kubun,
        SimulationDate $simulation_date
    ): PlannedCultivationStatusWorkCollection {
        return $this->where('display_kubun', $display_kubun->value())
            ->where('date', $simulation_date->format('Y-m-d'))
            ->filter(function ($pcsw) {
                if (is_null($pcsw->factory_growing_stage)) {
                    return false;
                }

                return $pcsw->factory_growing_stage->growing_stage !== GrowingStage::SEEDING;
            });
    }

    /**
     * フロア別栽培株数の過不足数を取得
     *
     * @param  \App\ValueObjects\Date\SimulationDate $simulation_date
     * @param  int $growing_stage_sequence_number
     * @param  int $floor
     * @return int
     */
    public function getExcessOrDeficiencyByFloor(
        SimulationDate $simulation_date,
        int $growing_stage_sequence_number,
        int $floor
    ): int {
        $growing_stock_quantity =
            optional($this->findByDateAndGrowingStageSequenceNumber(
                $simulation_date,
                $growing_stage_sequence_number
            ))
            ->getGrowingStockQuantityByFloor($floor) ?: 0;

        $prev_growing_stock_quantity =
            optional($this->findByDateAndGrowingStageSequenceNumber(
                $simulation_date,
                ($growing_stage_sequence_number - 1)
            ))
            ->getGrowingStockQuantityByFloor($floor) ?: 0;

        return $prev_growing_stock_quantity - $growing_stock_quantity;
    }

    /**
     * 栽培株数の過不足数の合計を取得
     *
     * @param  \App\ValueObjects\Date\SimulationDate $simulation_date
     * @param  int $growing_stage_sequence_number
     * @param  int $number_of_floors
     * @return int
     */
    public function getSumOfExcessOrDeficiency(
        SimulationDate $simulation_date,
        int $growing_stage_sequence_number,
        int $number_of_floors
    ): int {
        $excess_or_deficiency = 0;
        foreach (range(1, $number_of_floors) as $floor) {
            $excess_or_deficiency += $this
                ->getExcessOrDeficiencyByFloor($simulation_date, $growing_stage_sequence_number, $floor);
        }

        return $excess_or_deficiency;
    }

    /**
     * ステージごとの移動ベッド数を示すMAPにデータを加工
     *
     * @return array
     */
    public function toMapOfStageAndBeds(): array
    {
        return $this
            ->map(function ($pcsw) {
                $patterns = $pcsw->factory_growing_stage
                    ->factory_cycle_pattern
                    ->factory_cycle_pattern_items
                    ->where('day_of_the_week', $pcsw->day_of_the_week)
                    ->values();

                $bed_number = [];
                foreach (range(1, $pcsw->floor_number) as $floor) {
                    foreach ($patterns as $index => $fcpi) {
                        $index += 1;
                        $bed_number[$floor][$fcpi->pattern] =
                            $pcsw["moving_bed_count_floor_{$floor}_pattern_{$index}"] ?: 0;
                    }
                }

                return [
                    'growing_stage_sequence_number' => $pcsw->growing_stages_sequence_number,
                    'bed_number' => $bed_number
                ];
            })
            ->pluck('bed_number', 'growing_stage_sequence_number')
            ->all();
    }

    /**
     * 生育ステージとサイクルパターンの組み合わせに加工
     *
     * @return array
     */
    public function toBedStatusOptions(): array
    {
        return $this
            ->map(function ($pcsw) {
                return [
                    'growing_stage_sequence_number' => $pcsw->growing_stages_sequence_number,
                    'growing_stage_name' => $pcsw->factory_growing_stage->growing_stage_name,
                    'label_color' => $pcsw->factory_growing_stage->label_color,
                    'number_of_holes' => $pcsw->number_of_holes,
                    'factory_cycle_pattern_items' => $pcsw->factory_growing_stage
                        ->factory_cycle_pattern
                        ->factory_cycle_pattern_items
                        ->where('day_of_the_week', $pcsw->day_of_the_week)
                        ->values()
                        ->map(function ($fcpi, $index) use ($pcsw) {
                            $index++;
                            $property = "moving_panel_count_pattern_{$index}";

                            return [
                                'pattern' => $fcpi->pattern,
                                'number_of_panels' => $pcsw->{$property}
                            ];
                        })
                        ->all()
                ];
            })
            ->values()
            ->all();
    }
}

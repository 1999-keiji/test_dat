<?php

declare(strict_types=1);

namespace App\Models\Plan\Collections;

use Illuminate\Database\Eloquent\Collection;
use App\Models\Plan\CultivationState;
use App\ValueObjects\Date\WorkingDate;

class CultivationStateCollection extends Collection
{
    /**
     * 生育ステージ連番で抽出
     *
     * @param  int $growing_stage_sequence_number
     * @return \App\Models\Plan\Collections\CultivationStateCollection
     */
    public function filterByGrowingStageSequenceNumber(
        int $growing_stage_sequence_number
    ): CultivationStateCollection {
        return $this->where('growing_stage_sequence_number', $growing_stage_sequence_number);
    }

    /**
     * 指定された日付と生育ステージ連番のデータを取得
     *
     * @param  \App\ValueObjects\Date\WorkingDate $working_date
     * @param  int $growing_stage_sequence_number
     * @return \App\Models\Plan\CultivationState|null
     */
    public function findByWorkingDateAndGrowingStageSequenceNumber(
        WorkingDate $working_date,
        int $growing_stage_sequence_number
    ): ?CultivationState {
        return $this->where('working_date', $working_date->format('Y-m-d'))
            ->where('growing_stage_sequence_number', $growing_stage_sequence_number)
            ->first();
    }

    /**
     * 指定された生育ステージ連番と曜日のデータを取得
     *
     * @param  int $growing_stage_sequence_number
     * @param  int $day_of_the_week
     * @return \App\Models\Plan\CultivationState|null
     */
    public function findByGrowingStageSequenceNumberAndDayOfTheWeek(
        int $growing_stage_sequence_number,
        int $day_of_the_week
    ): ?CultivationState {
        return $this->where('growing_stage_sequence_number', $growing_stage_sequence_number)
            ->where('day_of_the_week', $day_of_the_week)
            ->first();
    }

    /**
     * フロア別栽培株数の過不足数を取得
     *
     * @param  \App\ValueObjects\Date\WorkingDate $working_date
     * @param  int $growing_stage_sequence_number
     * @param  int $floor
     * @return int
     */
    public function getExcessOrDeficiencyByFloor(
        WorkingDate $working_date,
        int $growing_stage_sequence_number,
        int $floor
    ): int {
        $growing_stock_quantity =
            optional($this->findByWorkingDateAndGrowingStageSequenceNumber(
                $working_date,
                $growing_stage_sequence_number
            ))
            ->getGrowingStockQuantityByFloor($floor) ?: 0;

        $prev_growing_stock_quantity =
            optional($this->findByWorkingDateAndGrowingStageSequenceNumber(
                $working_date,
                ($growing_stage_sequence_number - 1)
            ))
            ->getGrowingStockQuantityByFloor($floor) ?: 0;

        return $prev_growing_stock_quantity - $growing_stock_quantity;
    }

    /**
     * 栽培株数の過不足数の合計を取得
     *
     *  @param  \App\ValueObjects\Date\WorkingDate $working_date
     * @param  int $growing_stage_sequence_number
     * @param  int $number_of_floors
     * @return int
     */
    public function getSumOfExcessOrDeficiency(
        WorkingDate $working_date,
        int $growing_stage_sequence_number,
        int $number_of_floors
    ): int {
        $excess_or_deficiency = 0;
        foreach (range(1, $number_of_floors) as $floor) {
            $excess_or_deficiency += $this
                ->getExcessOrDeficiencyByFloor($working_date, $growing_stage_sequence_number, $floor);
        }

        return $excess_or_deficiency;
    }
}

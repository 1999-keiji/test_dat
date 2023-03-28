<?php

declare(strict_types=1);

namespace App\Models\Plan\Collections;

use Illuminate\Database\Eloquent\Collection;
use App\Models\Master\FactoryBed;
use App\Models\Plan\PlannedArrangementStatusWork;
use App\ValueObjects\Date\SimulationDate;
use App\ValueObjects\Enum\DisplayKubun;

class PlannedArrangementStatusWorkCollection extends Collection
{
    /**
     * 表示区分に応じてベッドに割り当てられているパネルの状態を返却
     *
     * @param  \App\Models\Master\FactoryBed $factory_bed
     * @param  \App\ValueObjects\Enum\DisplayKubun $display_kubun
     * @return \App\Models\Plan\PlannedArrangementStatusWork
     */
    public function getAllocatedBedStatus(FactoryBed $factory_bed, DisplayKubun $display_kubun): ?array
    {
        $status = $this->where('bed_column', $factory_bed->column)
            ->filter(function ($psaw) use ($factory_bed, $display_kubun) {
                $status = $psaw["growing_stages_count_{$factory_bed->row}"];

                // シミュレーションが確定済?
                if ($display_kubun->isFixedStatus()) {
                    return $psaw->getDisplayKubun()->value() === $display_kubun->value() && ! is_null($status);
                }

                // 仕掛かり中のシミュレーションの確定表示?
                if ($psaw->getDisplayKubun()->isFixedStatus()) {
                    return ! is_null($status);
                }

                return true;
            })
            ->sortBy(function ($psaw) {
                return $psaw->getDisplayKubun()->isFixedStatus() ? 0 : 1;
            })
            ->first();

        return is_null($status) ? null : [
            'stage' => $status["growing_stages_count_{$factory_bed->row}"],
            'pattern' => $status["pattern_row_count_{$factory_bed->row}"],
            'is_fixed' => $status->getDisplayKubun()->isFixedStatus()
        ];
    }

    /**
     * 確定状態からの変更後のデータを取得
     *
     * @param  \App\Models\Master\FactoryBed $factory_bed
     * @return array
     */
    public function getReplacedBedStatus(FactoryBed $factory_bed): ?array
    {
        $status = $this->where('bed_column', $factory_bed->column)
            ->where('display_kubun', DisplayKubun::PROCESS)
            ->first();

        $stage = $status["growing_stages_count_{$factory_bed->row}"];
        return is_null($stage) ? null : [
            'stage' => $status["growing_stages_count_{$factory_bed->row}"],
            'pattern' => $status["pattern_row_count_{$factory_bed->row}"]
        ];
    }

    /**
     * 確定データを抽出
     *
     * @param  \App\ValueObjects\Date\SimulationDate $simulation_date
     * @return \App\Models\Plan\Collections\PlannedArrangementStatusWorkCollection
     */
    public function filterFixedBySimulationDate(
        SimulationDate $simulation_date
    ): PlannedArrangementStatusWorkCollection {
        return $this->where('date', $simulation_date->format('Y-m-d'))
            ->where('display_kubun', DisplayKubun::FIXED);
    }

    /**
     * 仕掛かり中のデータを抽出
     *
     * @param  \App\ValueObjects\Date\SimulationDate $simulation_date
     * @return \App\Models\Plan\Collections\PlannedArrangementStatusWorkCollection
     */
    public function filterProcessingBySimulationDate(
        SimulationDate $simulation_date
    ): PlannedArrangementStatusWorkCollection {
        return $this->where('date', $simulation_date->format('Y-m-d'))
            ->where('display_kubun', DisplayKubun::PROCESS);
    }
}

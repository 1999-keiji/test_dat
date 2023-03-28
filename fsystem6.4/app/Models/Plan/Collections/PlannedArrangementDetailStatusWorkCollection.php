<?php

declare(strict_types=1);

namespace App\Models\Plan\Collections;

use Illuminate\Database\Eloquent\Collection;
use App\Models\Master\FactoryBed;
use App\Models\Plan\GrowthSimulation;
use App\Models\Plan\PlannedArrangementDetailStatusWork;
use App\ValueObjects\Enum\DisplayKubun;

class PlannedArrangementDetailStatusWorkCollection extends Collection
{
    /**
     * 前日割当分とともに生産計画配置詳細状況作業データを取得
     *
     * @param  \App\Models\Plan\GrowthSimulation $growth_simulation
     * @param  int $bed_row
     * @param  int $bed_column
     * @return \App\Models\Plan\PlannedArrangementDetailStatusWork
     */
    public function findPlannedArrangementDetailStatusWorkWithPreviousDay(
        GrowthSimulation $growth_simulation,
        int $bed_row,
        int $bed_column
    ): PlannedArrangementDetailStatusWork {
        $statuses = $this->where('bed_row', $bed_row)
            ->where('bed_column', $bed_column)
            ->sortBy(function ($padsw) {
                return $padsw->getPrevDisplayKubun()->isFixedStatus() ? 0 : 1;
            });

        $base = $statuses->firstWhere('prev_display_kubun', $growth_simulation->getDisplayKubun()->value());

        $sum_of_panel = $base->x_coordinate_panel * $base->y_coordinate_panel;
        foreach ($statuses as $padsw) {
            $has_fixed = $padsw->getPrevDisplayKubun()->isFixedStatus();
            foreach (range(1, $sum_of_panel) as $panel) {
                $status = $padsw["prev_panel_status_{$panel}"];
                if ($has_fixed && ! is_null($status)) {
                    $base["prev_panel_status_{$panel}"] = $status;
                }
                if (! $has_fixed && is_null($base["prev_panel_status_{$panel}"])) {
                    $base["prev_panel_status_{$panel}"] = $status;
                }
            }
        }

        return $base;
    }

    /**
     * 表示区分に応じてベッドに割り当てられているパネルの詳細状態を返却
     *
     * @param  \App\Models\Master\FactoryBed $factory_bed
     * @param  \App\ValueObjects\Enum\DisplayKubun $display_kubun
     * @return array
     */
    public function getAllocatedPanelDetailStatuses(
        FactoryBed $factory_bed,
        DisplayKubun $display_kubun
    ): array {
        $sum_of_panel = $factory_bed->x_coordinate_panel * $factory_bed->y_coordinate_panel;
        return $this->where('bed_row', $factory_bed->row)
            ->where('bed_column', $factory_bed->column)
            ->filter(function ($padsw) use ($display_kubun) {
                if ($display_kubun->isFixedStatus()) {
                    return $padsw->getDisplayKubun()->value() === $display_kubun->value();
                }

                return true;
            })
            ->sortBy(function ($padsw) {
                return $padsw->getDisplayKubun()->isFixedStatus() ? 0 : 1;
            })
            ->reduce(function ($panels, $padsw) use ($sum_of_panel) {
                foreach (range(1, $sum_of_panel) as $panel) {
                    $status = $padsw["panel_status_{$panel}"];
                    if ($padsw->getDisplayKubun()->isFixedStatus()) {
                        $panels[$panel] = $status;
                    }

                    if (! $padsw->getDisplayKubun()->isFixedStatus() &&
                        $status === GrowthSimulation::DUMMY_SEQ_NUM_OF_EMPTY_PANEL) {
                        $panels[$panel] = $status;
                    }

                    if (! $padsw->getDisplayKubun()->isFixedStatus() && is_null($panels[$panel])) {
                        $panels[$panel] = $status;
                    }
                }

                return $panels;
            }, array_fill_keys(range(1, $sum_of_panel), null));
    }
}

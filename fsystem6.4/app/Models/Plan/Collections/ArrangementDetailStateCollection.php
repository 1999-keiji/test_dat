<?php

declare(strict_types=1);

namespace App\Models\Plan\Collections;

use Illuminate\Database\Eloquent\Collection;
use App\Models\Master\FactoryBed;
use App\ValueObjects\Date\WorkingDate;

class ArrangementDetailStateCollection extends Collection
{
    /**
     * 作業日でデータを抽出
     *
     * @param  \App\ValueObjects\Date\WorkingDate $working_date
     * @return \App\Models\Plan\Collections\ArrangementDetailStateCollection
     */
    public function filterByWorkingDate(WorkingDate $working_date): ArrangementDetailStateCollection
    {
        return $this->where('working_date', $working_date->format('Y-m-d'));
    }

    /**
     * 座標に応じてパネルの状態を返却
     *
     * @param  \App\Models\Master\FactoryBed $factory_bed
     * @param  \App\ValueObjects\Enum\DisplayKubun $display_kubun
     * @return array
     */
    public function getPanelStates(FactoryBed $factory_bed): array
    {
        $ads = $this->where('bed_row', $factory_bed->row)
            ->where('bed_column', $factory_bed->column)
            ->first();

        if (is_null($ads)) {
            return [];
        }

        $panels = [];
        foreach (range(1, $factory_bed->x_coordinate_panel * $factory_bed->y_coordinate_panel) as $panel) {
            $panels[$panel] = $ads["panel_status_{$panel}"];
        }

        return $panels;
    }
}

<?php

declare(strict_types=1);

namespace App\Models\Plan\Collections;

use Illuminate\Database\Eloquent\Collection;
use App\Models\Master\FactoryBed;
use App\ValueObjects\Date\WorkingDate;

class ArrangementStateCollection extends Collection
{
    /**
     * 作業日でデータを抽出
     *
     * @param  \App\ValueObjects\Date\WorkingDate $working_date
     * @return \App\Models\Plan\Collections\ArrangementStateCollection
     */
    public function filterByWorkingDate(WorkingDate $working_date): ArrangementStateCollection
    {
        return $this->where('working_date', $working_date->format('Y-m-d'));
    }

    /**
     * 座標に応じてベッドの状態を返却
     *
     * @param  \App\Models\Master\FactoryBed $factory_bed
     * @param  \App\ValueObjects\Enum\DisplayKubun $display_kubun
     * @return array|null
     */
    public function getBedState(FactoryBed $factory_bed): ?array
    {
        $as = $this->firstWhere('bed_column', $factory_bed->column);
        return is_null($as) ? null : [
            'stage' => $as["growing_stages_count_{$factory_bed->row}"],
            'pattern' => $as["pattern_row_count_{$factory_bed->row}"],
        ];
    }
}

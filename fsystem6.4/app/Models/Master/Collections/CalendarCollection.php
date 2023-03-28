<?php

declare(strict_types=1);

namespace App\Models\Master\Collections;

use Illuminate\Database\Eloquent\Collection;
use App\Models\Master\Calendar;
use App\ValueObjects\Date\WorkingDate;

class CalendarCollection extends Collection
{
    /**
     * 日付でデータを抽出
     *
     * @param  \App\ValueObjects\Date\WorkingDate $working_date
     * @return \App\Models\Master\Calendar
     */
    public function filterByDate(WorkingDate $working_date): ?Calendar
    {
        return $this->where('date', $working_date->format('Y-m-d'))->first();
    }
}

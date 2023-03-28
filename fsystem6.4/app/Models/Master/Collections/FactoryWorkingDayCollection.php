<?php

declare(strict_types=1);

namespace App\Models\Master\Collections;

use Illuminate\Database\Eloquent\Collection;
use App\ValueObjects\Date\Date;

class FactoryWorkingDayCollection extends Collection
{
    /**
     * 曜日を抽出
     *
     * @return array
     */
    public function pluckDayOfTheWeek(): array
    {
        return $this->pluck('day_of_the_week')->all();
    }

    /**
     * 曜日を抽出
     * ただし、値はISO-8601形式
     *
     * @return array
     */
    public function pluckIsoDayOfTheWeek(): array
    {
        return $this
            ->pluck('day_of_the_week')
            ->map(function ($day_of_the_week) {
                if ($day_of_the_week !== 0) {
                    return $day_of_the_week;
                }

                return Date::SUNDAY;
            })
            ->sort()
            ->values()
            ->all();
    }
}

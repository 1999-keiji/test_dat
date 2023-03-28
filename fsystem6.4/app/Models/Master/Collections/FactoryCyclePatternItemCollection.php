<?php

declare(strict_types=1);

namespace App\Models\Master\Collections;

use Illuminate\Database\Eloquent\Collection;
use App\ValueObjects\Date\Date;

class FactoryCyclePatternItemCollection extends Collection
{
    /**
     * 曜日で抽出
     *
     * @param  int $day_of_the_week
     * @return \App\Models\Master\Collections\FactoryCyclePatternItemCollection
     */
    public function filetrByDayOfTheWeek(int $day_of_the_week)
    {
        return $this->where('day_of_the_week', $day_of_the_week);
    }

    /**
     * パターンごとにグルーピング
     *
     * @return \App\Models\Master\Collections\FactoryCyclePatternItemCollection
     */
    public function groupByPattern(): FactoryCyclePatternItemCollection
    {
        $grouped = $this->groupBy('pattern')
            ->map(function ($grouped) {
                return $grouped
                    ->sortBy(function ($fspi) {
                        if ($fspi->day_of_the_week !== 0) {
                            return $fspi->day_of_the_week;
                        }

                        return  Date::SUNDAY;
                    })
                    ->values();
            });

        return new FactoryCyclePatternItemCollection($grouped);
    }

    /**
     * API用にレスポンスパラメータの形式にコンバート
     *
     * @return array
     */
    public function toResponseForSearchingApi(): array
    {
        return $this
            ->groupByPattern()
            ->map(function ($grouped, $pattern) {
                return [
                    'pattern' => $pattern,
                    'number_of_panels' => $grouped->pluck('number_of_panels', 'day_of_the_week')->all()
                ];
            })
            ->values()
            ->all();
    }
}

<?php

declare(strict_types=1);

namespace App\ValueObjects\Date;

use App\Models\Master\Factory;
use App\Models\Master\Collections\FactoryGrowingStageCollection;
use App\Models\Plan\GrowthSimulation;

class SimulationDate extends Date
{
    /**
     * 工場休を考慮して前日を取得
     *
     * @param  \App\Models\Plan\GrowthSimulation $growth_simulation
     * @param  \App\Models\Master\Factory $factory
     * @return string $format
     * @return string
     */
    public function prevDay(GrowthSimulation $growth_simulation, Factory $factory, string $format = ''): ?string
    {
        $prev_day = $this;
        while (true) {
            $prev_day = $prev_day->subDay();
            if (! $prev_day->isWorkingDay($factory)) {
                continue;
            }

            break;
        }

        if (! $growth_simulation->canSimulateOnTheDate($prev_day)) {
            return null;
        }

        return $prev_day->format($format ?: self::FORMAT);
    }

    /**
     * 工場休を考慮して翌日を取得
     *
     * @param  \App\Models\Plan\GrowthSimulation $growth_simulation
     * @param  \App\Models\Master\Factory $factory
     * @return string $format
     * @return string
     */
    public function nextDay(GrowthSimulation $growth_simulation, Factory $factory, string $format = ''): ?string
    {
        $next_day = $this;
        while (true) {
            $next_day = $next_day->addDay();
            if (! $next_day->isWorkingDay($factory)) {
                continue;
            }

            break;
        }

        if (! $growth_simulation->canSimulateOnTheDate($next_day)) {
            return null;
        }

        return $next_day->format($format ?: self::FORMAT);
    }

    /**
     * 1週間前の日付を取得
     *
     * @param  \App\Models\Plan\GrowthSimulation $growth_simulation
     * @return string
     */
    public function prevWeek(GrowthSimulation $growth_simulation, string $format = ''): ?string
    {
        $prev_week = $this->subWeek();
        if (! $growth_simulation->canSimulateOnTheDate($prev_week)) {
            return null;
        }

        return $prev_week->format($format ?: self::FORMAT);
    }

    /**
     * 1週間後の日付を取得
     *
     * @param  \App\Models\Plan\GrowthSimulation $growth_simulation
     * @return string
     */
    public function nextWeek(GrowthSimulation $growth_simulation, string $format = ''): ?string
    {
        $next_week = $this->addWeek();
        if (! $growth_simulation->canSimulateOnTheDate($next_week)) {
            return null;
        }

        return $next_week->format($format ?: self::FORMAT);
    }

    /**
     * 1ヶ月前の日付を取得
     *
     * @param  \App\Models\Plan\GrowthSimulation $growth_simulation
     * @param  \App\Models\Master\Factory $factory
     * @return string $format
     * @return string
     */
    public function prevMonth(GrowthSimulation $growth_simulation, Factory $factory, string $format = ''): ?string
    {
        $prev_month = $this->subMonth();
        while (true) {
            if (! $prev_month->isWorkingDay($factory)) {
                $prev_month = $prev_month->subDay();
                continue;
            }

            break;
        }

        if (! $growth_simulation->canSimulateOnTheDate($prev_month)) {
            return null;
        }

        return $prev_month->format($format ?: self::FORMAT);
    }

    /**
     * 1ヶ月後の日付を取得
     *
     * @param  \App\Models\Plan\GrowthSimulation $growth_simulation
     * @param  \App\Models\Master\Factory $factory
     * @return string $format
     * @return string
     */
    public function nextMonth(GrowthSimulation $growth_simulation, Factory $factory, $format = ''): ?string
    {
        $next_month = $this->addMonth();
        while (true) {
            if (! $next_month->isWorkingDay($factory)) {
                $next_month = $next_month->addDay();
                continue;
            }

            break;
        }

        if (! $growth_simulation->canSimulateOnTheDate($next_month)) {
            return null;
        }

        return $next_month->format($format ?: self::FORMAT);
    }

    /**
     * シミュレーション可能な日付を取得
     *
     * @param  \App\Models\Master\Factory $factory
     * @return array
     */
    public function getSimulatableDatesOnTheWeek(Factory $factory): array
    {
        $date = $this->startOfWeek();

        $dates = [];
        while ($date->lte($this->endOfWeek())) {
            if ($date->isWorkingDay($factory)) {
                $dates[] = $date;
            }

            $date = $date->addDay();
        }

        return $dates;
    }

    /**
     * 日付オプションの取得
     *
     * @param  \App\Models\Plan\GrowthSimulation $growth_simulation
     * @param  \App\Models\Master\Factory $factory
     * @param  string $format
     * @return array
     */
    public function options(GrowthSimulation $growth_simulation, Factory $factory, string $format = ''): array
    {
        return [
            'prev_day' => $this->prevDay($growth_simulation, $factory, $format),
            'next_day' => $this->nextDay($growth_simulation, $factory, $format),
            'prev_week' => $this->prevWeek($growth_simulation, $format),
            'next_week' => $this->nextWeek($growth_simulation, $format),
            'prev_month' => $this->prevMonth($growth_simulation, $factory, $format),
            'next_month' => $this->nextMonth($growth_simulation, $factory, $format),
            'start_of_week' => $this->startOfWeek()->format($format),
        ];
    }

    /**
     * 工場休を考慮して選択不可能な曜日を返却
     *
     * @param  \App\Models\Master\Factory $factory
     * @return array $disabled_days_of_week
     */
    public function disabledDaysOfWeek(Factory $factory): array
    {
        $days_of_week = $factory->factory_working_days->pluckDayOfTheWeek();
        return array_values(
            array_filter(range(0, self::DAYS_PER_WEEK - 1), function ($day_of_week) use ($days_of_week) {
                return ! in_array($day_of_week, $days_of_week, true);
            })
        );
    }

    /**
     * 収穫日として選択不可能である曜日を取得
     *
     * @param  \App\Models\Master\Factory $factory
     * @return array $disabled_days_of_week
     */
    public function disabledDaysOfWeekOnHarvesting(Factory $factory): array
    {
        $harvesting_day_of_week = head($factory->factory_working_days->pluckIsoDayOfTheWeek());
        return array_values(
            array_filter(range(0, self::DAYS_PER_WEEK - 1), function ($days_of_week) use ($harvesting_day_of_week) {
                return $days_of_week !== $harvesting_day_of_week;
            })
        );
    }

    /**
     * 播種日として選択不可能である曜日を取得
     *
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Master\Collections\FactoryGrowingStageCollection $factory_growing_stages
     * @return array $disabled_days_of_week
     */
    public function disabledDaysOfWeekOnSeeding(
        Factory $factory,
        FactoryGrowingStageCollection $factory_growing_stages
    ): array {
        $harvesting_day_of_week = head($factory->factory_working_days->pluckIsoDayOfTheWeek());

        $seeding_date = $this->now();
        while ((int)$seeding_date->format('w') !== $harvesting_day_of_week) {
            $seeding_date = $seeding_date->subDay();
            continue;
        }

        $seeding_date = $seeding_date->format('Y-m-d');
        foreach ($factory_growing_stages->reverse() as $fgs) {
            $seeding_date = $fgs->getGrowingStage()->getStageChangedDate($factory, $seeding_date, $fgs->growing_term);
        }

        $seeding_day_of_week = (int)$this->parse($seeding_date)->format('w');
        return array_values(
            array_filter(range(0, self::DAYS_PER_WEEK - 1), function ($days_of_week) use ($seeding_day_of_week) {
                return $days_of_week !== $seeding_day_of_week;
            })
        );
    }

    /**
     * Prepare the object for JSON serialization.
     *
     * @param  \App\Models\Plan\GrowthSimulation $growth_simulation
     * @param  \App\Models\Master\Factory $factory
     * @return string
     */
    public function toJson(GrowthSimulation $growth_simulation, Factory $factory)
    {
        return json_encode([
            'value' => $this->value(),
            'formatted' => $this->value('Y-m-d'),
            'ja' => $this->formatToJa(),
            'options' => $this->options($growth_simulation, $factory, 'Y-m-d'),
            'disabled_days_of_week' => $this->disabledDaysOfWeek($factory)
        ]);
    }
}

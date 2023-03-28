<?php

declare(strict_types=1);

namespace App\ValueObjects\Enum;

use InvalidArgumentException;
use Cake\Chronos\Chronos;
use App\Models\Master\Factory;

final class GrowingStage extends Enum
{
    public const SEEDING = 1;
    public const PORTING = 2;
    public const PLANTING = 3;
    public const HARVESTING = 4;

    protected const ENUM = [
        '播種' => self::SEEDING,
        '移植' => self::PORTING,
        '定植' => self::PLANTING,
        '収穫' => self::HARVESTING
    ];

    /**
     * 色指定を必要とする生育ステージを返却
     *
     * @return array
     */
    public function getGrowingStagesThatNeedLabelColor(): array
    {
        return [self::PORTING, self::PLANTING];
    }

    /**
     * 歩留率を必要とする生育ステージを返却
     *
     * @return array
     */
    public function getGrowingStagesThatNeedYieldRate(): array
    {
        return [self::SEEDING, self::PORTING];
    }

    /**
     * サイクルパターンを必要とする生育ステージを返却
     *
     * @return array
     */
    public function getGrowingStagesThatNeedCyclePattern(): array
    {
        return [self::PORTING, self::PLANTING];
    }

    /**
     * 次ステージへの移行日を取得
     *
     * @param  \App\Models\Master\Factory $factory
     * @param  string $date
     * @param  int $growing_term
     * @return string
     * @throws \InvalidArgumentException;
     */
    public function getNextGrowthStageDate(Factory $factory, string $date, int $growing_term): string
    {
        $date = Chronos::parse($date);
        if (! in_array($date->format('w'), $factory->factory_working_days->pluckDayOfTheWeek())) {
            throw new InvalidArgumentException('the date is not working day of the factory.');
        }

        if ($growing_term % Chronos::DAYS_PER_WEEK === 0) {
            return $date->addDays($growing_term)->format('Y-m-d');
        }
        if ($factory->factory_working_days->count() === Chronos::DAYS_PER_WEEK) {
            return $date->addDays($growing_term)->format('Y-m-d');
        }

        $stage_changing_dates = [];
        $start_of_week = $date->startOfWeek();

        $current_stage_date = $start_of_week;
        while ($current_stage_date->lte($start_of_week->endOfWeek())) {
            if (! in_array($current_stage_date->format('w'), $factory->factory_working_days->pluckDayOfTheWeek())) {
                $current_stage_date = $current_stage_date->addDay();
                continue;
            }

            $stage_changing_date = $current_stage_date->addDays($growing_term);
            while (! in_array($stage_changing_date->format('w'), $factory->factory_working_days->pluckDayOfTheWeek())
                || in_array($stage_changing_date->format('Y-m-d'), $stage_changing_dates, true)) {
                $stage_changing_date = $stage_changing_date->addDay();
            }

            $stage_changing_dates[$current_stage_date->format('ymd')] = $stage_changing_date->format('Y-m-d');
            $current_stage_date = $current_stage_date->addDay();
        }

        return $stage_changing_dates[$date->format('ymd')];
    }

    /**
     * 前ステージからの移行日を取得
     *
     * @param  App\Models\Master\Factory $factory
     * @param  string $date
     * @param  int $growing_term
     * @return string
     */
    public function getStageChangedDate(Factory $factory, string $date, int $growing_term): string
    {
        $date = Chronos::parse($date);
        if (! in_array($date->format('w'), $factory->factory_working_days->pluckDayOfTheWeek())) {
            throw new InvalidArgumentException('the date is not working day of the factory.');
        }

        if ($growing_term % Chronos::DAYS_PER_WEEK === 0) {
            return $date->subDays($growing_term)->format('Y-m-d');
        }
        if ($factory->factory_working_days->count() === Chronos::DAYS_PER_WEEK) {
            return $date->subDays($growing_term)->format('Y-m-d');
        }

        $stage_changed_dates = [];
        $end_of_week = $date->endOfWeek()->addWeek();

        $current_stage_date = $end_of_week;
        while ($current_stage_date->gte($end_of_week->startOfWeek()->subWeek())) {
            if (! in_array($current_stage_date->format('w'), $factory->factory_working_days->pluckDayOfTheWeek())) {
                $current_stage_date = $current_stage_date->subDay();
                continue;
            }

            $stage_changed_date = $current_stage_date->subDays($growing_term);
            while (! in_array($stage_changed_date->format('w'), $factory->factory_working_days->pluckDayOfTheWeek())
                || in_array($stage_changed_date->format('Y-m-d'), $stage_changed_dates, true)) {
                $stage_changed_date = $stage_changed_date->subDay();
            }

            $stage_changed_dates[$current_stage_date->format('ymd')] = $stage_changed_date->format('Y-m-d');
            $current_stage_date = $current_stage_date->subDay();
        }

        return $stage_changed_dates[$date->format('ymd')];
    }

    /**
     * @return string
     */
    public function toJson(): string
    {
        return json_encode([
            'growing_stages' => $this->all(),
            'variable_stage' => self::PORTING,
            'disabled_to_save' => [self::HARVESTING],
            'can_append_stage' => [self::SEEDING, self::PORTING],
            'can_remove_stage' => [self::PORTING],
            'need_label_color' => $this->getGrowingStagesThatNeedLabelColor(),
            'need_yield_rate' => $this->getGrowingStagesThatNeedYieldRate(),
            'need_cycle_pattern' => $this->getGrowingStagesThatNeedCyclePattern()
        ]);
    }
}

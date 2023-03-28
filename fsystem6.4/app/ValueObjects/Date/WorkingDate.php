<?php

declare(strict_types=1);

namespace App\ValueObjects\Date;

use App\Models\Master\Factory;
use App\Models\Plan\BedState;

class WorkingDate extends Date
{
    /**
     * 指定された期間だけ作業日のリストを作成して返却
     *
     * @param  int $week_term
     * @return array
     */
    public function toListOfDate(int $week_term): array
    {
        $working_date = $this;

        $last_of_list = $working_date->addWeeks($week_term)->subDay();
        while ($working_date->lte($last_of_list)) {
            $list[] = $working_date;
            $working_date = $working_date->addDay();
        }

        return $list;
    }

    /**
     * 指定された日付までの作業日のリストを取得
     *
     * @param  \App\ValueObjects\Date\WorkingDate $date_to
     * @return array
     */
    public function getWorkingDates(WorkingDate $date_to): array
    {
        $date = $this;

        $list = [];
        while ($date->lte($date_to)) {
            $list[] = $date;
            $date = $date->addDay();
        }

        return $list;
    }

    /**
     * 指定された日付までの作業日のリストを、工場休を考慮して取得
     *
     * @param  \App\ValueObjects\Date\WorkingDate $date_to
     * @param  \App\Models\Master\Factory $factory
     * @return array
     */
    public function getWorkingDatesExceptFactoryRest(WorkingDate $date_to, Factory $factory): array
    {
        $date = $this;

        $list = [];
        while ($date->lte($date_to)) {
            if ($date->isWorkingDay($factory)) {
                $list[] = $date;
            }

            $date = $date->addDay();
        }

        return $list;
    }

    /**
     * 製品化指示書に実績の数値を出力するか判定する
     *
     * @return bool
     */
    public function willOutputProductizedResult(): bool
    {
        return $this->isPassedDate();
    }

    /**
     * 工場休を考慮して前日を取得
     *
     * @param  \App\Models\Plan\BedState $bed_state
     * @param  \App\Models\Master\Factory $factory
     * @return string $format
     * @return string
     */
    public function prevDay(BedState $bed_state, Factory $factory, string $format = ''): ?string
    {
        $prev_day = $this;
        while (true) {
            $prev_day = $prev_day->subDay();
            if (! $prev_day->isWorkingDay($factory)) {
                continue;
            }

            break;
        }

        if (! $bed_state->canReferOnTheDate($prev_day)) {
            return null;
        }

        return $prev_day->format($format ?: self::FORMAT);
    }

    /**
     * 工場休を考慮して翌日を取得
     *
     * @param  \App\Models\Plan\BedState $bed_state
     * @param  \App\Models\Master\Factory $factory
     * @return string $format
     * @return string
     */
    public function nextDay(BedState $bed_state, Factory $factory, string $format = ''): ?string
    {
        $next_day = $this;
        while (true) {
            $next_day = $next_day->addDay();
            if (! $next_day->isWorkingDay($factory)) {
                continue;
            }

            break;
        }

        if (! $bed_state->canReferOnTheDate($next_day)) {
            return null;
        }

        return $next_day->format($format ?: self::FORMAT);
    }

    /**
     * 日付オプションの取得
     *
     * @param  \App\Models\Plan\BedState $bed_state
     * @param  \App\Models\Master\Factory $factory
     * @param  string $format
     * @return array
     */
    public function options(BedState $bed_state, Factory $factory, string $format = ''): array
    {
        return [
            'prev_day' => $this->prevDay($bed_state, $factory, $format),
            'next_day' => $this->nextDay($bed_state, $factory, $format)
        ];
    }

    /**
     * @param  \App\Models\Plan\BedState $bed_state
     * @param  \App\Models\Master\Factory $factory
     * @return string
     */
    public function toJson(BedState $bed_state, Factory $factory)
    {
        return json_encode([
            'value' => $this->value(),
            'formatted' => $this->value('Y-m-d'),
            'ja' => $this->formatToJa(),
            'options' => $this->options($bed_state, $factory, 'Y-m-d'),
        ]);
    }

    /**
     * Prepare the object for JSON serialization.
     *
     * @return string
     */
    public function jsonSerialize()
    {
        return [
            'date' => $this->format('Y-m-d'),
            'formatted_date' => $this->formatToJa(),
            'day' => $this->format('j')
        ];
    }
}

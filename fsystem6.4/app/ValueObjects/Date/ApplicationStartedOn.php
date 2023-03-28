<?php

declare(strict_types=1);

namespace App\ValueObjects\Date;

class ApplicationStartedOn extends Date
{
    /**
     * 適用期間が重複していないことを判定
     *
     * @param  array $terms
     * @return bool
     */
    public function isNotOverlapped(array $terms)
    {
        $overlapped = 0;
        foreach ($terms as $current => $dates) {
            $application_started_on = new ApplicationStartedOn($dates['application_started_on']);
            $application_ended_on = new ApplicationEndedOn($dates['application_ended_on']);

            $overlapped += collect($terms)
                ->reject(function ($dates, $idx) use ($current) {
                    return $idx === $current;
                })
                ->filter(function ($dates) use ($application_started_on, $application_ended_on) {
                    $start = new ApplicationStartedOn($dates['application_started_on']);
                    $end = new ApplicationEndedOn($dates['application_ended_on']);

                    return $application_started_on->lte($start) && $application_ended_on->gte($end) ||
                        $application_started_on->gte($start) && $application_ended_on->lte($end) ||
                        $application_started_on->between($start, $end, false) ||
                        $application_ended_on->between($start, $end, false);
                })
                ->isEmpty() ? 0 : 1;
        }

        return $overlapped === 0;
    }
}

<?php

declare(strict_types=1);

namespace App\Services\Master;

use App\Models\Master\Calendar;
use App\Repositories\Master\CalendarRepository;
use App\ValueObjects\Date\WorkingDate;

class CalendarService
{
    /**
     * @var \App\Repositories\Master\CalendarRepository
     */
    private $calendar_repo;

    /**
     * @param  \App\Repositories\Master\CalendarRepository $calendar_repo
     * @return void
     */
    public function __construct(CalendarRepository $calendar_repo)
    {
        $this->calendar_repo = $calendar_repo;
    }

    /**
     * カレンダー情報を取得
     *
     * @param  WorkingDate $working_date
     * @param  int $event_class
     * @return array
     */
    public function getCalendarEvents(WorkingDate $working_date, int $event_class)
    {
        $calendars = $this->calendar_repo->getCalendarEvents($working_date, $event_class);

        $date = $working_date->startOfMonth()->startOfWeek();
        $working_dates = [];

        while ($date->lte($working_date->endOfMonth()->endOfWeek())) {
            if (! isset($working_dates[$date->format('W')])) {
                $working_dates[$date->format('W')] = [];
            }

            $working_dates[$date->format('W')][] = [
                'working_date' => $date,
                'calendar' => $calendars->filterByDate($date) ?: new Calendar()
            ];

            $date = $date->addDay();
        }

        return $working_dates;
    }

    /**
     * カレンダーマスタの登録
     *
     * @param  array $params
     * @return \App\Models\Master\Calendar
     */
    public function saveCalendarEvent(array $params)
    {
        $params['remark'] = $params['remark'] ?: '';
        return $this->calendar_repo->save($params);
    }

    /**
     * カレンダーマスタの削除
     *
     * @param \App\Models\Master\Calendar $calendar
     * @return void
     */
    public function deleteCalendarEvent(Calendar $calendar): void
    {
        $calendar->delete();
    }
}

<?php

declare(strict_types=1);

namespace App\Repositories\Master;

use App\Models\Master\Calendar;
use App\Models\Master\Collections\CalendarCollection;
use App\ValueObjects\Date\WorkingDate;

class CalendarRepository
{
    /**
     * @var \App\Models\Master\Calendar
     */
    private $model;

    /**
     * @param  \App\Models\Master\Calendar
     * @return void
     */
    public function __construct(Calendar $model)
    {
        $this->model = $model;
    }

    /**
     * カレンダー情報を取得
     *
     * @param  WorkingDate $working_date
     * @param  int $event_class
     * @return \App\Models\Master\Collections\CalendarCollection
     */
    public function getCalendarEvents(WorkingDate $working_date, int $event_class): CalendarCollection
    {
        return $this->model
            ->select([
                'date',
                'event',
                'remark'
            ])
            ->whereBetween('date', [
                $working_date->firstOfMonth()->format('Y-m-d'),
                $working_date->endOfMonth()->format('Y-m-d')
            ])
            ->where('event_class', $event_class)
            ->get();
    }

    /**
     * カレンダー情報を登録
     *
     * @param  array $params
     * @return \App\Models\Master\Calendar $calendar
     */
    public function save(array $params)
    {
        $calendar = $this->model->find($params['date']);
        if (is_null($calendar)) {
            $calendar = new Calendar();
        }

        $calendar->fill($params)->save();
        return $calendar;
    }
}

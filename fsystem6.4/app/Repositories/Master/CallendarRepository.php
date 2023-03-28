<?php

declare(strict_types=1);

namespace App\Repositories\Master;

use App\Models\Master\Callendar;

class CallendarRepository
{
    /**
     * @var \App\Models\Master\Callendar
     */
    private $model;

    /**
     * @param  \App\Models\Master\Callendar
     * @return void
     */
    public function __construct(Callendar $model)
    {
        $this->model = $model;
    }

    /**
     * カレンダー情報を取得
     *
     * @params array $params
     */
    public function getCallendar($params)
    {
        return $this->model->select([
                'callendars.date',
                'callendars.event',
                'callendars.remark'
            ])
            ->where(function ($query) use ($params) {
                if ($event_class = $params['event_class']) {
                    $query->where('event_class', $event_class);
                }
            })
            ->whereBetween('date', [$params['begin_date'], $params['end_date']])
            ->get();
    }

    /**
     * カレンダー設定を削除
     *
     * @param  array $params
     */
    public function delete(array $params)
    {
        $this->model
            ->where('date', $params['date'])
            ->delete();
    }

    /**
     * カレンダー設定を作成
     *
     * @param  array $params
     */
    public function create(array $params)
    {
        return (string)$this->model->insert(array_filter($params, 'is_not_null'));
    }
}

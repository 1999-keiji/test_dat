<?php

declare(strict_types=1);

namespace App\Services\Master;

use App\Models\Master\Factory;
use App\Models\Master\FactoryRest;
use App\Repositories\Master\FactoryRestRepository;
use App\ValueObjects\Date\WorkingDate;

class FactoryRestService
{
    /**
     * @var \App\Repositories\Master\FactoryRestRepository
     */
    private $factory_rest_repo;

    /**
     * @param  \App\Repositories\Master\FactoryRestRepository $factory_rest_repo
     * @return void
     */
    public function __construct(FactoryRestRepository $factory_rest_repo)
    {
        $this->factory_rest_repo = $factory_rest_repo;
    }

    /**
     * カレンダー情報を取得
     *
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\ValueObjects\Date\WorkingDate $working_date
     * @return array
     */
    public function getFactoryRest(Factory $factory, WorkingDate $working_date)
    {
        $factory_rest_list = $this->factory_rest_repo->getFactoryRest($factory, $working_date);

        $date = $working_date->startOfMonth()->startOfWeek();
        $working_dates = [];

        while ($date->lte($working_date->endOfMonth()->endOfWeek())) {
            if (! isset($working_dates[$date->format('W')])) {
                $working_dates[$date->format('W')] = [];
            }

            $working_dates[$date->format('W')][] = [
                'working_date' => $date,
                'factory_rest' => $factory_rest_list->filterByDate($date) ?: new FactoryRest()
            ];

            $date = $date->addDay();
        }

        return $working_dates;
    }

    /**
     * 工場カレンダーマスタの登録
     *
     * @param  \App\Models\Master\Factory $factory
     * @param  array $params
     * @return \App\Models\Master\FactoryRest
     */
    public function saveFactoryRest(Factory $factory, array $params): FactoryRest
    {
        $params['factory_code'] = $factory->factory_code;
        $params['remark'] = $params['remark'] ?: '';

        return $this->factory_rest_repo->save($params);
    }
}

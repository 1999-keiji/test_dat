<?php

declare(strict_types=1);

namespace App\Repositories\Master;

use App\Models\Master\Factory;
use App\Models\Master\FactoryRest;
use App\Models\Master\Collections\FactoryRestCollection;
use App\ValueObjects\Date\WorkingDate;

class FactoryRestRepository
{
    /**
     * @var \App\Models\Master\FactoryRest
     */
    private $model;

    /**
     * @param  \App\Models\Master\FactoryRest $model
     * @return void
     */
    public function __construct(FactoryRest $model)
    {
        $this->model = $model;
    }

    /**
     * カレンダー情報を取得
     *
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\ValueObjects\Date\WorkingDate $working_date
     * @return \App\Models\Master\Collections\FactoryRestCollection
     */
    public function getFactoryRest(Factory $factory, WorkingDate $working_date): FactoryRestCollection
    {
        return $this->model
            ->select([
                'factory_rest.date',
                'factory_rest.factory_is_rest',
                'factory_rest.shipment_is_rest',
                'factory_rest.delivery_is_rest',
                'factory_rest.remark'
            ])
            ->where('factory_code', $factory->factory_code)
            ->whereBetween('date', [
                $working_date->firstOfMonth()->format('Y-m-d'),
                $working_date->endOfMonth()->format('Y-m-d')
            ])
            ->get();
    }

    /**
     * カレンダー設定を登録
     *
     * @param  array $params
     * @return \App\Models\Master\FactoryRest $factory_rest
     */
    public function save(array $params): FactoryRest
    {
        $factory_rest = $this->model
            ->where('factory_code', $params['factory_code'])
            ->where('date', $params['date'])
            ->first();

        if (is_null($factory_rest)) {
            $factory_rest = new FactoryRest();
        }

        $factory_rest->fill($params)->save();
        return $factory_rest;
    }
}

<?php

declare(strict_types=1);

namespace App\Repositories\Master;

use Illuminate\Auth\AuthManager;
use Cake\Chronos\Chronos;
use App\Models\Master\Factory;
use App\Models\Master\FactoryWorkingDay;

class FactoryWorkingDayRepository
{
    /**
     * @var \Illuminate\Auth\AuthManager
     */
    private $auth;

    /**
     * @var \App\Models\Master\FactoryWorkingDay
     */
    private $model;

    /**
     * @param \Illuminate\Auth\AuthManager $auth
     * @param  \App\Models\Master\FactoryWorkingDay $model
     * @return void
     */
    public function __construct(AuthManager $auth, FactoryWorkingDay $model)
    {
        $this->auth = $auth;
        $this->model = $model;
    }

    /**
     * 工場営業日の保存
     *
     * @param  \App\Models\Master\Factory $factory
     * @param  array $working_days
     * @return void
     */
    public function saveFactoryWorkingDays(Factory $factory, array $working_days): void
    {
        $this->model
            ->where('factory_code', $factory->factory_code)
            ->delete();

        $factory_working_days = [];
        foreach ($working_days as $day_of_the_week) {
            $factory_working_days[] = [
                'factory_code' => $factory->factory_code,
                'day_of_the_week' => $day_of_the_week,
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'created_by' => $this->auth->id(),
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => $this->auth->id()
            ];
        }

        $this->model->insert($factory_working_days);
    }
}

<?php

declare(strict_types=1);

namespace App\Repositories\Master;

use Illuminate\Auth\AuthManager;
use Cake\Chronos\Chronos;
use App\Models\Master\Factory;
use App\Models\Master\FactoryBed;

class FactoryBedRepository
{
    /**
     * @var \Illuminate\Auth\AuthManager
     */
    private $auth;

    /**
     * @var \App\Models\Master\FactoryBed
     */
    private $model;

    /**
     * @param \Illuminate\Auth\AuthManager $auth
     * @param  \App\Models\Master\FactoryBed $model
     * @return void
     */
    public function __construct(AuthManager $auth, FactoryBed $model)
    {
        $this->auth = $auth;
        $this->model = $model;
    }

    /**
     * 工場列マスタの登録
     *
     * @param  array $factory_beds
     * @return void
     */
    public function insert(array $factory_beds)
    {
        $this->model->insert(array_map(function ($fb) {
            $fb['created_by'] = $this->auth->id();
            $fb['created_at'] = Chronos::now();
            $fb['updated_by'] = $this->auth->id();
            $fb['updated_at'] = Chronos::now();

            return $fb;
        }, $factory_beds));
    }

    /**
     * 工場ベッドマスタの削除
     *
     * @param  \App\Models\Master\Factory $factory
     * @return void
     */
    public function delete(Factory $factory): void
    {
        $this->model->where('factory_code', $factory->factory_code)->delete();
    }
}

<?php

declare(strict_types=1);

namespace App\Repositories\Master;

use Illuminate\Auth\AuthManager;
use Cake\Chronos\Chronos;
use App\Models\Master\Factory;
use App\Models\Master\FactoryColumn;

class FactoryColumnRepository
{
    /**
     * @var \Illuminate\Auth\AuthManager
     */
    private $auth;

    /**
     * @var \App\Models\Master\FactoryColumn
     */
    private $model;

    /**
     * @param \Illuminate\Auth\AuthManager $auth
     * @param  \App\Models\Master\FactoryColumn $model
     * @return void
     */
    public function __construct(AuthManager $auth, FactoryColumn $model)
    {
        $this->auth = $auth;
        $this->model = $model;
    }

    /**
     * 工場列マスタの登録
     *
     * @param  array $factory_columns
     * @return void
     */
    public function insert(array $factory_columns): void
    {
        $this->model->insert(array_map(function ($fc) {
            $fc['created_by'] = $this->auth->id();
            $fc['created_at'] = Chronos::now();
            $fc['updated_by'] = $this->auth->id();
            $fc['updated_at'] = Chronos::now();

            return $fc;
        }, $factory_columns));
    }

    /**
     * 工場列マスタの削除
     *
     * @param  \App\Models\Master\Factory $factory
     * @return void
     */
    public function delete(Factory $factory): void
    {
        $this->model->where('factory_code', $factory->factory_code)->delete();
    }
}

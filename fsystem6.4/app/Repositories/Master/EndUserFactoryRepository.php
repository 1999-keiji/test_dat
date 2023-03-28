<?php

declare(strict_types=1);

namespace App\Repositories\Master;

use App\Models\Master\Factory;
use App\Models\Master\EndUser;
use App\Models\Master\EndUserFactory;
use App\Models\Master\Collections\FactoryCollection;
use App\Models\Master\Collections\EndUserCollection;

class EndUserFactoryRepository
{
    /**
     * @var \App\Models\Master\EndUserFactory
     */
    private $model;

    /**
     * @param  \App\Models\Master\EndUserFactory $model
     * @return void
     */
    public function __construct(EndUserFactory $model)
    {
        $this->model = $model;
    }

    /**
     * エンドユーザ工場マスタの登録
     *
     * @param  array $params
     * @return \App\Models\Master\EndUserFactory
     */
    public function create(array $params): EndUserFactory
    {
        return $this->model->create($params);
    }

    /**
     * エンドユーザ工場マスタの取得
     * 未登録の場合は登録
     *
     * @param  array $params
     * @return \App\Models\Master\EndUserFactory
     */
    public function findOrCreateEndUserFactory(array $params)
    {
        return $this->model->firstOrCreate($params);
    }

    /**
     * 既存の工場マスタをエンドユーザに対してまとめて紐づけ
     *
     * @param  \App\Models\Master\EndUser $end_user
     * @param  \App\Models\Master\Collections\FactoryCollection $factories
     * @return void
     */
    public function linkFactories(EndUser $end_user, FactoryCollection $factories): void
    {
        foreach ($factories as $f) {
            $this->model->create([
                'end_user_code' => $end_user->end_user_code,
                'factory_code' => $f->factory_code
            ]);
        }
    }

    /**
     * 工場マスタに対して既存のエンドユーザをまとめて紐づけ
     *
     * @param \App\Models\Master\Factory $factory
     * @param \App\Models\Master\EndUserCollection $end_users
     * @return void
     */
    public function linkEndUsers(Factory $factory, EndUserCollection $end_users): void
    {
        foreach ($end_users as $eu) {
            $this->model->create([
                'end_user_code' => $eu->end_user_code,
                'factory_code' => $factory->factory_code
            ]);
        }
    }
}

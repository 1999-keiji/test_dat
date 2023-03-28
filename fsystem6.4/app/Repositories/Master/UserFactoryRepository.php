<?php

declare(strict_types=1);

namespace App\Repositories\Master;

use Illuminate\Auth\AuthManager;
use Cake\Chronos\Chronos;
use App\Models\Master\User;
use App\Models\Master\UserFactory;

class UserFactoryRepository
{
    /**
     * @var \Illuminate\Auth\AuthManager
     */
    private $auth;

    /**
     * @var \App\Models\Master\UserFactory $model
     */
    private $model;

    /**
     * @param \Illuminate\Auth\AuthManager $auth
     * @param \App\Models\Master\UserPermission $model
     */
    public function __construct(AuthManager $auth, UserFactory $model)
    {
        $this->auth = $auth;
        $this->model = $model;
    }

    /**
     * ユーザ工場マスタの登録
     *
     * @param  \App\Models\Master\User $user
     * @param  array $factory_code_list
     * @return void
     */
    public function create(User $user, array $factory_code_list): void
    {
        $user_factories = [];
        foreach ($factory_code_list as $factory_code) {
            $user_factories[] = [
                'user_code' => $user->user_code,
                'factory_code' => $factory_code,
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'created_by' => $this->auth->id(),
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => $this->auth->id()
            ];
        }

        $this->model->insert($user_factories);
    }

    /**
     * ユーザ工場マスタの削除
     *
     * @param  \App\Models\Master\User $user
     * @return void
     */
    public function delete(User $user): void
    {
        $user->user_factories->each(function ($uf) {
            $uf->delete();
        });
    }
}

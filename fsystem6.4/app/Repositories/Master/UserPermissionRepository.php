<?php

declare(strict_types=1);

namespace App\Repositories\Master;

use Illuminate\Auth\AuthManager;
use Cake\Chronos\Chronos;
use App\Models\Master\User;
use App\Models\Master\UserPermission;

class UserPermissionRepository
{
    /**
     * @var \Illuminate\Auth\AuthManager
     */
    private $auth;

    /**
     * @var \App\Models\Master\UserPermission $model
     */
    private $model;

    /**
     * @param \Illuminate\Auth\AuthManager $auth
     * @param \App\Models\Master\UserPermission $model
     */
    public function __construct(AuthManager $auth, UserPermission $model)
    {
        $this->auth = $auth;
        $this->model = $model;
    }

    /**
     * ユーザ権限マスタの登録
     *
     * @param  \App\Models\Master\User $user
     * @param  array $permissions
     * @return void
     */
    public function create(User $user, array $permissions): void
    {
        $user_permissions = [];
        foreach ($permissions as $category => $permission) {
            $user_permissions[] = [
                'user_code' => $user->user_code,
                'category' => $category,
                'permission' => $permission,
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'created_by' => $this->auth->id(),
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => $this->auth->id()
            ];
        }

        $this->model->insert($user_permissions);
    }

    /**
     * ユーザ権限マスタの削除
     *
     * @param  \App\Models\Master\User $user
     * @return void
     */
    public function delete(User $user): void
    {
        $user->user_permissions->each(function ($up) {
            $up->delete();
        });
    }
}

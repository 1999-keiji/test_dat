<?php

declare(strict_types=1);

namespace App\Repositories\Master;

use App\Models\Master\Permission;
use App\Models\Master\User;
use App\Models\Master\Collections\PermissionCollection;

class PermissionRepository
{
    /**
     * @var \App\Models\Master\Permission
     */
    private $model;

    /**
     * @var \App\Models\Master\Permission $model
     */
    public function __construct(Permission $model)
    {
        $this->model = $model;
    }

    /**
     * 指定された所属のデフォルト権限を取得する
     *
     * @param  int $affiliation
     * @return \App\Models\Master\Collections\PermissionCollection
     */
    public function getDefaultPermissions(int $affiliation): PermissionCollection
    {
        return $this->model
            ->select([
                'permissions.category',
                'permissions.permission',
                'menus.group_name',
                'menus.category_name'
            ])
            ->join('menus', 'menus.category', '=', 'permissions.category')
            ->where('permissions.affiliation', $affiliation)
            ->orderBy('menus.tab_no', 'ASC')
            ->orderBy('menus.group_column_no', 'ASC')
            ->orderBy('menus.group_row_no', 'ASC')
            ->orderBy('menus.category_order', 'ASC')
            ->get();
    }

    /**
     * 指定されたユーザの権限情報を取得する
     *
     * @param  \App\Models\Master\User $user
     * @return \App\Models\Master\Collections\PermissionCollection
     */
    public function getCurrentPermissions(User $user): PermissionCollection
    {
        return $this->model
            ->selectRaw(
                'permissions.category, '.
                '(CASE WHEN user_permissions.permission IS NOT NULL THEN user_permissions.permission '.
                'ELSE permissions.permission END) AS permission, menus.group_name, menus.category_name'
            )
            ->join('menus', 'menus.category', '=', 'permissions.category')
            ->leftJoin('user_permissions', function ($join) use ($user) {
                $join->on('user_permissions.category', '=', 'permissions.category')
                    ->where('user_permissions.user_code', $user->user_code);
            })
            ->where('permissions.affiliation', $user->affiliation)
            ->orderBy('menus.tab_no', 'ASC')
            ->orderBy('menus.group_column_no', 'ASC')
            ->orderBy('menus.group_row_no', 'ASC')
            ->orderBy('menus.category_order', 'ASC')
            ->get();
    }
}

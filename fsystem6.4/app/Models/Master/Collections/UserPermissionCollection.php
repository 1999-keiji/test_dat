<?php

declare(strict_types=1);

namespace App\Models\Master\Collections;

use Illuminate\Database\Eloquent\Collection;

class UserPermissionCollection extends Collection
{
    /**
     * 画面にアクセスが可能かどうか判定する
     *
     * @param  string $path
     * @return bool
     */
    public function canAccess(string $path): bool
    {
        $user_permission = $this->where('category', subtract_category_from_path($path))->first();
        if (is_null($user_permission)) {
            return false;
        }

        return $user_permission->permission->canAccess();
    }

    /**
     * データ保存が可能かどうか判定する
     *
     * @param  string $path
     * @return bool
     */
    public function canSave(string $path): bool
    {
        $user_permission = $this->where('category', subtract_category_from_path($path))->first();
        if (is_null($user_permission)) {
            return false;
        }

        return $user_permission->permission->canSave();
    }
}

<?php

declare(strict_types=1);

namespace App\Models\Master;

use App\Models\Master\Collections\UserPermissionCollection;
use App\Models\Model;
use App\ValueObjects\Enum\Permission;
use App\Traits\UpdatedDatetimeObservable;
use App\Traits\AuthorObservable;

class UserPermission extends Model
{
    use UpdatedDatetimeObservable, AuthorObservable;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = ['user_code', 'category'];

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array $models
     * @return \App\Models\Master\Collections\UserPermissionCollection
     */
    public function newCollection(array $models = []): UserPermissionCollection
    {
        return new UserPermissionCollection($models);
    }

    /**
     * @return \App\ValueObjects\Enum\Permission
     */
    public function getPermissionAttribute($value): Permission
    {
        return new Permission($value);
    }
}

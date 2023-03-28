<?php

declare(strict_types=1);

namespace App\Models\Master;

use App\Models\Model;
use App\Models\Master\Collections\PermissionCollection;

class Permission extends Model
{
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = ['affiliation', 'category'];

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array $models
     * @return \App\Models\Master\Collections\PermissionCollection
     */
    public function newCollection(array $models = []): PermissionCollection
    {
        return new PermissionCollection($models);
    }
}

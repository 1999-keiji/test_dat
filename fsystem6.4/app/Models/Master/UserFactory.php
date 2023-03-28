<?php

declare(strict_types=1);

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Model;
use App\Models\Master\Collections\UserFactoryCollection;
use App\Traits\AuthorObservable;
use App\Traits\UpdatedDatetimeObservable;

class UserFactory extends Model
{
    use UpdatedDatetimeObservable, AuthorObservable;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = ['user_code', 'factory_code'];

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
     * @return \App\Models\Master\Collections\UserFactoryCollection
     */
    public function newCollection(array $models = []): UserFactoryCollection
    {
        return new UserFactoryCollection($models);
    }

    /**
     * 紐づく工場を取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function factory(): BelongsTo
    {
        return $this->belongsTo(Factory::class, 'factory_code');
    }
}

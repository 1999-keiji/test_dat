<?php

declare(strict_types=1);

namespace App\Models\Master;

use App\Models\Model;
use App\Models\Master\Collections\FactoryWorkingDayCollection;
use App\Traits\AuthorObservable;

class FactoryWorkingDay extends Model
{
    use AuthorObservable;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = ['factory_code', 'day_of_the_week'];

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
    protected $guarded = ['created_by', 'created_at'];

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array $models
     * @return \App\Models\Master\Collections\FactoryWorkingDayCollection
     */
    public function newCollection(array $models = []): FactoryWorkingDayCollection
    {
        return new FactoryWorkingDayCollection($models);
    }
}

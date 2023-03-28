<?php

declare(strict_types=1);

namespace App\Models\Master;

use App\Models\Model;
use App\Models\Master\Collections\CalendarCollection;
use App\Traits\AuthorObservable;
use App\Traits\UpdatedDatetimeObservable;

class Calendar extends Model
{
    use AuthorObservable, UpdatedDatetimeObservable;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'date';

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
    protected $guarded = [
        'created_by',
        'created_at'
    ];

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array $models
     * @return \App\Models\Master\Collections\CalendarCollection
     */
    public function newCollection(array $models = []): CalendarCollection
    {
        return new CalendarCollection($models);
    }
}

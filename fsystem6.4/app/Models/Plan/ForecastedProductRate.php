<?php

declare(strict_types=1);

namespace App\Models\Plan;

use App\Models\Model;
use App\Models\Plan\Collections\ForecastedProductRateCollection;
use App\Traits\AuthorObservable;
use App\Traits\UpdatedDatetimeObservable;

class ForecastedProductRate extends Model
{
    use AuthorObservable, UpdatedDatetimeObservable;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = ['factory_code', 'species_code', 'date'];

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
     * @return \App\Models\Plan\Collections\ForecastedProductRateCollection
     */
    public function newCollection(array $models = []): ForecastedProductRateCollection
    {
        return new ForecastedProductRateCollection($models);
    }
}

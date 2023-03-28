<?php

namespace App\Models\Stock;

use App\Models\Model;
use App\Models\Stock\Collections\CarryOverStockCollection;
use App\Traits\AuthorObservable;
use App\Traits\UpdatedDatetimeObservable;

class CarryOverStock extends Model
{
    use AuthorObservable, UpdatedDatetimeObservable;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = [
        'date',
        'factory_code',
        'species_code',
        'number_of_heads',
        'weight_per_number_of_heads',
        'input_group'
    ];

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
     * @return \App\Models\Stock\Collections\CarryOverStockCollection
     */
    public function newCollection(array $models = []): CarryOverStockCollection
    {
        return new CarryOverStockCollection($models);
    }
}

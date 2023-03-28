<?php

declare(strict_types=1);

namespace App\Models\Master;

use App\Models\Model;
use App\Models\Master\Collections\FactoryCyclePatternItemCollection;

class FactoryCyclePatternItem extends Model
{
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = [
        'factory_code',
        'cycle_pattern_sequence_number',
        'pattern',
        'day_of_the_week'
    ];

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
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array $models
     * @return \App\Models\Master\Collections\FactoryCyclePatternItemCollection
     */
    public function newCollection(array $models = []): FactoryCyclePatternItemCollection
    {
        return new FactoryCyclePatternItemCollection($models);
    }
}

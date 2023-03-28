<?php

declare(strict_types=1);

namespace App\Models\Master;

use App\Models\Model;
use App\Models\Master\Collections\FactoryProductPriceCollection;
use App\Traits\AuthorObservable;
use App\Traits\UpdatedDatetimeObservable;
use App\ValueObjects\Date\ApplicationStartedOn;
use App\ValueObjects\Decimal\UnitPrice;

class FactoryProductPrice extends Model
{
    use AuthorObservable, UpdatedDatetimeObservable;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = [
        'factory_code',
        'factory_product_sequence_number',
        'currency_code',
        'application_started_on'
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
    protected $guarded = [
        'created_by',
        'created_at'
    ];

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array $models
     * @return \App\Models\Master\Collections\FactoryProductPriceCollection
     */
    public function newCollection(array $models = []): FactoryProductPriceCollection
    {
        return new FactoryProductPriceCollection($models);
    }

    /**
     * @return \App\ValueObjects\Date\ApplicationStartedOn
     */
    public function getApplicationStartedOnAttribute($value): ApplicationStartedOn
    {
        return new ApplicationStartedOn($value);
    }

    /**
     * @return \App\ValueObjects\Date\UnitPrice
     */
    public function getUnitPriceAttribute($value): UnitPrice
    {
        return new UnitPrice($value);
    }
}

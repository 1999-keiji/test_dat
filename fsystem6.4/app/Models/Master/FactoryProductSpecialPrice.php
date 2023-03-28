<?php

declare(strict_types=1);

namespace App\Models\Master;

use App\Models\Model;
use App\Models\Master\Collections\FactoryProductSpecialPriceCollection;
use App\Traits\AuthorObservable;
use App\ValueObjects\Date\ApplicationEndedOn;
use App\ValueObjects\Date\ApplicationStartedOn;
use App\ValueObjects\Decimal\UnitPrice;

class FactoryProductSpecialPrice extends Model
{
    use AuthorObservable;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = ['delivery_destination_code', 'factory_code', 'factory_product_sequence_number', 'currency_code', 'application_started_on'];

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'delivery_destination_code',
        'factory_code',
        'factory_product_sequence_number',
        'currency_code',
        'application_started_on',
        'application_ended_on',
        'unit_price'
    ];

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array $models
     * @return \App\Models\Master\Collections\FactoryProductSpecialPriceCollection
     */
    public function newCollection(array $models = []): FactoryProductSpecialPriceCollection
    {
        return new FactoryProductSpecialPriceCollection($models);
    }

    /**
     * @return \App\ValueObjects\Date\ApplicationStartedOn
     */
    public function getApplicationStartedOnAttribute($value): ApplicationStartedOn
    {
        return new ApplicationStartedOn($value);
    }

    /**
     * @return \App\ValueObjects\Date\ApplicationStartedOn
     */
    public function getApplicationEndedOnAttribute($value): ApplicationEndedOn
    {
        return new ApplicationEndedOn($value);
    }

    /**
     * @return \App\ValueObjects\Date\UnitPrice
     */
    public function getUnitPriceAttribute($value): UnitPrice
    {
        return new UnitPrice($value);
    }
}

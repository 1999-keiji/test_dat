<?php

declare(strict_types=1);

namespace App\Models\Master;

use App\Models\Model;
use App\Models\Master\Collections\ProductSpecialPriceCollection;
use App\ValueObjects\Date\ApplicationEndedOn;
use App\ValueObjects\Date\ApplicationStartedOn;

class ProductSpecialPrice extends Model
{
    /**
     * モデルのタイムスタンプを更新するかの指示
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = ['delivery_destination_code', 'factory_code', 'product_code', 'currency_code', 'application_started_on'];

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
     * @var array
     */
    private const LINKED_COLUMNS = [
        'delivery_destination_code',
        'factory_code',
        'product_code',
        'currency_code',
        'application_started_on',
        'application_ended_on',
        'unit_price'

    ];

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array $models
     * @return \App\Models\Master\Collections\ProductSpecialPriceCollection
     */
    public function newCollection(array $models = []): ProductSpecialPriceCollection
    {
        return new ProductSpecialPriceCollection($models);
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
}

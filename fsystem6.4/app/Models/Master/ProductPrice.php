<?php

declare(strict_types=1);

namespace App\Models\Master;

use App\Models\Model;
use App\Models\Master\Collections\ProductPriceCollection;
use App\ValueObjects\Date\ApplicationStartedOn;

class ProductPrice extends Model
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
    protected $primaryKey = ['factory_code', 'product_code', 'currency_code', 'application_started_on'];

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
        'factory_code',
        'product_code',
        'currency_code',
        'application_started_on',
        'unit_price'
    ];

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array $models
     * @return \App\Models\Master\Collections\ProductPriceCollection
     */
    public function newCollection(array $models = []): ProductPriceCollection
    {
        return new ProductPriceCollection($models);
    }

    /**
     * @return \App\ValueObjects\Date\ApplicationStartedOn
     */
    public function getApplicationStartedOnAttribute($value): ApplicationStartedOn
    {
        return new ApplicationStartedOn($value);
    }
}

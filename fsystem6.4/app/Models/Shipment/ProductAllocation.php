<?php

declare(strict_types=1);

namespace App\Models\Shipment;

use App\Models\Model;
use App\Models\Shipment\Collections\ProductAllocationCollection;
use App\Traits\AuthorObservable;

class ProductAllocation extends Model
{
    use AuthorObservable;

    /**
     * The primary key for the model.
     *
     * @var array
     */
    protected $primaryKey = [
        'factory_code',
        'species_code',
        'harvesting_date',
        'order_number'
    ];

    /**
     * 日付の形式変更
     *
     * @var array
     */
    protected $dates = [
        'shipping_date',
        'harvesting_date',
        'delivery_date'
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
     * @return \App\Models\Shipment\Collections\ProductAllocationCollection
     */
    public function newCollection(array $models = []): ProductAllocationCollection
    {
        return new ProductAllocationCollection($models);
    }
}

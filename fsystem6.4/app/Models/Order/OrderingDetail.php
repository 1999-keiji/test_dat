<?php

declare(strict_types=1);

namespace App\Models\Order;

use App\Models\Model;
use App\Models\Order\Collections\OrderingDetailCollection;
use App\Traits\AuthorObservable;
use App\Traits\UpdatedDatetimeObservable;

class OrderingDetail extends Model
{
    use AuthorObservable, UpdatedDatetimeObservable;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = ['own_company_code', 'place_order_number', 'place_order_chapter_number'];

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
     * Create a new Eloquent Collection instance.
     *
     * @param  array $models
     * @return \App\Models\Order\Collections\OrderingDetailCollection
     */
    public function newCollection(array $models = []): OrderingDetailCollection
    {
        return new OrderingDetailCollection($models);
    }
}

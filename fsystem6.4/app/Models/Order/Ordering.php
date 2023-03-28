<?php

declare(strict_types=1);

namespace App\Models\Order;

use App\Models\Model;
use App\Traits\AuthorObservable;
use App\Traits\UpdatedDatetimeObservable;

class Ordering extends Model
{
    use AuthorObservable, UpdatedDatetimeObservable;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'place_order_number';

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
}

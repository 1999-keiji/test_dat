<?php

declare(strict_types=1);

namespace App\Models\Order;

use App\Models\Model;

class OrderHistory extends Model
{
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = ['order_number', 'registration_date'];

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

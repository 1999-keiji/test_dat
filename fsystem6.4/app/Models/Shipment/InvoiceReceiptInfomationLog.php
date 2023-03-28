<?php

declare(strict_types=1);

namespace App\Models\Shipment;

use App\Models\Model;
use App\Traits\AuthorObservable;

class InvoiceReceiptInfomationLog extends Model
{
    use AuthorObservable;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = ['order_number', 'sequence_number'];

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
}

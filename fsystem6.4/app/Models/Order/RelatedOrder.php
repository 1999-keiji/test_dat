<?php

declare(strict_types=1);

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Model;
use App\Traits\AuthorObservable;
use App\Traits\UpdatedDatetimeObservable;

class RelatedOrder extends Model
{
    use AuthorObservable, UpdatedDatetimeObservable;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = [
        'temporary_order_number',
        'fixed_order_number'
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
    protected $guarded = ['created_by', 'created_at'];

    /**
     * 紐づけられた仮注文を取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function temporary_order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'temporary_order_number', 'order_number');
    }

    /**
     * 紐づけられた仮注文を取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function fixed_order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'fixed_order_number', 'order_number');
    }
}

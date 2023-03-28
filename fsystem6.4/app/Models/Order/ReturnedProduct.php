<?php

declare(strict_types=1);

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Model;
use App\Models\Master\FactoryProduct;
use App\Models\Master\Product;
use App\Models\Order\Collections\ReturnedProductCollection;
use App\Traits\AuthorObservable;
use App\Traits\UpdatedDatetimeObservable;

class ReturnedProduct extends Model
{
    use AuthorObservable, UpdatedDatetimeObservable;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'order_number';

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
     * @return \App\Models\Order\Collections\ReturnedProductCollection
     */
    public function newCollection(array $models = []): ReturnedProductCollection
    {
        return new ReturnedProductCollection($models);
    }

    /**
     * 返品合価を取得
     *
     * @return float
     */
    public function getReturnedAmount(): float
    {
        return $this->unit_price * $this->quantity;
    }

    /**
     * 返品情報に紐づく注文情報を取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_number', 'order_number');
    }

    /**
     * 返品情報に紐づく商品マスタの情報を取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_code', 'product_code');
    }

    /**
     * 返品に紐づく工場取扱商品マスタの情報を取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function factory_product(): BelongsTo
    {
        return $this->belongsTo(FactoryProduct::class, 'factory_product_sequence_number', 'sequence_number')
            ->where('factory_code', $this->order->factory_code);
    }
}

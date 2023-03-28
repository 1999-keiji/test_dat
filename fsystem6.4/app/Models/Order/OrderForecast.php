<?php

declare(strict_types=1);

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Model;
use App\Models\Master\DeliveryFactoryProduct;
use App\Models\Order\Collections\OrderForecastCollection;
use App\Traits\AccessControllableWithFactories;
use App\Traits\AuthorObservable;

class OrderForecast extends Model
{
    use AccessControllableWithFactories, AuthorObservable;

    /**
     * 複数形にならないよう名前指定
     *
     * @var string
     */
    protected $table = 'order_forecast';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = ['delivery_destination_code', 'factory_code', 'factory_product_sequence_number', 'date'];

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
     * @return \App\Models\Order\Collections\OrderForecastCollection
     */
    public function newCollection(array $models = []): OrderForecastCollection
    {
        return new OrderForecastCollection($models);
    }

    /**
     * 受注フォーキャストデータに紐づく納入工場商品マスタを取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function delivery_factory_product(): BelongsTo
    {
        return $this->belongsTo(DeliveryFactoryProduct::class, 'delivery_destination_code', 'delivery_destination_code')
            ->where('factory_code', $this->factory_code)
            ->where('factory_product_sequence_number', $this->factory_product_sequence_number);
    }
}

<?php

declare(strict_types=1);

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Model;
use App\Models\Master\Collections\FactoryProductCollection;
use App\Models\Order\Order;
use App\Traits\AuthorObservable;
use App\Traits\UpdatedDatetimeObservable;

class FactoryProduct extends Model
{
    use AuthorObservable, UpdatedDatetimeObservable;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = ['factory_code', 'sequence_number'];

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

    /**
     * 削除可能なマスタかどうか判定する
     *
     * @return bool
     */
    public function isDeletable(): bool
    {
        return $this->delivery_factory_products->isEmpty() && $this->orders->isEmpty();
    }

    /**
     * 主キーをパイプつなぎにして返却
     *
     * @return string
     */
    public function getJoinedPrimaryKeys(): string
    {
        return implode('|', [
            $this->factory_code,
            $this->sequence_number
        ]);
    }

    /**
     * 工場商品の商品規格を取得
     *
     * @return array
     */
    public function getPackagingStyle(): array
    {
        return [
            'number_of_heads' => $this->number_of_heads,
            'weight_per_number_of_heads' => $this->weight_per_number_of_heads,
            'input_group' => $this->input_group
        ];
    }

    /**
     * 工場取扱商品に紐づく工場マスタの情報を取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function factory(): BelongsTo
    {
        return $this->belongsTo(Factory::class, 'factory_code');
    }

    /**
     * 工場取扱商品に紐づく商品マスタの情報を取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_code');
    }

    /**
     * 工場取扱商品に紐づく工場商品価格マスタの情報を取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function factory_product_prices(): HasMany
    {
        return $this->hasMany(FactoryProductPrice::class, 'factory_code', 'factory_code')
            ->where('factory_product_sequence_number', $this->sequence_number);
    }

    /**
     * 工場取扱商品に紐づく納入工場商品マスタの情報を取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function delivery_factory_products(): HasMany
    {
        return $this->hasMany(DeliveryFactoryProduct::class, 'factory_code', 'factory_code')
            ->where('factory_product_sequence_number', $this->sequence_number);
    }

    /**
     * 工場取扱商品に紐づく注文データを取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'factory_code', 'factory_code')
            ->where('factory_product_sequence_number', $this->sequence_number);
    }

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array $models
     * @return \App\Models\Master\Collections\FactoryProductCollection
     */
    public function newCollection(array $models = []): FactoryProductCollection
    {
        return new FactoryProductCollection($models);
    }
}

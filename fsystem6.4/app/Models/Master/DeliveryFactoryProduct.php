<?php

declare(strict_types=1);

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Model;
use App\Models\Master\Collections\DeliveryFactoryProductCollection;
use App\Traits\AccessControllableWithFactories;
use App\Traits\AuthorObservable;
use App\ValueObjects\Date\Date;
use App\ValueObjects\Decimal\UnitPrice;

class DeliveryFactoryProduct extends Model
{
    use AccessControllableWithFactories, AuthorObservable;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = ['delivery_destination_code', 'factory_code', 'factory_product_sequence_number'];

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['delivery_destination_code', 'factory_code', 'factory_product_sequence_number'];

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array $models
     * @return \App\Models\Master\Collections\DeliveryFactoryProductCollection
     */
    public function newCollection(array $models = []): DeliveryFactoryProductCollection
    {
        return new DeliveryFactoryProductCollection($models);
    }

    /**
     * 主キーをパイプつなぎにして返却
     *
     * @return string
     */
    public function getJoinedPrimaryKeys(): string
    {
        return implode('|', array_only($this->attributes, $this->getKeyName()));
    }

    /**
     * 適用される単価を取得
     *
     * @param  string $currency_code
     * @param  \App\ValueObjects\Date\Date $date
     * @return \App\ValueObjects\Decimal\UnitPrice $unit_price
     */
    public function getAppliedUnitPrice(string $currency_code, Date $date): ?UnitPrice
    {
        $unit_price = $this->factory_product_special_prices->getAppliedUnitPrice($currency_code, $date);
        if (is_null($unit_price)) {
            $unit_price = $this->factory_product->factory_product_prices->getAppliedUnitPrice($currency_code, $date);
        }

        return $unit_price;
    }

    /**
     * 納入先マスタの情報を取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function delivery_destination(): BelongsTo
    {
        return $this->belongsTo(DeliveryDestination::class, 'delivery_destination_code');
    }

    /**
     * 工場商品マスタの情報を取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function factory_product(): BelongsTo
    {
        return $this->belongsTo(FactoryProduct::class, 'factory_code', 'factory_code')
            ->where('sequence_number', $this->factory_product_sequence_number);
    }

    /**
     * 工場商品特価マスタの情報を取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function factory_product_special_prices(): HasMany
    {
        return $this->hasMany(FactoryProductSpecialPrice::class, 'delivery_destination_code', 'delivery_destination_code')
            ->where('factory_code', $this->factory_code)
            ->where('factory_product_sequence_number', $this->factory_product_sequence_number);
    }
}

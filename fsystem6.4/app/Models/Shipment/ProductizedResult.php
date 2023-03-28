<?php

declare(strict_types=1);

namespace App\Models\Shipment;

use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Model;
use App\Models\Shipment\ProductizedResultDetail;
use App\Models\Shipment\Collections\ProductizedResultCollection;
use App\Traits\AuthorObservable;
use App\Traits\UpdatedDatetimeObservable;

class ProductizedResult extends Model
{
    use AuthorObservable, UpdatedDatetimeObservable;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = [
        'factory_code',
        'species_code',
        'harvesting_date'
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
    protected $guarded = [
        'created_by',
        'created_at'
    ];

    /**
     * 日付の形式変更
     *
     * @var string
     */
    protected $dates = ['harvesting_date'];

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array $models
     * @return \App\Models\Shipment\Collections\ProductizedResultCollection
     */
    public function newCollection(array $models = []): ProductizedResultCollection
    {
        return new ProductizedResultCollection($models);
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
     * 調整後の収穫株数を取得
     *
     * @return int
     */
    public function getAdjustedHarvestingQuantity(): int
    {
        if (is_null($this->producted_quantity)) {
            return $this->harvesting_quantity + $this->forecasted_crop_failure + $this->forecasted_advanced_harvest;
        }

        return $this->harvesting_quantity + $this->crop_failure + $this->advanced_harvest;
    }

    /**
     * 製品化率を取得
     *
     * @return float
     */
    public function getProductRate(): float
    {
        $product_rate = (float)0;

        $harvesting_quantity = $this->getAdjustedHarvestingQuantity();
        if ($harvesting_quantity !== 0) {
            $product_rate = (
                ($this->producted_quantity ?: 0) / $harvesting_quantity
            ) * 100;
        }

        return $product_rate;
    }

    /**
     * 廃棄その他の数値を取得
     *
     * @return int
     */
    public function getSumOfDiscardedExceptFailure(): int
    {
        return $this->triming + $this->product_failure + $this->packing + $this->sample;
    }

    /**
     * 製品化実績に紐づく製品化実績明細を取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function productized_result_details(): HasMany
    {
        return $this->hasMany(ProductizedResultDetail::class, 'factory_code', 'factory_code')
            ->where('species_code', $this->species_code)
            ->where('harvesting_date', $this->harvesting_date);
    }
}

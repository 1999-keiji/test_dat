<?php

declare(strict_types=1);

namespace App\Models\Shipment;

use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Model;
use App\Models\Shipment\Collections\ProductizedResultDetailCollection;
use App\Models\Stock\StockResultByWarehouse;
use App\Traits\AuthorObservable;
use App\Traits\UpdatedDatetimeObservable;

class ProductizedResultDetail extends Model
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
        'harvesting_date',
        'number_of_heads',
        'weight_per_number_of_heads',
        'input_group'
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
    protected $dates = [
        'harvesting_date'
    ];

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array $models
     * @return \App\Models\Shipment\Collections\ProductizedResultDetailCollection
     */
    public function newCollection(array $models = []): ProductizedResultDetailCollection
    {
        return new ProductizedResultDetailCollection($models);
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
     * 製品化実績明細に紐づく倉庫別在庫実績を取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function stock_result_by_warehouses(): HasMany
    {
        return $this->hasMany(StockResultByWarehouse::class, 'factory_code', 'factory_code')
            ->where('species_code', $this->species_code)
            ->where('harvesting_date', $this->harvesting_date)
            ->where('number_of_heads', $this->number_of_heads)
            ->where('weight_per_number_of_heads', $this->weight_per_number_of_heads)
            ->where('input_group', $this->input_group);
    }
}

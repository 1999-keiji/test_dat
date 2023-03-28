<?php

namespace App\Models\Stock;

use App\Models\Model;
use App\Models\Stock\Collections\StocktakingDetailCollection;
use App\Traits\AuthorObservable;
use App\Traits\UpdatedDatetimeObservable;

class StocktakingDetail extends Model
{
    use AuthorObservable, UpdatedDatetimeObservable;

    /**
     * The primary key for the model.
     *
     * @var array
     */
    protected $primaryKey = 'stocktaking_id';
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
     * 引当在庫であるかどうか判定する
     *
     * @return bool
     */
    public function hasAllocated(): bool
    {
        return $this->number_of_cases !== 0;
    }

    /**
     * 在庫数を取得
     *
     * @return int
     */
    public function getStockQuantity(): int
    {
        return $this->hasAllocated() ?
            $this->stock_quantity / $this->number_of_cases :
            $this->stock_quantity;
    }

    /**
     * 実在庫数を取得
     *
     * @return int
     */
    public function getActualStockQuantity(): int
    {
        return $this->hasAllocated() ?
            $this->actual_stock_quantity / $this->number_of_cases :
            $this->actual_stock_quantity;
    }

    /**
     * 在庫差異を取得
     *
     * @return int
     */
    public function getStockDifference(): int
    {
        return $this->getStockQuantity() - $this->getActualStockQuantity();
    }

    /**
     * 重量を取得
     *
     * @return int
     */
    public function getStocktakingWeight(): int
    {
        return $this->weight_per_number_of_heads * $this->actual_stock_quantity;
    }

    /**
     * 株数を取得
     *
     * @return int
     */
    public function getSumOfStockQuantity(): int
    {
        return floor($this->number_of_heads * $this->actual_stock_quantity);
    }

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array $models
     * @return \App\Models\Stock\Collections\StocktakingDetailCollection
     */
    public function newCollection(array $models = []): StocktakingDetailCollection
    {
        return new StocktakingDetailCollection($models);
    }
}

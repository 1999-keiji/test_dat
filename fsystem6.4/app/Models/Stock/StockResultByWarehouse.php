<?php

declare(strict_types=1);

namespace App\Models\Stock;

use App\Models\Model;
use App\Models\Stock\Collections\StockResultByWarehouseCollection;
use App\Traits\AuthorObservable;
use App\Traits\UpdatedDatetimeObservable;

class StockResultByWarehouse extends Model
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
        'warehouse_code',
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
     * 調整後の在庫数量を取得
     *
     * @return int
     */
    public function getAdjustedStockQuantity(): int
    {
        return $this->product_stock_quantity + $this->adjustment_quantity;
    }

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array $models
     * @return \App\Models\Stock\Collections\StockResultByWarehouseCollection
     */
    public function newCollection(array $models = []): StockResultByWarehouseCollection
    {
        return new StockResultByWarehouseCollection($models);
    }
}

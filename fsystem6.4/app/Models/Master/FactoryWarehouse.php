<?php

declare(strict_types=1);

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Model;
use App\Models\Master\Collections\FactoryWarehouseCollection;
use App\Models\Stock\Stock;
use App\Traits\AuthorObservable;
use App\Traits\UpdatedDatetimeObservable;

class FactoryWarehouse extends Model
{
    use AuthorObservable, UpdatedDatetimeObservable;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = ['factory_code', 'warehouse_code'];

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
     * Create a new Eloquent Collection instance.
     *
     * @param  array $models
     * @return \App\Models\Master\Collections\FactoryWarehouseCollection
     */
    public function newCollection(array $models = []): FactoryWarehouseCollection
    {
        return new FactoryWarehouseCollection($models);
    }

    /**
     * 主キーをパイプつなぎにして返却
     *
     * @return string
     */
    public function getJoinedPrimaryKeys(): string
    {
        return implode('|', [$this->factory_code,$this->warehouse_code]);
    }

    /**
     * 削除可能な工場倉庫マスタであるか判定
     *
     * @return bool
     */
    public function isDeletable(): bool
    {
        if ($this->priority === 1) {
            return false;
        }

        return $this->stocks->filterNotAllocated()->isEmpty();
    }

    /**
     * 工場倉庫マスタに紐づく工場マスタの情報を取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function factory(): BelongsTo
    {
        return $this->belongsTo(Factory::class, 'factory_code');
    }

    /**
     * 工場倉庫マスタに紐づく倉庫マスタの情報を取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_code');
    }

    /**
     * 工場倉庫マスタに紐づく在庫データを取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class, 'factory_code', 'factory_code')
            ->where('warehouse_code', $this->warehouse_code);
    }
}

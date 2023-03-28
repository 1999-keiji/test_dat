<?php

declare(strict_types=1);

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Kyslik\ColumnSortable\Sortable;
use App\Models\Model;
use App\Models\Master\Collections\WarehouseCollection;
use App\Traits\AuthorObservable;
use App\Traits\UpdatedDatetimeObservable;

class Warehouse extends Model
{
    use AuthorObservable, Sortable, UpdatedDatetimeObservable;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'warehouse_code';

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
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'can_display' => 'boolean'
    ];

    /**
     * @var array
     */
    public $sortbale = ['warehouse_code', 'warehouse_name'];


    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array $models
     * @return \App\Models\Master\Collections\WarehouseCollection
     */
    public function newCollection(array $models = []): WarehouseCollection
    {
        return new WarehouseCollection($models);
    }

    /**
     * 削除可能な倉庫であるか判定
     *
     * @return bool
     */
    public function isDeletable(): bool
    {
        return $this->factory_warehouses->isEmpty() && $this->delivery_warehouses->isEmpty();
    }

    /**
     * 倉庫に紐づく工場倉庫マスタの情報を取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function factory_warehouses(): HasMany
    {
        return $this->hasMany(FactoryWarehouse::class, 'warehouse_code');
    }

    /**
     * 倉庫に紐づく納入倉庫マスタの情報を取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function delivery_warehouses(): HasMany
    {
        return $this->hasMany(DeliveryWarehouse::class, 'warehouse_code');
    }
}

<?php

declare(strict_types=1);

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Kyslik\ColumnSortable\Sortable;
use App\Models\Model;
use App\Models\Master\Collections\CorporationCollection;
use App\Models\Master\Collections\WarehouseCollection;
use App\Traits\AuthorObservable;
use App\Traits\UpdatedDatetimeObservable;

class Corporation extends Model
{
    use AuthorObservable, Sortable, UpdatedDatetimeObservable;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'corporation_code';

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
     * @var array
     */
    public $sortbale = ['corporation_code', 'corporation_name'];

    /**
     * 削除可能な法人マスタであるか判定
     *
     * @return bool
     */
    public function isDeletable(): bool
    {
        return $this->factories->isEmpty();
    }

    /**
     * 傘下の工場に紐づく倉庫をまとめて取得
     *
     * @return \App\Models\Master\Collections\WarehouseCollection
     */
    public function getWarehousesOfRelatedFactories(): WarehouseCollection
    {
        $warehouses = $this
            ->factories->map(function ($f) {
                return $f->getFactoryWarehouses();
            })
            ->flatten()
            ->unique('warehouse_code')
            ->sortBy('warehouse_code')
            ->values();

        return new WarehouseCollection($warehouses->all());
    }

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array $models
     * @return \App\Models\Master\Collections\CorporationCollection
     */
    public function newCollection(array $models = []): CorporationCollection
    {
        return new CorporationCollection($models);
    }

    /**
     * 法人マスタに紐づく工場マスタの情報を取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function factories(): HasMany
    {
        return $this->hasMany(Factory::class, 'corporation_code');
    }
}

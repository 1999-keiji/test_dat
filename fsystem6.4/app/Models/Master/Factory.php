<?php

declare(strict_types=1);

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Kyslik\ColumnSortable\Sortable;
use App\Models\Model;
use App\Models\Master\Collections\FactoryCollection;
use App\Models\Master\Collections\FactoryWarehouseCollection;
use App\Models\Master\Collections\WarehouseCollection;
use App\Models\Stock\StockManipulationControl;
use App\Traits\AuthorObservable;
use App\Traits\UpdatedDatetimeObservable;
use App\Traits\AccessControllableWithFactories;

class Factory extends Model
{
    use Sortable, AccessControllableWithFactories, AuthorObservable, UpdatedDatetimeObservable;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'factory_code';

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
     * @var array
     */
    public $sortbale = [
        'factory_code',
        'factory_name'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['needs_to_slide_printing_shipping_date' => 'boolean'];

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array $models
     * @return \App\Models\Master\Collections\FactoryCollection
     */
    public function newCollection(array $models = []): FactoryCollection
    {
        return new FactoryCollection($models);
    }

    /**
     * 削除可能な工場マスタであるか判定
     *
     * @return bool
     */
    public function isDeletable(): bool
    {
        return $this->factory_species->isEmpty()
            && $this->factory_products->isEmpty()
            && $this->product_prices->isEmpty()
            && $this->product_special_prices->isEmpty();
    }

    /**
     * 営業曜日を取得
     *
     * @return array
     */
    public function getWorkingDayOfTheWeeks(): array
    {
        return $this->factory_working_days->pluckDayOfTheWeek();
    }

    /**
     * 請求書に表示される情報が上書きされるかどうか判定する
     *
     * @return bool
     */
    public function willOverwriteOnInvoice(): bool
    {
        return ($this->invoice_corporation_name !== $this->factory_name) ||
            ($this->invoice_postal_code !== $this->postal_code) ||
            ($this->invoice_address !== $this->address) ||
            ($this->invoice_phone_number !== $this->phone_number) ||
            ($this->invoice_fax_number != $this->fax_number);
    }

    /**
     * 紐づけされたエンドユーザコードを取得
     *
     * @return array
     */
    public function getLinkedEndUserCodeList(): array
    {
        return $this->end_user_factories->pluck('end_user_code')->all();
    }

    /**
     * 工場に紐づく倉庫の情報を取得
     *
     * @return \App\Models\Master\Collections\FactoryWarehouseCollection
     */
    public function getFactoryWarehouses(): FactoryWarehouseCollection
    {
        return $this->factory_warehouses->map(function ($fw) {
            return $fw->warehouse;
        });
    }

    /**
     * 優先度の最も高い倉庫の情報を取得
     *
     * @return \App\Models\Master\Warehouse
     */
    public function getDefaultWarehouse(): Warehouse
    {
        return $this->factory_warehouses->sortByPriority()->first()->warehouse;
    }

    /**
     * 引当可能な倉庫の情報を取得
     *
     * @return \App\Models\Master\Collections\WarehouseCollection
     */
    public function getAllocatableWarehouses(): WarehouseCollection
    {
        return $this->corporation->getWarehousesOfRelatedFactories();
    }

    /**
     * 工場に紐づく法人マスタを取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function corporation(): BelongsTo
    {
        return $this->belongsTo(Corporation::class, 'corporation_code', 'corporation_code');
    }

    /**
     * 工場に紐づく営業日マスタを取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function factory_working_days(): HasMany
    {
        return $this->hasMany(FactoryWorkingDay::class, 'factory_code');
    }

    /**
     * 工場に紐づく列マスタを取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function factory_columns(): HasMany
    {
        return $this->hasMany(FactoryColumn::class, 'factory_code');
    }

    /**
     * 工場に紐づくベッドマスタを取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function factory_beds(): HasMany
    {
        return $this->hasMany(FactoryBed::class, 'factory_code');
    }

    /**
     * 工場に紐づく工場倉庫マスタを取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function factory_warehouses(): HasMany
    {
        return $this->hasMany(FactoryWarehouse::class, 'factory_code');
    }

    /**
     * 工場に紐づく工場パネルマスタを取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function factory_panels(): HasMany
    {
        return $this->hasMany(FactoryPanel::class, 'factory_code');
    }

    /**
     * 工場に紐づく商品価格マスタを取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function product_prices(): HasMany
    {
        return $this->hasMany(ProductPrice::class, 'factory_code');
    }

    /**
     * 工場に紐づく商品特価マスタを取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function product_special_prices(): HasMany
    {
        return $this->hasMany(ProductSpecialPrice::class, 'factory_code');
    }

    /**
     * 工場に紐づく工場サイクルパターンマスタを取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function factory_cycle_patterns(): HasMany
    {
        return $this->hasMany(FactoryCyclePattern::class, 'factory_code');
    }

    /**
     * 工場に紐づく工場カレンダーマスタを取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function factory_rest(): HasMany
    {
        return $this->hasMany(FactoryRest::class, 'factory_code');
    }

    /**
     * 工場に紐づくエンドユーザマスタの情報を取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function end_user_factories(): HasMany
    {
        return $this->hasMany(EndUserFactory::class, 'factory_code');
    }

    /**
     * 工場に紐づく工場取扱品種マスタの情報を取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function factory_species(): HasMany
    {
        return $this->hasMany(FactorySpecies::class, 'factory_code');
    }

    /**
     * 工場に紐づく工場取扱商品マスタの情報を取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function factory_products(): HasMany
    {
        return $this->hasMany(FactoryProduct::class, 'factory_code');
    }

    /**
     * 工場に紐づくユーザマスタを取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function user_factories(): HasMany
    {
        return $this->hasMany(UserFactory::class, 'factory_code');
    }

    /**
     * 工場に紐づく在庫棚卸制御データを取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function stock_manipulation_control(): HasOne
    {
        return $this->hasOne(StockManipulationControl::class, 'factory_code');
    }
}

<?php

declare(strict_types=1);

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Kyslik\ColumnSortable\Sortable;
use App\Models\Model;
use App\Models\Master\Collections\ProductCollection;
use App\Traits\AuthorObservable;
use App\Traits\DataLinkable;
use App\Traits\UpdatedDatetimeObservable;
use App\ValueObjects\Enum\CreatingType;

class Product extends Model
{
    use Sortable, DataLinkable, AuthorObservable, UpdatedDatetimeObservable;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'product_code';

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
        'custom_product_flag' => 'boolean',
        'lot_target_flag' => 'boolean',
        'export_target_flag' => 'boolean',
        'itm_flag1' => 'boolean',
        'itm_flag2' => 'boolean',
        'itm_flag3' => 'boolean',
        'itm_flag4' => 'boolean',
        'itm_flag5' => 'boolean'
    ];

    /**
     * @var array
     */
    public $sortbale = ['product_code', 'product_name'];

    /**
     * @var array
     */
    private const LINKED_COLUMNS = [
        'product_code',
        'species_code',
        'product_name',
        'result_addup_code',
        'result_addup_name',
        'result_addup_abbreviation',
        'product_large_category',
        'product_middle_category',
        'product_class',
        'custom_product_flag',
        'sales_order_unit',
        'sales_order_unit_quantity',
        'minimum_sales_order_unit_quantity',
        'statement_of_delivery_name',
        'pickup_slip_message',
        'lot_target_flag',
        'species_name',
        'export_target_flag',
        'net_weight',
        'gross_weight',
        'depth',
        'width',
        'height',
        'country_of_origin',
        'itm_class2',
        'itm_class3',
        'itm_class4',
        'itm_class5',
        'itm_flag1',
        'itm_flag2',
        'itm_flag3',
        'itm_flag4',
        'itm_flag5',
    ];

    /**
     * 削除可能な商品であるか判定
     *
     * @return bool
     */
    public function isDeletable(): bool
    {
        return $this->creating_type->isDeletableCreatingType() && $this->factory_products->isEmpty();
    }

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array $models
     * @return \App\Models\Master\Collections\ProductCollection
     */
    public function newCollection(array $models = []): ProductCollection
    {
        return new ProductCollection($models);
    }

    /**
     * 商品に紐づく品種マスタの情報を取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function species(): BelongsTo
    {
        return $this->belongsTo(Species::class, 'species_code');
    }

    /**
     * 商品に紐づく商品価格マスタの情報を取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function product_prices(): HasMany
    {
        return $this->hasMany(ProductPrice::class, 'product_code');
    }

    /**
     * 商品に紐づく商品特別価格マスタの情報を取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function product_special_prices(): HasMany
    {
        return $this->hasMany(ProductSpecialPrice::class, 'product_code');
    }

    /**
     * 商品に紐づく工場取扱商品マスタの情報を取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function factory_products(): HasMany
    {
        return $this->hasMany(FactoryProduct::class, 'product_code');
    }

    /**
     * @return \App\ValueObjects\Enum\CreatingType
     */
    public function getCreatingTypeAttribute($value): CreatingType
    {
        return new CreatingType($value);
    }
}

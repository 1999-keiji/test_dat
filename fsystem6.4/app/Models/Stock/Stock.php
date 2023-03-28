<?php

declare(strict_types=1);

namespace App\Models\Stock;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Model;
use App\Models\Master\Factory;
use App\Models\Master\Species;
use App\Models\Master\Warehouse;
use App\Models\Order\Order;
use App\Models\Stock\Collections\StockCollection;
use App\ValueObjects\Date\DeliveryDate;
use App\ValueObjects\Date\HarvestingDate;
use App\Traits\AuthorObservable;
use App\Traits\UpdatedDatetimeObservable;

class Stock extends Model
{
    use AuthorObservable, UpdatedDatetimeObservable;

    /**
     * @var int
     */
    private const EXPIRATION_TERM = 5;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'stock_id';

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
     * @return \App\Models\Stock\Collections\StockCollection
     */
    public function newCollection(array $models = []): StockCollection
    {
        return new StockCollection($models);
    }

    /**
     * 収穫日を取得
     *
     * @return \App\ValueObjects\Date\HarvestingDate
     */
    public function getHarvestingDate(): HarvestingDate
    {
        return HarvestingDate::parse($this->harvesting_date);
    }

    /**
     * 有効期限を取得
     *
     * @return \App\ValueObjects\Date\HarvestingDate
     */
    public function getExpiredOn(): HarvestingDate
    {
        return $this->getHarvestingDate()->addDays(self::EXPIRATION_TERM);
    }

    /**
     * 有効期限までの日数を取得
     *
     * @return int
     */
    public static function getExpirationTerm(): int
    {
        return self::EXPIRATION_TERM;
    }

    /**
     * 有効期限切れかどうか判定
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        return $this->getExpiredOn()->isPassedDate();
    }

    /**
     * 引当済かどうか判定
     *
     * @return bool
     */
    public function hasAllocated(): bool
    {
        return ! is_null($this->order_number);
    }

    /**
     * 納入日を取得
     *
     * @return \App\ValueObjects\Date\DeliveryDate
     */
    public function getDeliveryDate(): DeliveryDate
    {
        return DeliveryDate::parse($this->delivery_date);
    }

    /**
     * 廃棄数を差し引いた在庫数を取得
     *
     * @return int
     */
    public function getStockQuantityExceptDisposed(): int
    {
        return $this->stock_quantity - $this->disposal_quantity;
    }

    /**
     * 更新前後の在庫数量の差異を取得
     *
     * @return int
     */
    public function getDiffOfStockQuantity(): int
    {
        return $this->getAttribute('stock_quantity') - $this->getOriginal('stock_quantity');
    }

    /**
     * 在庫に紐づく工場マスタの情報を取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function factory(): BelongsTo
    {
        return $this->belongsTo(Factory::class, 'factory_code', 'factory_code');
    }

    /**
     * 在庫に紐づく倉庫マスタの情報を取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_code', 'warehouse_code');
    }

    /**
     * 在庫に紐づく品種マスタの情報を取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function species(): BelongsTo
    {
        return $this->belongsTo(Species::class, 'species_code', 'species_code');
    }

    /**
     * 在庫に紐づく製品化実績明細データを取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function stock_result_by_warehouse(): BelongsTo
    {
        return $this->belongsTo(StockResultByWarehouse::class, 'factory_code', 'factory_code')
            ->where('species_code', $this->species_code)
            ->where('harvesting_date', $this->harvesting_date)
            ->where('warehouse_code', $this->warehouse_code)
            ->where('number_of_heads', $this->number_of_heads)
            ->where('weight_per_number_of_heads', $this->weight_per_number_of_heads)
            ->where('input_group', $this->input_group);
    }

    /**
     * 在庫に紐づく移動元倉庫の情報を取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function departure_warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'before_warehouse_code', 'warehouse_code');
    }

    /**
     * 在庫に紐づく注文情報を取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_number', 'order_number');
    }
}

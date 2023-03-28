<?php

namespace App\Models\Stock;

use App\Models\Model;
use App\Models\Stock\Collections\StockStateCollection;
use App\ValueObjects\Date\DeliveryDate;
use App\ValueObjects\Date\HarvestingDate;
use App\Traits\AuthorObservable;
use App\Traits\UpdatedDatetimeObservable;

class StockState extends Model
{
    use AuthorObservable, UpdatedDatetimeObservable;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = ['stock_id', 'stock_date'];

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
        return HarvestingDate::parse($this->expiration_date);
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
        return ! is_null($this->delivery_destination_code);
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
     * Create a new Eloquent Collection instance.
     *
     * @param  array $models
     * @return \App\Models\Stock\Collections\StockStateCollection
     */
    public function newCollection(array $models = []): StockStateCollection
    {
        return new StockStateCollection($models);
    }
}

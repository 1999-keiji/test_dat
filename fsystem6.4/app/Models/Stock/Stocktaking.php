<?php

namespace App\Models\Stock;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Cake\Chronos\Chronos;
use App\Models\Model;
use App\Models\Master\Factory;
use App\Models\Master\Warehouse;
use App\Traits\AuthorObservable;
use App\Traits\UpdatedDatetimeObservable;

class Stocktaking extends Model
{
    use AuthorObservable, UpdatedDatetimeObservable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'stocktaking';

    /**
     * The primary key for the model.
     *
     * @var array
     */
    protected $primaryKey = ['factory_code', 'warehouse_code', 'stocktaking_month'];

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
     * 主キーをパイプつなぎにして返却
     *
     * @return string
     */
    public function getJoinedPrimaryKeys(): string
    {
        $joined_primary_keys = implode('|', array_only($this->attributes, $this->getKeyName()));
        return str_replace('/', '\\', $joined_primary_keys);
    }

    /**
     * 在庫棚卸が開始している判定
     *
     * @return bool
     */
    public function hasNotStartedYet(): bool
    {
        return ! $this->exists;
    }

    /**
     * 在庫棚卸中かどうか判定
     *
     * @return bool
     */
    public function hasBeenStocktakingNow(): bool
    {
        return ! $this->hasNotStartedYet() &&
            ! $this->hasCompleted() &&
            $this->stock_manipulation_control->stock_control_flag;
    }

    /**
     * 在庫棚卸が中断中かどうか判定
     *
     * @return bool
     */
    public function hasBeenStoppedStocktaking(): bool
    {
        return ! $this->hasNotStartedYet() &&
            ! $this->hasCompleted() &&
            ! $this->stock_manipulation_control->stock_control_flag;
    }

    /**
     * 在庫棚卸が完了しているか判定
     *
     * @return bool
     */
    public function hasCompleted(): bool
    {
        return ! is_null($this->stocktaking_comp_at);
    }

    /**
     * 棚卸完了日時を取得
     *
     * @return \Cake\Chronos\Chronos
     */
    public function getStocktakingCompletedAt(): Chronos
    {
        return Chronos::parse($this->stocktaking_comp_at);
    }

    /**
     * 紐づく工場マスタの情報を取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function factory(): BelongsTo
    {
        return $this->belongsTo(Factory::class, 'factory_code', 'factory_code');
    }

    /**
     * 紐づく倉庫マスタの情報を取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_code', 'warehouse_code');
    }

    /**
     * 紐づく明細データを取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function stocktaking_details(): HasMany
    {
        return $this->hasMany(StocktakingDetail::class, 'factory_code', 'factory_code')
            ->where('warehouse_code', $this->warehouse_code)
            ->where('stocktaking_month', $this->stocktaking_month);
    }

    /**
     * 紐づく在庫棚卸制御データを取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function stock_manipulation_control(): HasOne
    {
        return $this->hasOne(StockManipulationControl::class, 'factory_code', 'factory_code');
    }
}

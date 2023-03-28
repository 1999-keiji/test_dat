<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Model;
use App\Models\Master\Collections\FactoryCyclePatternCollection;

class FactoryCyclePattern extends Model
{
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = ['factory_code', 'sequence_number'];

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
        return implode('|', [
            $this->factory_code,
            $this->sequence_number
        ]);
    }

    /**
     * 削除可能かマスタかどうか判定する
     *
     * @return bool
     */
    public function isDeletable(): bool
    {
        return $this->factory_growing_stages->isEmpty();
    }

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array $models
     * @return \App\Models\Master\Collections\FactoryCyclePatternCollection
     */
    public function newCollection(array $models = []): FactoryCyclePatternCollection
    {
        return new FactoryCyclePatternCollection($models);
    }

    /**
     * サイクルパターンマスタに紐づく詳細データを取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function factory_cycle_pattern_items(): HasMany
    {
        return $this->hasMany(FactoryCyclePatternItem::class, 'factory_code', 'factory_code')
            ->where('cycle_pattern_sequence_number', $this->sequence_number);
    }

    /**
     * サイクルパターンマスタに紐づく工場生育ステージマスタを取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function factory_growing_stages(): HasMany
    {
        return $this->hasMany(FactoryGrowingStage::class, 'factory_code', 'factory_code')
            ->where('cycle_pattern_sequence_number', $this->sequence_number);
    }
}

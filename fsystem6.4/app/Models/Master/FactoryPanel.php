<?php

declare(strict_types=1);

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Model;
use App\Models\Master\Collections\FactoryPanelCollection;

class FactoryPanel extends Model
{
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = ['factory_code', 'number_of_holes'];

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
     * 削除可能なマスタかどうか判定する
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
     * @return \App\Models\Master\Collections\FactoryPanelCollection
     */
    public function newCollection(array $models = []): FactoryPanelCollection
    {
        return new FactoryPanelCollection($models);
    }

    /**
     * 主キーをパイプつなぎにして返却
     *
     * @return string
     */
    public function getJoinedPrimaryKeys(): string
    {
        return implode('|', [$this->factory_code, $this->number_of_holes]);
    }

    /**
     * 工場パネルに紐づく工場生育ステージマスタを取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function factory_growing_stages(): HasMany
    {
        return $this->hasMany(FactoryGrowingStage::class, 'factory_code', 'factory_code')
            ->where('number_of_holes', $this->number_of_holes);
    }
}

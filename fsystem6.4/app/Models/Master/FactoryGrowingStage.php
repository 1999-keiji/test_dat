<?php

declare(strict_types=1);

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Model;
use App\Models\Master\Collections\FactoryGrowingStageCollection;
use App\Traits\AuthorObservable;
use App\Traits\UpdatedDatetimeObservable;
use App\ValueObjects\Enum\GrowingStage;

class FactoryGrowingStage extends Model
{
    use AuthorObservable, UpdatedDatetimeObservable;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = ['factory_code', 'factory_species_code', 'sequence_number'];

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
    protected $guarded = ['created_by', 'created_at'];

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array $models
     * @return \App\Models\Master\Collections\FactoryGrowingStageCollection
     */
    public function newCollection(array $models = []): FactoryGrowingStageCollection
    {
        return new FactoryGrowingStageCollection($models);
    }

    /**
     * @return App\ValueObjects\Enum\GrowingStage
     */
    public function getGrowingStage(): GrowingStage
    {
        return new GrowingStage($this->growing_stage);
    }

    /**
     * 生育ステージマスタに紐づくサイクルパターンマスタを取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function factory_cycle_pattern(): BelongsTo
    {
        return $this->belongsTo(FactoryCyclePattern::class, 'factory_code', 'factory_code')
            ->where('sequence_number', $this->cycle_pattern_sequence_number);
    }
}

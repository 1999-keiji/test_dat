<?php

declare(strict_types=1);

namespace App\Models\Plan;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Model;
use App\Models\Master\FactoryGrowingStage;
use App\Models\Plan\Collections\GrowthSimulationItemCollection;
use App\Traits\AuthorObservable;
use App\Traits\UpdatedDatetimeObservable;

class GrowthSimulationItem extends Model
{
    use AuthorObservable, UpdatedDatetimeObservable;

    /**
     * 複数形にならないよう名前指定
     *
     * @var string
     */
    protected $table = 'growth_simulation_item';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = [
        'factory_code',
        'simulation_id',
        'factory_species_code',
        'detail_id',
        'growing_stages_sequence_number'
    ];

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
     * @return \App\Models\Plan\Collections\GrowthSimulationItemCollection
     */
    public function newCollection(array $models = []): GrowthSimulationItemCollection
    {
        return new GrowthSimulationItemCollection($models);
    }

    /**
     * 生産シミュレーション明細に紐づく工場生育ステージマスタの情報を取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function factory_growing_stage(): BelongsTo
    {
        return $this->belongsTo(FactoryGrowingStage::class, 'factory_code', 'factory_code')
            ->where('factory_species_code', $this->factory_species_code)
            ->where('sequence_number', $this->growing_stages_sequence_number);
    }
}

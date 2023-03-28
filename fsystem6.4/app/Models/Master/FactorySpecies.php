<?php

declare(strict_types=1);

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Kyslik\ColumnSortable\Sortable;
use App\Models\Model;
use App\Models\Master\Collections\FactorySpeciesCollection;
use App\Models\Plan\GrowthSimulation;
use App\Traits\AuthorObservable;
use App\Traits\UpdatedDatetimeObservable;

class FactorySpecies extends Model
{
    use Sortable, AuthorObservable, UpdatedDatetimeObservable;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = ['factory_code', 'factory_species_code'];

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
     * @var array
     */
    public $sortbale = ['factory_code', 'factory_species_code'];

    /**
     * 主キーをパイプつなぎにして返却
     *
     * @return string
     */
    public function getJoinedPrimaryKeys(): string
    {
        return implode('|', [$this->factory_code, $this->factory_species_code]);
    }

    /**
     * 削除可能なマスタかどうか判定する
     *
     * @return bool
     */
    public function isDeletable(): bool
    {
        return $this->growth_simulations->isEmpty();
    }

    /**
     * 工場取扱品種に紐づく工場マスタの情報を取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function factory(): BelongsTo
    {
        return $this->belongsTo(Factory::class, 'factory_code');
    }

    /**
     * 工場取扱品種に紐づく品種マスタの情報を取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function species(): BelongsTo
    {
        return $this->belongsTo(Species::class, 'species_code');
    }

    /**
     * 工場取扱品種に紐づく工場生育ステージマスタの情報を取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function factory_growing_stages(): HasMany
    {
        return $this->hasMany(FactoryGrowingStage::class, 'factory_code', 'factory_code')
            ->where('factory_species_code', $this->factory_species_code);
    }

    /**
     * 工場取扱品種に紐づく生産シミュレーションの情報を取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function growth_simulations(): HasMany
    {
        return $this->hasMany(GrowthSimulation::class, 'factory_code', 'factory_code')
            ->where('factory_species_code', $this->factory_species_code);
    }

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array $models
     * @return \App\Models\Master\Collections\FactorySpeciesCollection
     */
    public function newCollection(array $models = []): FactorySpeciesCollection
    {
        return new FactorySpeciesCollection($models);
    }
}

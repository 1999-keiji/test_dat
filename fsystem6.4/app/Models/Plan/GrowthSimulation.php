<?php

declare(strict_types=1);

namespace App\Models\Plan;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use App\Models\Model;
use App\Models\Master\FactorySpecies;
use App\Models\Master\Collections\FactoryGrowingStageCollection;
use App\Models\Plan\Collections\GrowthSimulationCollection;
use App\Traits\AccessControllableWithFactories;
use App\Traits\AuthorObservable;
use App\Traits\UpdatedDatetimeObservable;
use App\ValueObjects\Date\SimulationDate;
use App\ValueObjects\Enum\DisplayKubun;

class GrowthSimulation extends Model
{
    use AccessControllableWithFactories, AuthorObservable, UpdatedDatetimeObservable;

    /**
     * @var int
     */
    public const DUMMY_SEQ_NUM_OF_EMPTY_PANEL = 0;

    /**
     * @var int
     */
    public const DUMMY_SEQ_NUM_OF_OTHER_SPECIES = 99;

    /**
     * 複数形にならないよう名前指定
     *
     * @var string
     */
    protected $table = 'growth_simulation';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = ['factory_code', 'simulation_id', 'factory_species_code'];

    /**
     * 日付の形式変更
     *
     * @var string
     */
    protected $dates = [
        'work_at',
        'simulation_preparation_start_at',
        'simulation_preparation_comp_at',
        'fixed_start_at',
        'fixed_comp_at'
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
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['has_fixed'];

    /**
     * 主キーをパイプつなぎにして返却
     *
     * @return string
     */
    public function getJoinedPrimaryKeys(): string
    {
        return implode('|', array_only($this->attributes, $this->getKeyName()));
    }

    /**
     * ロックされていないシミュレーションかどうか判定する
     *
     * @return bool
     */
    public function isUnlockedSimulation(): bool
    {
        return is_null($this->work_by);
    }

    /**
     * ログインユーザーによってシミュレーション中かどうか判定する
     *
     * @return bool
     */
    public function isSimulatingByLoginedUser(): bool
    {
        return $this->work_by === Auth::user()->user_code;
    }

    /**
     * シミュレーション可能かどうか判定する
     *
     * @return bool
     */
    public function canSimulate(): bool
    {
        return $this->isUnlockedSimulation() || $this->isSimulatingByLoginedUser();
    }

    /**
     * シミュレーションの準備が完了しているかどうか判定する
     *
     * @return bool
     */
    public function hasPrepared(): bool
    {
        return ! is_null($this->simulation_preparation_comp_at);
    }

    /**
     * 確定されたシミュレーションかどうか判定する
     *
     * @return bool
     */
    public function hasFixed(): bool
    {
        return ! is_null($this->fixed_start_at);
    }

    /**
     * 確定作業中のシミュレーションかどうか判定する
     *
     * @return bool
     */
    public function isFixingSimulation(): bool
    {
        return ! is_null($this->fixed_start_at) && is_null($this->fixed_comp_at);
    }

    /**
     * 修正が不可能なシミュレーションかどうか判定する
     *
     * @return bool
     */
    public function canNotBeEdited(): bool
    {
        return ! $this->hasFixed() && ! $this->isSimulatingByLoginedUser();
    }

    /**
     * 確定/未確定状態に応じて表示区分を返却する
     *
     * @return \App\ValueObjects\Enum\DisplayKubun
     */
    public function getDisplayKubun(): DisplayKubun
    {
        return $this->hasFixed() ?
            new DisplayKubun(DisplayKubun::FIXED) :
            new DisplayKubun(DisplayKubun::PROCESS);
    }

    /**
     * 削除可能な生産シミュレーションであるか判定
     *
     * @return bool
     */
    public function isDeletable(): bool
    {
        return ! $this->hasFixed();
    }

    /**
     * 生産シミュレーションに時に選択できる日付かどうか判定する
     *
     * @param  \App\ValueObjects\Date\SimulationDate $simulation_date
     * @return bool
     */
    public function canSimulateOnTheDate(SimulationDate $simulation_date)
    {
        return $simulation_date->gte($this->getFirstPortingDate()) &&
            $simulation_date->lte($this->getLastHarvestingDate());
    }

    /**
     * 生産シミュレーションにおける最初の移植日を取得
     *
     * @return \App\ValueObjects\Date\SimulationDate
     */
    public function getFirstPortingDate()
    {
        return $this->growth_simulation_items->getFirstPortingDate();
    }

    /**
     * 生産シミュレーションにおける最初の収穫日を取得
     *
     * @return \App\ValueObjects\Date\SimulationDate
     */
    public function getFirstHarvestingDate()
    {
        return $this->growth_simulation_items->getFirstHarvestingDate();
    }

    /**
     * 生産シミュレーションにおける最後の収穫日を取得
     *
     * @return \App\ValueObjects\Date\SimulationDate
     */
    public function getLastHarvestingDate()
    {
        return $this->growth_simulation_items->getLastHarvestingDate();
    }

    /**
     * 指定された日付における工場生育ステージマスタのデータを取得
     *
     * @param  \App\ValueObjects\Date\SimulationDate $simulation_date
     * @return \App\Models\Master\Collections\FactoryGrowingStageCollection
     */
    public function getFactoryGrowingStagesOnTheDate(SimulationDate $simulation_date): FactoryGrowingStageCollection
    {
        return $this->factory_species->factory_growing_stages->toFactoryGrowingStages($simulation_date);
    }

    /**
     * 生産シミュレーションに紐づく工場取扱品種マスタの情報を取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function factory_species(): BelongsTo
    {
        return $this->belongsTo(FactorySpecies::class, 'factory_code', 'factory_code')
            ->where('factory_species_code', $this->factory_species_code);
    }

    /**
     * 生産シミュレーションに紐づく生産シミュレーション明細データを取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function growth_simulation_items(): HasMany
    {
        return $this->hasMany(GrowthSimulationItem::class, 'factory_code', 'factory_code')
            ->where('simulation_id', $this->simulation_id)
            ->where('factory_species_code', $this->factory_species_code);
    }

    /**
     * 生産シミュレーションに紐づく生産計画栽培状況作業データを取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function planned_cultivation_status_works(): HasMany
    {
        return $this->hasMany(PlannedCultivationStatusWork::class, 'factory_code', 'factory_code')
            ->where('simulation_id', $this->simulation_id)
            ->where('factory_species_code', $this->factory_species_code);
    }

    /**
     * 生産シミュレーションに紐づく生産計画配置状況作業データを取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function planned_arrangement_status_works(): HasMany
    {
        return $this->hasMany(PlannedArrangementStatusWork::class, 'factory_code', 'factory_code')
            ->where('simulation_id', $this->simulation_id)
            ->where('factory_species_code', $this->factory_species_code);
    }

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array $models
     * @return \App\Models\Plan\Collections\GrowthSimulationCollection
     */
    public function newCollection(array $models = []): GrowthSimulationCollection
    {
        return new GrowthSimulationCollection($models);
    }

    /**
     * @return bool
     */
    public function getHasFixedAttribute(): bool
    {
        return $this->hasFixed();
    }
}

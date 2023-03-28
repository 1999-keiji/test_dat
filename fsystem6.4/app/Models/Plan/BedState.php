<?php

declare(strict_types=1);

namespace App\Models\Plan;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Model;
use App\Models\Master\FactorySpecies;
use App\Models\Master\Collections\FactoryGrowingStageCollection;
use App\Traits\AccessControllableWithFactories;
use App\Traits\AuthorObservable;
use App\ValueObjects\Date\WorkingDate;

class BedState extends Model
{
    use AccessControllableWithFactories, AuthorObservable;

    /**
     * @var int
     */
    public const DUMMY_SEQ_NUM_OF_OTHER_SPECIES = 99;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = ['factory_code', 'factory_species_code', 'start_of_week'];

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
     * 主キーをパイプつなぎにして返却
     *
     * @return string
     */
    public function getJoinedPrimaryKeys(): string
    {
        return implode('|', array_only($this->attributes, $this->getKeyName()));
    }

    /**
     * 作業週初日の取得
     *
     * @return \App\ValueObjects\Date\WorkingDate
     */
    public function getStartOfWeek(): WorkingDate
    {
        return WorkingDate::parse($this->start_of_week);
    }

    /**
     * 作業日をまとめて取得
     *
     * @return array
     */
    public function getWorkingDates(): array
    {
        return $this->getStartOfWeek()
            ->getWorkingDatesExceptFactoryRest($this->getStartOfWeek()->endOfWeek(), $this->factory_species->factory);
    }

    /**
     * 紐づくデータの登録が完了しているかどうか判定する
     *
     * @return bool
     */
    public function hasPrepared(): bool
    {
        return ! is_null($this->completed_preparation_at);
    }

    /**
     * 参照可能な作業日かどうか判定する
     *
     * @param  \App\ValueObjects\Date\WorkingDate $working_date
     * @return bool
     */
    public function canReferOnTheDate(WorkingDate $working_date): bool
    {
        $working_dates = $this->getWorkingDates();
        return $working_date->gte(head($working_dates)) && $working_date->lte(last($working_dates));
    }

    /**
     * 指定された日付における工場生育ステージマスタのデータを取得
     *
     * @param  \App\ValueObjects\Date\WorkingDate $working_date
     * @return \App\Models\Master\Collections\FactoryGrowingStageCollection
     */
    public function getFactoryGrowingStagesOnTheDate(WorkingDate $working_date): FactoryGrowingStageCollection
    {
        return $this->factory_species->factory_growing_stages->toFactoryGrowingStages($working_date);
    }

    /**
     * ベッド状況に紐づく工場取扱品種マスタの情報を取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function factory_species(): BelongsTo
    {
        return $this->belongsTo(FactorySpecies::class, 'factory_code', 'factory_code')
            ->where('factory_species_code', $this->factory_species_code);
    }

    /**
     * ベッド状態に紐づく播種計画データを取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function seeding_plans(): HasMany
    {
        return $this->hasMany(SeedingPlan::class, 'factory_code', 'factory_code')
            ->where('factory_species_code', $this->factory_species_code)
            ->where('start_of_week', $this->start_of_week);
    }

    /**
     * ベッド状態に紐づく栽培状況データを取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cultivation_states(): HasMany
    {
        return $this->hasMany(CultivationState::class, 'factory_code', 'factory_code')
            ->where('factory_species_code', $this->factory_species_code)
            ->where('start_of_week', $this->start_of_week);
    }

    /**
     * ベッド状態に紐づく配置状況データを取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function arrangement_states(): HasMany
    {
        return $this->hasMany(ArrangementState::class, 'factory_code', 'factory_code')
            ->where('factory_species_code', $this->factory_species_code)
            ->where('start_of_week', $this->start_of_week);
    }

    /**
     * ベッド状態に紐づく配置詳細状況データを取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function arrangement_detail_states(): HasMany
    {
        return $this->hasMany(ArrangementDetailState::class, 'factory_code', 'factory_code')
            ->where('factory_species_code', $this->factory_species_code)
            ->where('start_of_week', $this->start_of_week);
    }
}

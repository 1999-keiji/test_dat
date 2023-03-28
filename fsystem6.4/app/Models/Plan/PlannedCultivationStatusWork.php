<?php

declare(strict_types=1);

namespace App\Models\Plan;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Model;
use App\Models\Master\FactoryGrowingStage;
use App\Models\Plan\Collections\PlannedCultivationStatusWorkCollection;
use App\Traits\AuthorObservable;
use App\Traits\UpdatedDatetimeObservable;

class PlannedCultivationStatusWork extends Model
{
    use AuthorObservable, UpdatedDatetimeObservable;

    /**
     * @var int
     */
    public const NUMBER_OF_FLOORS_COLUMN = 10;

    /**
     * @var string
     */
    protected $table = 'planned_cultivation_status_work';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = [
        'factory_code',
        'simulation_id',
        'factory_species_code',
        'display_kubun',
        'growing_stages_sequence_number',
        'date'
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
     * @return \App\Models\Plan\Collections\PlannedCultivationStatusWorkCollection
     */
    public function newCollection(array $models = []): PlannedCultivationStatusWorkCollection
    {
        return new PlannedCultivationStatusWorkCollection($models);
    }

    /**
     * 指定パターンの移動パネル数を取得する
     *
     * @param  int $pattern
     * @return int|null
     */
    public function getMovingPanelCountPattern(int $pattern): ?int
    {
        return $this['moving_panel_count_pattern_'.$pattern];
    }

    /**
     * 指定フロア指定パターンの移動ベッド数を取得する
     *
     * @param  int $floor
     * @param  int $pattern
     * @return int|null
     */
    public function getMovingBedCountFloorPattern(int $floor, int $pattern): ?int
    {
        return $this['moving_bed_count_floor_'.$floor.'_pattern_'.$pattern];
    }

    /**
     * 指定フロアの栽培株数を取得
     *
     * @param  int $floor
     * @return int
     */
    public function getGrowingStockQuantityByFloor(int $floor): int
    {
        $stocks = 0;
        for ($i = 1; $i <= 10; $i++) {
            $stocks += $this->getMovingBedCountFloorPattern($floor, $i) *
                $this->getMovingPanelCountPattern($i) *
                $this->number_of_holes;
        }

        return $stocks;
    }

    /**
     * 栽培株数の合計を取得
     *
     * @return int
     */
    public function getSumOfGrowingStockQuantity(): int
    {
        $stocks = 0;
        foreach (range(1, $this->floor_number) as $floor) {
            $stocks += $this->getGrowingStockQuantityByFloor($floor);
        }

        return $stocks;
    }

    /**
     * 指定フロアのパネル数を取得
     *
     * @param  int $floor
     * @return int
     */
    public function getPanelQuantityByFloor(int $floor): int
    {
        $panel_quantity = 0;
        for ($i = 1; $i <= 10; $i++) {
            $panel_quantity += $this->getMovingBedCountFloorPattern($floor, $i) *
                $this->getMovingPanelCountPattern($i);
        }

        return $panel_quantity;
    }

    /**
     * パネル数の合計を取得
     *
     * @return int
     */
    public function getSumOfPanelQuantity(): int
    {
        $panel_quantity = 0;
        foreach (range(1, $this->floor_number) as $floor) {
            $panel_quantity += $this->getPanelQuantityByFloor($floor);
        }

        return $panel_quantity;
    }

    /**
     * パターン名から移動パネル数を取得する
     *
     * @param  string $pattern
     * @param  array $factory_cycle_pattern_items
     * @return int
     */
    public function getNumberOfMovingPanelsByPatternName(string $pattern, array $factory_cycle_pattern_items): int
    {
        $index = $factory_cycle_pattern_items[$this->growing_stages_sequence_number]
            ->where('day_of_the_week', $this->day_of_the_week)
            ->values()
            ->where('pattern', $pattern)
            ->keys()
            ->first() ?? null;

        if (is_null($index)) {
            return 0;
        }

        $index = $index + 1;
        return $this["moving_panel_count_pattern_{$index}"] ?: 0;
    }

    /**
     * 指定フロア指定パターンの移動ベッド数を取得する
     *
     * @return int
     */
    public function getSumOfMovingBeds(): int
    {
        $range = range(1, self::NUMBER_OF_FLOORS_COLUMN);
        return array_reduce($range, function ($sum, $floor) {
            return $sum += $this["moving_bed_count_floor_{$floor}_sum"] ?: 0;
        }, 0);
    }

    /**
     * 生産計画配置状況作業データに紐づく工場生育ステージマスタを取得
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

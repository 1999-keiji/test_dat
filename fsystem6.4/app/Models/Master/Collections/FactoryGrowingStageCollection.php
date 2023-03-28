<?php

declare(strict_types=1);

namespace App\Models\Master\Collections;

use Illuminate\Database\Eloquent\Collection;
use App\Models\Master\Factory;
use App\Models\Master\FactoryGrowingStage;
use App\ValueObjects\Date\Date;
use App\ValueObjects\Enum\GrowingStage;

class FactoryGrowingStageCollection extends Collection
{
    /**
     * シーケンス番号で抽出
     *
     * @param  int $sequence_number
     * @return \App\Models\Master\FactoryGrowingStage
     */
    public function findBySequenceNumber(int $sequence_number): FactoryGrowingStage
    {
        return $this->firstWhere('sequence_number', $sequence_number);
    }

    /**
     * シーケンス番号で次ステージを抽出
     *
     * @param  int $sequence_number
     * @return \App\Models\Master\FactoryGrowingStage|null
     */
    public function findNextBySequenceNumber(int $sequence_number): ?FactoryGrowingStage
    {
        return $this
            ->filter(function ($fgs) use ($sequence_number) {
                return $fgs->sequence_number > $sequence_number;
            })
            ->sortBy('sequence_number')
            ->first();
    }

    /**
     * 指定されたステージよりも前段階のステージを抽出
     *
     * @param  int $sequence_number
     * @return \App\Models\Master\Collections\FactoryGrowingStageCollection
     */
    public function filterPreviousStages(int $sequence_number)
    {
        return $this->filter(function ($fgs) use ($sequence_number) {
            return $fgs->sequence_number < $sequence_number;
        });
    }

    /**
     * 播種ステージを抽出
     *
     * @return \App\Models\Master\FactoryGrowingStage
     */
    public function filterSeeding(): FactoryGrowingStage
    {
        return $this->firstWhere('growing_stage', GrowingStage::SEEDING);
    }

    /**
     * 播種ステージ以外のデータを取得
     *
     * @return \App\Models\Master\Collections\FactoryGrowingStageCollection
     */
    public function exceptSeeding(): FactoryGrowingStageCollection
    {
        return $this->reject(function ($fgs) {
            return $fgs->growing_stage === GrowingStage::SEEDING;
        });
    }

    /**
     * 生育ステージごとにグルーピング
     *
     * @return \App\Models\Master\Collections\FactoryGrowingStageCollection
     */
    public function groupByGrowingStage(): FactoryGrowingStageCollection
    {
        return $this->groupBy('growing_stage');
    }

    /**
     * 生育ステージ連番とラベルカラーのMAPに加工
     *
     * @return array
     */
    public function toStageAndLabelColorMap(): array
    {
        return $this->pluck('label_color', 'sequence_number')->all();
    }

    /**
     * 生育ステージ連番と、それに紐づくサイクルパターンのMAPに加工
     *
     * @return array
     */
    public function toStageAndCyclePatternsMap(): array
    {
        return $this
            ->reject(function ($fgs) {
                return is_null($fgs->cycle_pattern_sequence_number);
            })
            ->map(function ($fgs) {
                return [
                    'sequence_number' => $fgs->sequence_number,
                    'cycle_pattern_items' => $fgs->factory_cycle_pattern->factory_cycle_pattern_items
                ];
            })
            ->pluck('cycle_pattern_items', 'sequence_number')
            ->all();
    }

    /**
     * 生育ステージとサイクルパターンの組み合わせに加工
     *
     * @param  \App\ValueObjects\Date\Date $date
     * @return \App\Models\Master\Collections\FactoryGrowingStageCollection
     */
    public function toFactoryGrowingStages(Date $date): FactoryGrowingStageCollection
    {
        $stages = $this
            ->reject(function ($fgs) {
                return is_null($fgs->cycle_pattern_sequence_number);
            })
            ->map(function ($fgs) use ($date) {
                return [
                    'growing_stage_sequence_number' => $fgs->sequence_number,
                    'growing_stage_name' => $fgs->growing_stage_name,
                    'label_color' => $fgs->label_color,
                    'number_of_holes' => $fgs->number_of_holes,
                    'factory_cycle_pattern_items' => $fgs->factory_cycle_pattern
                        ->factory_cycle_pattern_items
                        ->where('day_of_the_week', $date->format('w') % Date::DAYS_PER_WEEK)
                        ->map(function ($fcpi) {
                            return [
                                'pattern' => $fcpi->pattern,
                                'number_of_panels' => $fcpi->number_of_panels
                            ];
                        })
                        ->values()
                ];
            })
            ->values()
            ->all();

        return new self($stages);
    }

    /**
     * 生育ステージ連番を条件にラベルカラーを取得
     *
     * @param  int $growing_stage_sequence_number
     * @return string
     */
    public function getLabelColor(int $growing_stage_sequence_number): string
    {
        return $this
            ->firstWhere('growing_stage_sequence_number', $growing_stage_sequence_number)['label_color'] ?? '';
    }

    /**
     * 生育ステージ連番を条件にパネル穴数を取得
     *
     * @param  int $growing_stage_sequence_number
     * @return int
     */
    public function getNumberOfHoles(int $growing_stage_sequence_number): int
    {
        return $this
            ->firstWhere('growing_stage_sequence_number', $growing_stage_sequence_number)['number_of_holes'] ?? 0;
    }

    /**
     * 生育ステージ連番とパターン名を条件に移動パネル数を取得
     *
     * @param  int $growing_stage_sequence_number
     * @param  string $pattern
     * @return int
     */
    public function getNumberOfPanels(int $growing_stage_sequence_number, string $pattern): int
    {
        $factory_cycle_pattern_items = $this
            ->firstWhere(
                'growing_stage_sequence_number',
                $growing_stage_sequence_number
            )['factory_cycle_pattern_items'] ?? null;

        if (is_null($factory_cycle_pattern_items)) {
            return 0;
        }

        return $factory_cycle_pattern_items->firstWhere('pattern', $pattern)['number_of_panels'] ?? 0;
    }

    /**
     * 紐づくサイクルパターンごとの週初めの平均移動パネル数を取得
     *
     * @param  \App\Models\Master\Factory $factory
     * @return array
     */
    public function getAverageOfMovingPanelsOnStartOfWeek(Factory $factory): array
    {
        $start_of_week = head($factory->factory_working_days->pluckIsoDayOfTheWeek());
        return $this->groupBy('cycle_pattern_sequence_number')
            ->reject(function ($grouped, $cycle_pattern_sequence_number) {
                return ! $cycle_pattern_sequence_number;
            })
            ->map(function ($grouped) use ($start_of_week) {
                return $grouped
                    ->first()
                    ->factory_cycle_pattern->factory_cycle_pattern_items
                    ->where('day_of_the_week', $start_of_week % Date::DAYS_PER_WEEK)
                    ->avg('number_of_panels');
            })
            ->all();
    }
}

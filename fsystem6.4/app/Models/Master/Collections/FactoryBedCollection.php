<?php

declare(strict_types=1);

namespace App\Models\Master\Collections;

use Illuminate\Database\Eloquent\Collection;
use App\Models\Master\FactoryBed;
use App\Models\Master\Collections\FactoryGrowingStageCollection;
use App\Models\Plan\BedState;
use App\Models\Plan\GrowthSimulation;
use App\Models\Plan\Collections\ArrangementDetailStateCollection;
use App\Models\Plan\Collections\ArrangementStateCollection;
use App\Models\Plan\Collections\PlannedArrangementDetailStatusWorkCollection;
use App\Models\Plan\Collections\PlannedArrangementStatusWorkCollection;
use App\ValueObjects\Enum\DisplayKubun;

class FactoryBedCollection extends Collection
{
    /**
     * 階でグルーピング
     *
     * @return \App\Models\Master\Collections\FactoryBedCollection
     */
    public function groupByFloor(): FactoryBedCollection
    {
        return $this->groupBy('floor');
    }

    /**
     * 段の情報を返却
     *
     * @param  array
     */
    public function rows(): array
    {
        return $this
            ->sortBy('row')
            ->groupBy('row')
            ->map(function ($grouped, $row) {
                return [
                    'row' => $row,
                    'floor' => $grouped->first()->floor
                ];
            })
            ->reverse()
            ->values()
            ->all();
    }

    /**
     * 段を逆順にして返却
     *
     * @return \App\Models\Master\Collections\FactoryBedCollection
     */
    public function reverseRow(): FactoryBedCollection
    {
        return $this->sortByDesc(function ($fb) {
            return implode('-', [$fb->floor, $fb->row]);
        });
    }

    /**
     * ベッド位置によってデータを抽出
     *
     * @param  int $row
     * @param  int $column
     * @return \App\Models\Master\FactoryBed
     */
    public function findByPosition(int $row, int $column): FactoryBed
    {
        return $this->where('row', $row)
            ->where('column', $column)
            ->first();
    }

    /**
     * 工場ベッドごとに作業用のパネルを割当
     *
     * @param  \App\ValueObjects\Enum\DisplayKubun $display_kubun
     * @param  \App\Models\Master\Collections\FactoryGrowingStageCollection $factory_growing_stages
     * @param  \App\Models\Plan\Collections\PlannedArrangementStatusWorkCollection $planned_arrangement_status_works
     * @param  array $circulations
     * @return array
     */
    public function allocatePanel(
        DisplayKubun $display_kubun,
        FactoryGrowingStageCollection $factory_growing_stages,
        PlannedArrangementStatusWorkCollection $planned_arrangement_status_works,
        array $circulations
    ): array {
        return $this
            ->groupBy('floor')
            ->map(function (
                $grouped,
                $floor
            ) use (
                $display_kubun,
                $factory_growing_stages,
                $planned_arrangement_status_works,
                $circulations
            ) {
                return [
                    'floor' => $floor,
                    'middle' => (int)($grouped->pluck('row')->unique()->median()),
                    'rows' => $grouped
                        ->groupBy('row')
                        ->map(function (
                            $grouped,
                            $row
                        ) use (
                            $display_kubun,
                            $factory_growing_stages,
                            $planned_arrangement_status_works,
                            $circulations
                        ) {
                            return [
                                'row' => $row,
                                'beds' => $grouped
                                    ->sortBy('column')
                                    ->map(function ($fb) use (
                                        $display_kubun,
                                        $factory_growing_stages,
                                        $planned_arrangement_status_works,
                                        $circulations
                                    ) {
                                        $stage = $pattern = null;
                                        $other_species = $is_fixed = false;
                                        $fixed_status = [];

                                        $status = $planned_arrangement_status_works
                                            ->getAllocatedBedStatus($fb, $display_kubun);
                                        if (! is_null($status)) {
                                            [$stage, $pattern, $is_fixed] = array_values($status);
                                            if ($stage === GrowthSimulation::DUMMY_SEQ_NUM_OF_OTHER_SPECIES) {
                                                $other_species = true;
                                            }
                                            if (! $display_kubun->isFixedStatus() && ! $other_species && $is_fixed) {
                                                $fixed_status = [
                                                    'stage' => $stage,
                                                    'label_color' => $factory_growing_stages
                                                        ->getLabelColor($stage ?: 0),
                                                    'number_of_holes' => $factory_growing_stages
                                                        ->getNumberOfHoles($stage ?: 0),
                                                    'pattern' => $pattern,
                                                    'number_of_panels' => $factory_growing_stages
                                                        ->getNumberOfPanels($stage ?: 0, $pattern ?: '')
                                                ];

                                                $status = $planned_arrangement_status_works->getReplacedBedStatus($fb);
                                                if (! is_null($status)) {
                                                    [$stage, $pattern] = array_values($status);
                                                }
                                            }
                                        }

                                        return [
                                            'row' => $fb->row,
                                            'column' => $fb->column,
                                            'floor' => $fb->floor,
                                            'circulation' => $circulations[$fb->column],
                                            'irradiation' => $fb->irradiation,
                                            'stage' => $stage,
                                            'label_color' => $factory_growing_stages->getLabelColor($stage ?: 0),
                                            'number_of_holes' => $factory_growing_stages->getNumberOfHoles($stage ?: 0),
                                            'pattern' => $pattern,
                                            'number_of_panels' => $factory_growing_stages
                                                ->getNumberOfPanels($stage ?: 0, $pattern ?: ''),
                                            'other_species' => $other_species,
                                            'is_fixed' => $is_fixed,
                                            'fixed_status' => $fixed_status
                                        ];
                                    })
                                    ->all()
                            ];
                        })
                        ->reverse()
                        ->values()
                        ->all()
                ];
            })
            ->reverse()
            ->values()
            ->all();
    }

    /**
     * 工場ベッドごとに作業用のパネル詳細を割当
     *
     * @param  \App\ValueObjects\Enum\DisplayKubun $display_kubun
     * @param  \App\Models\Master\Collections\FactoryGrowingStageCollection $factory_growing_stages
     * @param  \App\Models\Plan\Collections\PlannedArrangementDetailStatusWorkCollection $planned_arrangement_detail_status_works
     * @param  int $floor
     * @param  array $circulations
     * @return array
     */
    public function allocatePanelDetail(
        DisplayKubun $display_kubun,
        FactoryGrowingStageCollection $factory_growing_stages,
        PlannedArrangementDetailStatusWorkCollection $planned_arrangement_detail_status_works,
        int $floor,
        array $circulations
    ): array {
        return $this
            ->where('floor', $floor)
            ->groupBy('row')
            ->map(function (
                $grouped,
                $row
            ) use (
                $display_kubun,
                $factory_growing_stages,
                $planned_arrangement_detail_status_works,
                $circulations
            ) {
                return [
                    'row' => $row,
                    'beds' => $grouped->map(function ($fb) use (
                        $display_kubun,
                        $factory_growing_stages,
                        $planned_arrangement_detail_status_works,
                        $circulations
                    ) {
                        $statuses = $planned_arrangement_detail_status_works
                            ->getAllocatedPanelDetailStatuses($fb, $display_kubun);

                        $panels = [];
                        foreach (range(1, $fb->y_coordinate_panel) as $y_coordinate_panel) {
                            foreach (range(1, $fb->x_coordinate_panel) as $x_coordinate_panel) {
                                $panel = $fb->x_coordinate_panel * $y_coordinate_panel -
                                    ($fb->x_coordinate_panel - $x_coordinate_panel);

                                $status = $statuses[$panel];
                                $panels[] = [
                                    'column' => $fb->column,
                                    'circulation' => $circulations[$fb->column],
                                    'x_coordinate_panel' => $x_coordinate_panel,
                                    'y_coordinate_panel' => $y_coordinate_panel,
                                    'label_color' => $factory_growing_stages->getLabelColor($status ?: 0),
                                    'other_species' => $status === GrowthSimulation::DUMMY_SEQ_NUM_OF_OTHER_SPECIES
                                ];
                            }
                        }

                        return [
                            'row' => $fb->row,
                            'column' => $fb->column,
                            'panels' => array_reverse(array_chunk($panels, $fb->x_coordinate_panel))
                        ];
                    })
                ];
            })
            ->reverse()
            ->values()
            ->all();
    }

    /**
     * 工場ベッドごとに作業用のパネル詳細を割当
     * ※ 帳票出力用
     *
     * @param  \App\ValueObjects\Enum\DisplayKubun $display_kubun
     * @param  \App\Models\Master\Collections\FactoryGrowingStageCollection $factory_growing_stages
     * @param  \App\Models\Plan\Collections\PlannedArrangementDetailStatusWorkCollection $planned_arrangement_detail_status_works
     * @param  int $floor
     * @return array
     */
    public function allocatePanelDetailToExport(
        DisplayKubun $display_kubun,
        FactoryGrowingStageCollection $factory_growing_stages,
        PlannedArrangementDetailStatusWorkCollection $planned_arrangement_detail_status_works,
        int $floor,
        array $circulations
    ): array {
        $rows = $this->allocatePanelDetail(
            $display_kubun,
            $factory_growing_stages,
            $planned_arrangement_detail_status_works,
            $floor,
            $circulations
        );

        $re_constructed = [];
        $x_coordinattions = [];

        foreach ($rows as $row) {
            $reduced = $row['beds']
                ->reduce(function ($reduced, $bed) {
                    foreach ($bed['panels'] as $panels) {
                        $reduced = $reduced->merge($panels);
                    }

                    return $reduced;
                }, collect([]));

            $panels = $reduced
                ->groupBy('y_coordinate_panel')
                ->map(function ($grouped) {
                    return $grouped
                        ->sortBy(function ($panel) {
                            return ($panel['column'] * 100) + $panel['x_coordinate_panel'];
                        })
                        ->all();
                })
                ->all();

            $middle = (int)(count($panels) / 2);
            foreach ($panels as $idx => $panel) {
                $re_constructed[] = [
                    'row' => $row['row'],
                    'is_middle' => $idx === $middle,
                    'panels' => $panel
                ];
            }

            $x_coordinattions[] = $reduced->pluck('x_coordinate_panel')->max();
        }

        return [
            'rows' => $re_constructed,
            'middle' => (int)(count($re_constructed) / 2),
            'avg' => (int)(array_sum($x_coordinattions) / count($x_coordinattions))
        ];
    }

    /**
     * 工場ベッドごとに栽培状況を割当
     *
     * @param  \App\Models\Master\Collections\FactoryGrowingStageCollection $factory_growing_stages
     * @param  \App\Models\Plan\Collections\ArrangementStateCollection $arrangement_states
     * @param  array $circulations
     * @return array
     */
    public function allocateBedStates(
        FactoryGrowingStageCollection $factory_growing_stages,
        ArrangementStateCollection $arrangement_states,
        array $circulations
    ): array {
        return $this
            ->groupBy('floor')
            ->map(function ($grouped, $floor) use ($factory_growing_stages, $arrangement_states, $circulations) {
                return [
                    'floor' => $floor,
                    'middle' => (int)($grouped->pluck('row')->unique()->median()),
                    'rows' => $grouped
                        ->groupBy('row')
                        ->map(function (
                            $grouped,
                            $row
                        ) use (
                            $factory_growing_stages,
                            $arrangement_states,
                            $circulations
                        ) {
                            return [
                                'row' => $row,
                                'beds' => $grouped
                                    ->sortBy('column')
                                    ->map(function ($fb) use (
                                        $factory_growing_stages,
                                        $arrangement_states,
                                        $circulations
                                    ) {
                                        $stage = $pattern = null;
                                        $other_species = false;

                                        $arrangement_state = $arrangement_states->getBedState($fb);
                                        if (! is_null($arrangement_state)) {
                                            [$stage, $pattern] = array_values($arrangement_state);
                                            if ($stage === BedState::DUMMY_SEQ_NUM_OF_OTHER_SPECIES) {
                                                $other_species = true;
                                            }
                                        }

                                        return [
                                            'row' => $fb->row,
                                            'column' => $fb->column,
                                            'floor' => $fb->floor,
                                            'circulation' => $circulations[$fb->column],
                                            'irradiation' => $fb->irradiation,
                                            'stage' => $stage,
                                            'label_color' => $factory_growing_stages->getLabelColor($stage ?: 0),
                                            'number_of_holes' => $factory_growing_stages->getNumberOfHoles($stage ?: 0),
                                            'pattern' => $pattern,
                                            'number_of_panels' => $factory_growing_stages
                                                ->getNumberOfPanels($stage ?: 0, $pattern ?: ''),
                                            'other_species' => $other_species
                                        ];
                                    })
                                    ->all()
                            ];
                        })
                        ->reverse()
                        ->values()
                        ->all()
                ];
            })
            ->reverse()
            ->values()
            ->all();
    }

    /**
     * 工場ベッドごとに栽培詳細状況を割当
     *
     * @param  \App\Models\Master\Collections\FactoryGrowingStageCollection $factory_growing_stages
     * @param  \App\Models\Plan\Collections\ArrangementDetailStateCollection $arrangement_detail_states
     * @param  int $floor
     * @param  array $circulations
     * @return array
     */
    public function allocateDetailBedStates(
        FactoryGrowingStageCollection $factory_growing_stages,
        ArrangementDetailStateCollection $arrangement_detail_states,
        int $floor,
        array $circulations
    ): array {
        return $this
            ->where('floor', $floor)
            ->groupBy('row')
            ->map(function ($grouped, $row) use ($factory_growing_stages, $arrangement_detail_states, $circulations) {
                return [
                    'row' => $row,
                    'beds' => $grouped->map(function ($fb) use (
                        $factory_growing_stages,
                        $arrangement_detail_states,
                        $circulations
                    ) {
                        $states = $arrangement_detail_states->getPanelStates($fb);

                        $panels = [];
                        foreach (range(1, $fb->y_coordinate_panel) as $y_coordinate_panel) {
                            foreach (range(1, $fb->x_coordinate_panel) as $x_coordinate_panel) {
                                $panel = $fb->x_coordinate_panel * $y_coordinate_panel -
                                    ($fb->x_coordinate_panel - $x_coordinate_panel);

                                $state = $states[$panel] ?? null;
                                $panels[] = [
                                    'column' => $fb->column,
                                    'circulation' => $circulations[$fb->column],
                                    'x_coordinate_panel' => $x_coordinate_panel,
                                    'y_coordinate_panel' => $y_coordinate_panel,
                                    'label_color' => $factory_growing_stages->getLabelColor($state ?: 0),
                                    'other_species' => $state === BedState::DUMMY_SEQ_NUM_OF_OTHER_SPECIES
                                ];
                            }
                        }

                        return [
                            'row' => $fb->row,
                            'column' => $fb->column,
                            'panels' => array_reverse(array_chunk($panels, $fb->x_coordinate_panel))
                        ];
                    })
                ];
            })
            ->reverse()
            ->values()
            ->all();
    }

    /**
     * 工場ベッドごとに栽培詳細状況を割当
     * ※ 帳票出力用
     *
     * @param  \App\Models\Master\Collections\FactoryGrowingStageCollection $factory_growing_stages
     * @param  \App\Models\Plan\Collections\ArrangementDetailStateCollection $arrangement_detail_states
     * @param  int $floor
     * @param  array $circulations
     * @return array
     */
    public function allocateDetailBedStatesToExport(
        FactoryGrowingStageCollection $factory_growing_stages,
        ArrangementDetailStateCollection $arrangement_detail_states,
        int $floor,
        array $circulations
    ): array {
        $rows = $this->allocateDetailBedStates(
            $factory_growing_stages,
            $arrangement_detail_states,
            $floor,
            $circulations
        );

        $re_constructed = [];
        $x_coordinattions = [];

        foreach ($rows as $row) {
            $reduced = $row['beds']
                ->reduce(function ($reduced, $bed) {
                    foreach ($bed['panels'] as $panels) {
                        $reduced = $reduced->merge($panels);
                    }

                    return $reduced;
                }, collect([]));

            $panels = $reduced
                ->groupBy('y_coordinate_panel')
                ->map(function ($grouped) {
                    return $grouped
                        ->sortBy(function ($panel) {
                            return ($panel['column'] * 100) + $panel['x_coordinate_panel'];
                        })
                        ->all();
                })
                ->all();

            $middle = (int)(count($panels) / 2);
            foreach ($panels as $idx => $panel) {
                $re_constructed[] = [
                    'row' => $row['row'],
                    'is_middle' => $idx === $middle,
                    'panels' => $panel
                ];
            }

            $x_coordinattions[] = $reduced->pluck('x_coordinate_panel')->max();
        }

        return [
            'rows' => $re_constructed,
            'middle' => (int)(count($re_constructed) / 2),
            'avg' => (int)(array_sum($x_coordinattions) / count($x_coordinattions))
        ];
    }
}

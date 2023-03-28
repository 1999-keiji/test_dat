<?php

declare(strict_types=1);

namespace App\Repositories\Plan;

use Illuminate\Auth\AuthManager;
use Cake\Chronos\Chronos;
use App\Models\Plan\GrowthSimulation;
use App\Models\Plan\PlannedCultivationStatusWork;
use App\Models\Plan\Collections\PlannedCultivationStatusWorkCollection;
use App\ValueObjects\Date\SimulationDate;
use App\ValueObjects\Enum\DisplayKubun;
use App\ValueObjects\Enum\GrowingStage;

class PlannedCultivationStatusWorkRepository
{
    /**
     * @var \Illuminate\Auth\AuthManager
     */
    private $auth;

    /**
     * @var \App\Models\Plan\PlannedCultivationStatusWork
     */
    private $model;

    /**
     * @param  \Illuminate\Auth\AuthManager $auth
     * @param  \App\Models\Plan\PlannedCultivationStatusWork $model
     * @return void
     */
    public function __construct(AuthManager $auth, PlannedCultivationStatusWork $model)
    {
        $this->auth = $auth;
        $this->model = $model;
    }

    /**
     * 生産計画栽培状況を取得
     *
     * @param  \App\Models\Plan\GrowthSimulation $growth_simulation
     * @param  \App\ValueObjects\Date\SimulationDate $simulation_date
     * @return \App\Models\Plan\Collections\PlannedCultivationStatusWorkCollection
     */
    public function getPlannedCultivationStatusWorks(
        GrowthSimulation $growth_simulation,
        SimulationDate $simulation_date
    ) : PlannedCultivationStatusWorkCollection {
        return $this->model
            ->select([
                'growing_stages_sequence_number',
                'date',
                'day_of_the_week',
                'number_of_holes',
                'bed_number',
                'patterns_number',
                'floor_number',
                'moving_panel_count_pattern_1',
                'moving_panel_count_pattern_2',
                'moving_panel_count_pattern_3',
                'moving_panel_count_pattern_4',
                'moving_panel_count_pattern_5',
                'moving_panel_count_pattern_6',
                'moving_panel_count_pattern_7',
                'moving_panel_count_pattern_8',
                'moving_panel_count_pattern_9',
                'moving_panel_count_pattern_10',
                'moving_bed_count_floor_1_sum',
                'moving_bed_count_floor_1_pattern_1',
                'moving_bed_count_floor_1_pattern_2',
                'moving_bed_count_floor_1_pattern_3',
                'moving_bed_count_floor_1_pattern_4',
                'moving_bed_count_floor_1_pattern_5',
                'moving_bed_count_floor_1_pattern_6',
                'moving_bed_count_floor_1_pattern_7',
                'moving_bed_count_floor_1_pattern_8',
                'moving_bed_count_floor_1_pattern_9',
                'moving_bed_count_floor_1_pattern_10',
                'moving_bed_count_floor_2_sum',
                'moving_bed_count_floor_2_pattern_1',
                'moving_bed_count_floor_2_pattern_2',
                'moving_bed_count_floor_2_pattern_3',
                'moving_bed_count_floor_2_pattern_4',
                'moving_bed_count_floor_2_pattern_5',
                'moving_bed_count_floor_2_pattern_6',
                'moving_bed_count_floor_2_pattern_7',
                'moving_bed_count_floor_2_pattern_8',
                'moving_bed_count_floor_2_pattern_9',
                'moving_bed_count_floor_2_pattern_10',
                'moving_bed_count_floor_3_sum',
                'moving_bed_count_floor_3_pattern_1',
                'moving_bed_count_floor_3_pattern_2',
                'moving_bed_count_floor_3_pattern_3',
                'moving_bed_count_floor_3_pattern_4',
                'moving_bed_count_floor_3_pattern_5',
                'moving_bed_count_floor_3_pattern_6',
                'moving_bed_count_floor_3_pattern_7',
                'moving_bed_count_floor_3_pattern_8',
                'moving_bed_count_floor_3_pattern_9',
                'moving_bed_count_floor_3_pattern_10',
                'moving_bed_count_floor_4_sum',
                'moving_bed_count_floor_4_pattern_1',
                'moving_bed_count_floor_4_pattern_2',
                'moving_bed_count_floor_4_pattern_3',
                'moving_bed_count_floor_4_pattern_4',
                'moving_bed_count_floor_4_pattern_5',
                'moving_bed_count_floor_4_pattern_6',
                'moving_bed_count_floor_4_pattern_7',
                'moving_bed_count_floor_4_pattern_8',
                'moving_bed_count_floor_4_pattern_9',
                'moving_bed_count_floor_4_pattern_10',
                'moving_bed_count_floor_5_sum',
                'moving_bed_count_floor_5_pattern_1',
                'moving_bed_count_floor_5_pattern_2',
                'moving_bed_count_floor_5_pattern_3',
                'moving_bed_count_floor_5_pattern_4',
                'moving_bed_count_floor_5_pattern_5',
                'moving_bed_count_floor_5_pattern_6',
                'moving_bed_count_floor_5_pattern_7',
                'moving_bed_count_floor_5_pattern_8',
                'moving_bed_count_floor_5_pattern_9',
                'moving_bed_count_floor_5_pattern_10',
                'moving_bed_count_floor_6_sum',
                'moving_bed_count_floor_6_pattern_1',
                'moving_bed_count_floor_6_pattern_2',
                'moving_bed_count_floor_6_pattern_3',
                'moving_bed_count_floor_6_pattern_4',
                'moving_bed_count_floor_6_pattern_5',
                'moving_bed_count_floor_6_pattern_6',
                'moving_bed_count_floor_6_pattern_7',
                'moving_bed_count_floor_6_pattern_8',
                'moving_bed_count_floor_6_pattern_9',
                'moving_bed_count_floor_6_pattern_10',
                'moving_bed_count_floor_7_sum',
                'moving_bed_count_floor_7_pattern_1',
                'moving_bed_count_floor_7_pattern_2',
                'moving_bed_count_floor_7_pattern_3',
                'moving_bed_count_floor_7_pattern_4',
                'moving_bed_count_floor_7_pattern_5',
                'moving_bed_count_floor_7_pattern_6',
                'moving_bed_count_floor_7_pattern_7',
                'moving_bed_count_floor_7_pattern_8',
                'moving_bed_count_floor_7_pattern_9',
                'moving_bed_count_floor_7_pattern_10',
                'moving_bed_count_floor_8_sum',
                'moving_bed_count_floor_8_pattern_1',
                'moving_bed_count_floor_8_pattern_2',
                'moving_bed_count_floor_8_pattern_3',
                'moving_bed_count_floor_8_pattern_4',
                'moving_bed_count_floor_8_pattern_5',
                'moving_bed_count_floor_8_pattern_6',
                'moving_bed_count_floor_8_pattern_7',
                'moving_bed_count_floor_8_pattern_8',
                'moving_bed_count_floor_8_pattern_9',
                'moving_bed_count_floor_8_pattern_10',
                'moving_bed_count_floor_9_sum',
                'moving_bed_count_floor_9_pattern_1',
                'moving_bed_count_floor_9_pattern_2',
                'moving_bed_count_floor_9_pattern_3',
                'moving_bed_count_floor_9_pattern_4',
                'moving_bed_count_floor_9_pattern_5',
                'moving_bed_count_floor_9_pattern_6',
                'moving_bed_count_floor_9_pattern_7',
                'moving_bed_count_floor_9_pattern_8',
                'moving_bed_count_floor_9_pattern_9',
                'moving_bed_count_floor_9_pattern_10',
                'moving_bed_count_floor_10_sum',
                'moving_bed_count_floor_10_pattern_1',
                'moving_bed_count_floor_10_pattern_2',
                'moving_bed_count_floor_10_pattern_3',
                'moving_bed_count_floor_10_pattern_4',
                'moving_bed_count_floor_10_pattern_5',
                'moving_bed_count_floor_10_pattern_6',
                'moving_bed_count_floor_10_pattern_7',
                'moving_bed_count_floor_10_pattern_8',
                'moving_bed_count_floor_10_pattern_9',
                'moving_bed_count_floor_10_pattern_10'
            ])
            ->where('factory_code', $growth_simulation->factory_code)
            ->where('simulation_id', $growth_simulation->simulation_id)
            ->where('factory_species_code', $growth_simulation->factory_species_code)
            ->where('display_kubun', $growth_simulation->getDisplayKubun())
            ->whereBetween('date', [
                $simulation_date->startOfWeek()->format('Y-m-d'),
                $simulation_date->endOfWeek()->format('Y-m-d')
            ])
            ->get();
    }

    /**
     * フロアごとの移動ベッド数の合計を取得
     *
     * @param  \App\Models\Master\GrowthSimulation $growth_simulation
     * @return \App\Models\Plan\Collections\PlannedCultivationStatusWorkCollection
     */
    public function getSumOfMovingBedsPerFloor(
        GrowthSimulation $growth_simulation
    ): PlannedCultivationStatusWorkCollection {
        return $this->model
            ->select([
                'planned_cultivation_status_work.growing_stages_sequence_number',
                'factory_growing_stages.growing_stage',
                'planned_cultivation_status_work.date',
                'planned_cultivation_status_work.day_of_the_week',
                'planned_cultivation_status_work.bed_number',
                'planned_cultivation_status_work.moving_bed_count_floor_1_sum',
                'planned_cultivation_status_work.moving_bed_count_floor_2_sum',
                'planned_cultivation_status_work.moving_bed_count_floor_3_sum',
                'planned_cultivation_status_work.moving_bed_count_floor_4_sum',
                'planned_cultivation_status_work.moving_bed_count_floor_5_sum',
                'planned_cultivation_status_work.moving_bed_count_floor_6_sum',
                'planned_cultivation_status_work.moving_bed_count_floor_7_sum',
                'planned_cultivation_status_work.moving_bed_count_floor_8_sum',
                'planned_cultivation_status_work.moving_bed_count_floor_9_sum',
                'planned_cultivation_status_work.moving_bed_count_floor_10_sum'
            ])
            ->join('factory_growing_stages', function ($join) {
                $join->on('factory_growing_stages.factory_code', '=', 'planned_cultivation_status_work.factory_code')
                    ->on(
                        'factory_growing_stages.factory_species_code',
                        '=',
                        'planned_cultivation_status_work.factory_species_code'
                    )
                    ->on(
                        'factory_growing_stages.sequence_number',
                        '=',
                        'planned_cultivation_status_work.growing_stages_sequence_number'
                    );
            })
            ->where('planned_cultivation_status_work.factory_code', $growth_simulation->factory_code)
            ->where('planned_cultivation_status_work.simulation_id', $growth_simulation->simulation_id)
            ->where('planned_cultivation_status_work.factory_species_code', $growth_simulation->factory_species_code)
            ->where('planned_cultivation_status_work.display_kubun', $growth_simulation->getDisplayKubun())
            ->whereIn('factory_growing_stages.growing_stage', [GrowingStage::PORTING, GrowingStage::PLANTING])
            ->orderBy('planned_cultivation_status_work.date', 'ASC')
            ->orderBy('planned_cultivation_status_work.growing_stages_sequence_number', 'ASC')
            ->get();
    }

    /**
     * 生産計画栽培状況作業トランの登録
     *
     * @param  array $planned_cultivation_status_works_list
     * @return void
     */
    public function createPlannedCultivationStatusWorks(array $planned_cultivation_status_works_list): void
    {
        foreach ($planned_cultivation_status_works_list as $planned_cultivation_status_works) {
            $this->model->insert($planned_cultivation_status_works);
        }
    }

    /**
     * 生産計画栽培状況作業トランの削除
     *
     * @param  \App\Models\Plan\GrowthSimulation $growth_simulation
     * @return void
     */
    public function deletePlannedCultivationStatusWorks(GrowthSimulation $growth_simulation): void
    {
        $this->model
            ->where('factory_code', $growth_simulation->factory_code)
            ->where('simulation_id', $growth_simulation->simulation_id)
            ->where('factory_species_code', $growth_simulation->factory_species_code)
            ->delete();
    }

    /**
     * 生産計画栽培状況作業トランの更新
     *
     * @param  \App\Models\Plan\GrowthSimulation
     * @param  \App\ValueObjects\Date\SimulationDate $simulation_date
     * @param  array $params
     * @return void
    */
    public function updatePlannedCultivationStatusWork(
        GrowthSimulation $growth_simulation,
        SimulationDate $simulation_date,
        array $params
    ): void {
        $this->model->where('factory_code', $growth_simulation->factory_code)
            ->where('simulation_id', $growth_simulation->simulation_id)
            ->where('factory_species_code', $growth_simulation->factory_species_code)
            ->where('display_kubun', $growth_simulation->getDisplayKubun())
            ->where('growing_stages_sequence_number', $params['growing_stages_sequence_number'])
            ->where('date', '>=', $simulation_date->format('Y-m-d'))
            ->where('day_of_the_week', $params['day_of_the_week'])
            ->update(array_merge($params, ['updated_by' => $this->auth->user()->user_code]));
    }

    /**
     * 生産計画栽培状況確定
     *
     * @param  \App\Models\Plan\GrowthSimulation $growth_simulation
     * @return void
     */
    public function fixPlannedCultivationStatusWorks(GrowthSimulation $growth_simulation): void
    {
        $this->model->where('factory_code', $growth_simulation->factory_code)
            ->where('simulation_id', $growth_simulation->simulation_id)
            ->where('factory_species_code', $growth_simulation->factory_species_code)
            ->where('display_kubun', DisplayKubun::PROCESS)
            ->update([
                'display_kubun' => DisplayKubun::FIXED,
                'fixed_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => $this->auth->user()->user_code
            ]);
    }
}

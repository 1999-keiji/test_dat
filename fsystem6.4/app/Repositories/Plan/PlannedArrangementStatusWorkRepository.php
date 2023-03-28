<?php

declare(strict_types=1);

namespace App\Repositories\Plan;

use Illuminate\Auth\AuthManager;
use Illuminate\Database\Connection;
use Cake\Chronos\Chronos;
use App\Models\Plan\GrowthSimulation;
use App\Models\Plan\PlannedArrangementStatusWork;
use App\Models\Plan\Collections\PlannedArrangementStatusWorkCollection;
use App\ValueObjects\Date\SimulationDate;
use App\ValueObjects\Enum\DisplayKubun;

class PlannedArrangementStatusWorkRepository
{
    /**
     * @var \Illuminate\Auth\AuthManager
     */
    private $auth;

    /**
     * @var \Illuminate\Database\Connection
     */
    private $db;

    /**
     * @var \App\Models\Plan\PlannedArrangementStatusWork
     */
    private $model;

    /**
     * @param  \Illuminate\Auth\AuthManager $auth
     * @param  \Illuminate\Database\Connection $db
     * @param  \App\Models\Plan\PlannedArrangementStatusWork $model
     * @return void
     */
    public function __construct(AuthManager $auth, Connection $db, PlannedArrangementStatusWork $model)
    {
        $this->auth = $auth;
        $this->db = $db;
        $this->model = $model;
    }

    /**
     * 生産計画配置状況の登録
     *
     * @param  array $PlannedArrangementStatusWorks
     * @return void
     */
    public function createPlannedArrangementStatusWorks(array $planned_arrangement_status_works): void
    {
        foreach (array_chunk($planned_arrangement_status_works, 500) as $chunked) {
            $this->model->insert($chunked);
        }
    }

    /**
     * 生産計画配置状況の削除
     *
     * @param  \App\Models\Plan\GrowthSimulation $growth_simulation
     * @return void
     */
    public function deletePlannedArrangementStatusWorks(GrowthSimulation $growth_simulation): void
    {
        $this->model
            ->where('factory_code', $growth_simulation->factory_code)
            ->where('simulation_id', $growth_simulation->simulation_id)
            ->where('factory_species_code', $growth_simulation->factory_species_code)
            ->delete();
    }

    /**
     * 生産シミュレーションデータとシミュレーション日付を条件に生産計画配置状況作業データを取得
     *
     * @param  \App\Models\Plan\GrowthSimulation $growth_simulation
     * @param  \App\ValueObjects\Enum\DisplayKubun $display_kubun
     * @param  \App\ValueObjects\Date\SimulationDate $simulation_date
     * @return \App\Models\Plan\Collections\PlannedArrangementStatusWorkCollection
     */
    public function getPlannedArrangementStatusWorksBySimulationDate(
        GrowthSimulation $growth_simulation,
        DisplayKubun $display_kubun,
        SimulationDate $simulation_date
    ): PlannedArrangementStatusWorkCollection {
        return $this->model
            ->where('factory_code', $growth_simulation->factory_code)
            ->where('simulation_id', $growth_simulation->simulation_id)
            ->where('factory_species_code', $growth_simulation->factory_species_code)
            ->where(function ($query) use ($display_kubun) {
                if ($display_kubun->isFixedStatus()) {
                    $query->where('display_kubun', $display_kubun->value());
                }
            })
            ->where('date', $simulation_date->format('Y-m-d'))
            ->get();
    }

    /**
     * 確定済のものと結合して生産計画配置状況作業データを取得
     *
     * @param  \App\Models\Plan\GrowthSimulation $growth_simulation
     * @return \App\Models\Plan\Collections\PlannedArrangementStatusWorkCollection
     */
    public function getPlannedArrangementStatusWorksWithFixed(
        GrowthSimulation $growth_simulation
    ): PlannedArrangementStatusWorkCollection {
        $table = $this->model->getTable();
        $select = [
            "{$table}.factory_code",
            "{$table}.factory_species_code",
            "{$table}.date",
            "{$table}.bed_column",
            "{$table}.bed_row_number"
        ];

        foreach (range(1, PlannedArrangementStatusWork::NUMBER_OF_ROWS_COLUMN) as $row) {
            $select = array_merge($select, [
                "{$table}.growing_stages_count_{$row}",
                "{$table}.pattern_row_count_{$row}",
                "fixed.growing_stages_count_{$row} AS fixed_growing_stages_count_{$row}",
                "fixed.pattern_row_count_{$row} AS fixed_pattern_row_count_{$row}"
            ]);
        }

        return $this->model->select($select)
            ->leftJoin("{$table} AS fixed", function ($join) use ($table) {
                $join->on('fixed.factory_code', '=', "{$table}.factory_code")
                    ->on('fixed.simulation_id', '=', "{$table}.simulation_id")
                    ->on('fixed.factory_species_code', '=', "{$table}.factory_species_code")
                    ->on('fixed.date', '=', "{$table}.date")
                    ->on('fixed.bed_column', '=', "{$table}.bed_column")
                    ->where('fixed.display_kubun', DisplayKubun::FIXED);
            })
            ->where("{$table}.factory_code", $growth_simulation->factory_code)
            ->where("{$table}.simulation_id", $growth_simulation->simulation_id)
            ->where("{$table}.factory_species_code", $growth_simulation->factory_species_code)
            ->where("{$table}.display_kubun", DisplayKubun::PROCESS)
            ->orderBy("{$table}.date", 'ASC')
            ->orderBy("{$table}.bed_column", 'ASC')
            ->get();
    }

    /**
     * 生産計画配置状況確定処理実行
     *
     * @param  \App\Models\Plan\GrowthSimulation $growth_simulation
     * @return void
     */
    public function fixPlannedArrangementStatusWorks(GrowthSimulation $growth_simulation): void
    {
        $sql = "CALL fix_planned_arrangement_status_work('%s', %d, '%s', '%s')";
        $this->db->statement(sprintf(
            $sql,
            $growth_simulation->factory_code,
            $growth_simulation->simulation_id,
            $growth_simulation->factory_species_code,
            $this->auth->user()->user_code
        ));

        $this->model->where('factory_code', $growth_simulation->factory_code)
            ->where('simulation_id', $growth_simulation->simulation_id)
            ->where('factory_species_code', $growth_simulation->factory_species_code)
            ->where('display_kubun', DisplayKubun::PROCESS)
            ->delete();
    }

    /**
     * 栽培パネル配置結果の反映
     *
     * @param  \App\Models\Plan\PlannedArrangementStatusWork $pasw
     * @return void
     */
    public function savePlannedArrangementStatusWork(PlannedArrangementStatusWork $pasw): void
    {
        $query = $this->db->table($this->model->getTable())
            ->where('factory_code', $pasw->factory_code)
            ->where('simulation_id', $pasw->simulation_id)
            ->where('factory_species_code', $pasw->factory_species_code)
            ->where('display_kubun', $pasw->display_kubun)
            ->where('date', '>=', $pasw->date)
            ->where('bed_column', $pasw->bed_column);

        $params = [
            'updated_by' => $this->auth->id(),
            'updated_at' => Chronos::now()->format('Y-m-d H:i:s'),
        ];

        foreach (range(1, $pasw->bed_row_number) as $row) {
            $params["growing_stages_count_{$row}"] = $pasw["growing_stages_count_{$row}"];
            $params["pattern_row_count_{$row}"] = $pasw["pattern_row_count_{$row}"];
        }

        $query->update($params);
    }
}

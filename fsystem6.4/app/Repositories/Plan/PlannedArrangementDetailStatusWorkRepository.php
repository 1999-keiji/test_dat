<?php

declare(strict_types=1);

namespace App\Repositories\Plan;

use Illuminate\Auth\AuthManager;
use Illuminate\Database\Connection;
use Cake\Chronos\Chronos;
use App\Models\Plan\GrowthSimulation;
use App\Models\Plan\PlannedArrangementDetailStatusWork;
use App\Models\Plan\Collections\PlannedArrangementDetailStatusWorkCollection;
use App\ValueObjects\Date\SimulationDate;
use App\ValueObjects\Enum\DisplayKubun;
use App\Extension\Logger\ApplicationLogger;

class PlannedArrangementDetailStatusWorkRepository
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
     * @var \App\Models\Plan\PlannedArrangementDetailStatusWork
     */
    private $model;

    /**
     * @var \App\Extension\Logger\ApplicationLogger
     */
    private $application_logger;

    /**
     * @param  \Illuminate\Auth\AuthManager $auth
     * @param  \Illuminate\Database\Connection $db
     * @param  \App\Extension\Logger\ApplicationLogger $application_logger
     * @param  \App\Models\Plan\PlannedArrangementDetailStatusWork $model
     * @return void
     */
    public function __construct(
        AuthManager $auth,
        Connection $db,
        ApplicationLogger $application_logger,
        PlannedArrangementDetailStatusWork $model
    ) {
        $this->auth = $auth;
        $this->db = $db;
        $this->application_logger = $application_logger;
        $this->model = $model;
    }

    /**
     * 生産計画配置状況の登録
     *
     * @param  array $planned_arrangement_detail_status_works
     * @return void
     */
    public function createPlannedArrangementDetailStatusWorks(array $planned_arrangement_detail_status_works): void
    {
        foreach (array_chunk($planned_arrangement_detail_status_works, 500) as $chunked) {
            $this->model->insert($chunked);
        }
    }

    /**
     * 生産計画配置状況の削除
     *
     * @param  \App\Models\Plan\GrowthSimulation $growth_simulation
     * @return void
     */
    public function deletePlannedArrangementDetailStatusWorks(GrowthSimulation $growth_simulation): void
    {
        $this->model
            ->where('factory_code', $growth_simulation->factory_code)
            ->where('simulation_id', $growth_simulation->simulation_id)
            ->where('factory_species_code', $growth_simulation->factory_species_code)
            ->delete();
    }

    /**
     * 生産計画配置状況詳細確定処理実行
     *
     * @param  \App\Models\Plan\GrowthSimulation $growth_simulation
     * @return void
     */
    public function fixPlannedArrangementDetailStatusWorks(GrowthSimulation $growth_simulation): void
    {
        $sql = "CALL fix_planned_arrangement_detail_status_work('%s', %d,'%s','%s')";
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
     * 生産シミュレーションデータとシミュレーション日付を条件に生産計画配置詳細状況作業データを取得
     *
     * @param  \App\Models\Plan\GrowthSimulation $growth_simulation
     * @param  \App\ValueObjects\Enum\DisplayKubun $display_kubun
     * @param  \App\ValueObjects\Date\SimulationDate $simulation_date
     * @return \App\Models\Plan\Collections\PlannedArrangementDetailStatusWorkCollection
     */
    public function getPlannedArrangementDetailStatusWorksBySimulationDate(
        GrowthSimulation $growth_simulation,
        DisplayKubun $display_kubun,
        SimulationDate $simulation_date
    ): PlannedArrangementDetailStatusWorkCollection {
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
     * 工場コードとシミュレーション日付を条件に、生産シミュレーションに紐づく生産計画配置詳細状況作業データを、
     * ベッドのパネル配置とともに取得
     *
     * @param  \App\Models\Plan\GrowthSimulation $growth_simulation
     * @param  \App\ValueObjects\Date\SimulationDate $simulation_date
     * @return \App\Models\Plan\Collections\PlannedArrangementDetailStatusWorkCollection
     */
    public function getPlannedArrangementDetailStatusWorksWithBedCordinationBySimulationDate(
        GrowthSimulation $growth_simulation,
        SimulationDate $simulation_date
    ): PlannedArrangementDetailStatusWorkCollection {
        $query = $this->model->newQuery();
        $table = $this->model->getTable();
        $select = [
            "{$table}.factory_code",
            "{$table}.simulation_id",
            "{$table}.factory_species_code",
            "{$table}.display_kubun",
            'prev.display_kubun AS prev_display_kubun',
            "{$table}.date",
            "{$table}.bed_row",
            "{$table}.bed_column",
            'factory_beds.x_coordinate_panel',
            'factory_beds.y_coordinate_panel'
        ];

        foreach (range(1, PlannedArrangementDetailStatusWork::NUMBER_OF_PANEL_STATE_COLUMN) as $panel) {
            $select = array_merge($select, [
                "{$table}.panel_status_{$panel}",
                "prev.panel_status_{$panel} AS prev_panel_status_{$panel}"
            ]);
        }

        return $this->model
            ->select($select)
            ->join('factory_beds', function ($join) use ($table) {
                $join->on('factory_beds.factory_code', '=', "{$table}.factory_code")
                    ->on('factory_beds.row', '=', "{$table}.bed_row")
                    ->on('factory_beds.column', '=', "{$table}.bed_column");
            })
            ->leftJoin("{$table} AS prev", function ($join) use ($simulation_date, $table) {
                $join->on('prev.factory_code', '=', "{$table}.factory_code")
                    ->on('prev.simulation_id', '=', "{$table}.simulation_id")
                    ->on('prev.factory_species_code', '=', "{$table}.factory_species_code")
                    ->on('prev.bed_row', '=', "{$table}.bed_row")
                    ->on('prev.bed_column', '=', "{$table}.bed_column")
                    ->where('prev.date', $simulation_date->subDay()->format('Y-m-d'));
            })
            ->where("{$table}.factory_code", $growth_simulation->factory_code)
            ->where("{$table}.simulation_id", $growth_simulation->simulation_id)
            ->where("{$table}.factory_species_code", $growth_simulation->factory_species_code)
            ->where("{$table}.display_kubun", $growth_simulation->getDisplayKubun()->value())
            ->where("{$table}.date", $simulation_date->format('Y-m-d'))
            ->get();
    }

    /**
     * 栽培パネル配置結果の反映
     *
     * @param  \App\Models\Plan\PlannedArrangementDetailStatusWork $padsw
     * @return void
     */
    public function savePlannedArrangementDetailStatusWork(PlannedArrangementDetailStatusWork $padsw): void
    {
        $query = $this->db->table($this->model->getTable())
            ->where('factory_code', $padsw->factory_code)
            ->where('simulation_id', $padsw->simulation_id)
            ->where('factory_species_code', $padsw->factory_species_code)
            ->where('display_kubun', $padsw->display_kubun)
            ->where('date', '>=', $padsw->date)
            ->where('bed_row', $padsw->bed_row)
            ->where('bed_column', $padsw->bed_column);

        $params = [
            'updated_by' => $this->auth->id(),
            'updated_at' => Chronos::now()->format('Y-m-d H:i:s'),
        ];

        foreach (range(1, $padsw->x_coordinate_panel * $padsw->y_coordinate_panel) as $panel) {
            $params["panel_status_{$panel}"] = $padsw["panel_status_{$panel}"];
        }

        $query->update($params);
    }
}

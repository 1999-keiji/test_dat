<?php

declare(strict_types=1);

namespace App\Repositories\Plan;

use Illuminate\Database\Connection;
use Illuminate\Support\Collection;
use Cake\Chronos\Chronos;
use App\Models\Master\Factory;
use App\Models\Master\FactorySpecies;
use App\Models\Master\Species;
use App\Models\Plan\PanelState;
use App\Models\Plan\PlannedArrangementStatusWork;
use App\Models\Plan\Collections\PanelStateCollection;
use App\ValueObjects\Date\HarvestingDate;
use App\ValueObjects\Date\WorkingDate;
use App\ValueObjects\Enum\GrowingStage;
use App\ValueObjects\Enum\PanelStatus;

class PanelStateRepository
{
    /**
     * @var \Illuminate\Database\Connection
     */
    private $db;

    /**
     * @var \App\Models\Plan\PanelState
     */
    private $model;

    /**
     * @param  \Illuminate\Database\Connection $db
     * @param  \App\Models\Plan\PanelState $model
     * @return void
     */
    public function __construct(Connection $db, PanelState $model)
    {
        $this->db = $db;
        $this->model = $model;
    }

    /**
     * 指定された品種、期間に応じて収穫株数を取得
     *
     * @param  array $params
     * @param  array $harvesting_date_term
     * @return \App\Models\Plan\Collections\PanelStateCollection
     */
    public function getHarvestingStockQuantitiesBySpeciesAndHarvestingDate(
        array $params,
        array $harvesting_date_term
    ): PanelStateCollection {
        $query = $this->model
            ->select([
                'panel_state.factory_code',
                'factories.factory_abbreviation',
                $this->db->raw(
                    'SUM(CASE WHEN panel_state.using_hole_count IS NULL '.
                    'THEN panel_state.number_of_holes '.
                    'ELSE panel_state.using_hole_count END) AS harvesting_quantity'
                ),
                $this->db->raw(
                    'SUM(CASE WHEN panel_state.using_hole_count IS NULL '.
                    'THEN (panel_state.number_of_holes * factory_species.weight) '.
                    'ELSE (panel_state.using_hole_count * factory_species.weight) END) AS harvesting_weight'
                )
            ])
            ->from($this->db->raw('panel_state USE INDEX (panel_state_factory_code_next_growth_stage_date_index)'))
            ->join('factory_species', function ($join) {
                $join->on('factory_species.factory_code', '=', 'panel_state.factory_code')
                    ->on('factory_species.factory_species_code', '=', 'panel_state.factory_species_code');
            })
            ->join('factories', 'factories.factory_code', '=', 'factory_species.factory_code')
            ->where('factory_species.species_code', $params['species_code'])
            ->where(function ($query) use ($params) {
                if ($factory_code = $params['factory_code'] ?? null) {
                    $query->where('panel_state.factory_code', $factory_code);
                }
            })
            ->whereRaw('panel_state.date = DATE_ADD(panel_state.next_growth_stage_date, INTERVAL -1 day)')
            ->where('panel_state.current_growth_stage', GrowingStage::PLANTING)
            ->groupBy('panel_state.factory_code')
            ->orderBy('panel_state.factory_code', 'ASC');

        if ($params['display_term'] === 'date') {
            $query->addSelect('panel_state.next_growth_stage_date AS harvesting_date')
                ->where(
                    'panel_state.next_growth_stage_date',
                    '>=',
                    $harvesting_date_term['from']->subWeek(4)->startOfWeek()->format('Y-m-d')
                )
                ->where('panel_state.next_growth_stage_date', '<=', $harvesting_date_term['to']->format('Y-m-d'))
                ->groupBy('panel_state.next_growth_stage_date')
                ->orderBy('panel_state.next_growth_stage_date', 'ASC');
        }
        if ($params['display_term'] === 'month') {
            $query
                ->addSelect(
                    $this->db->raw("DATE_FORMAT(panel_state.next_growth_stage_date, '%Y%m') AS harvesting_month")
                )
                ->whereBetween('panel_state.next_growth_stage_date', [
                    $harvesting_date_term['from']->format('Y-m-d'),
                    $harvesting_date_term['to']->format('Y-m-d')
                ])
                ->groupBy(
                    $this->db->raw("DATE_FORMAT(panel_state.next_growth_stage_date, '%Y%m')")
                )
                ->orderBy('harvesting_month', 'ASC');
        }

        return $query->get();
    }

    /**
     * 工場、品種を指定して各収穫日の収穫株数を取得
     *
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Master\Species $species
     * @param  \App\ValueObjects\Date\HarvestingDate $harvesting_date
     * @return \App\Models\Plan\Collections\PanelStateCollection
     */
    public function getHarvestingQuantitiesByFactoryAndSpecies(
        Factory $factory,
        Species $species,
        HarvestingDate $harvesting_date
    ): PanelStateCollection {
        return $this->model
            ->select([
                'panel_state.next_growth_stage_date AS harvesting_date',
                $this->db->raw(
                    'SUM(CASE WHEN panel_state.using_hole_count IS NULL '.
                    'THEN panel_state.number_of_holes '.
                    'ELSE panel_state.using_hole_count END) AS harvesting_quantity'
                ),
                $this->db->raw(
                    'SUM(CASE WHEN panel_state.using_hole_count IS NULL '.
                    'THEN (panel_state.number_of_holes * factory_species.weight) '.
                    'ELSE (panel_state.using_hole_count * factory_species.weight) END) AS harvesting_weight'
                )
            ])
            ->from($this->db->raw('panel_state USE INDEX (panel_state_factory_code_next_growth_stage_date_index)'))
            ->join('factory_species', function ($join) {
                $join->on('panel_state.factory_code', '=', 'factory_species.factory_code')
                    ->on('panel_state.factory_species_code', '=', 'factory_species.factory_species_code');
            })
            ->where('panel_state.factory_code', $factory->factory_code)
            ->where('factory_species.species_code', $species->species_code)
            ->whereBetween('panel_state.next_growth_stage_date', [
                $harvesting_date->format('Y-m-d'),
                $harvesting_date->getEndOfDateOfGrowthSaleManagement()->format('Y-m-d')
            ])
            ->where('panel_state.current_growth_stage', GrowingStage::PLANTING)
            ->whereRaw('panel_state.`date` = DATE_ADD(panel_state.next_growth_stage_date, INTERVAL -1 day)')
            ->groupBy('panel_state.factory_code', 'factory_species.species_code', 'panel_state.next_growth_stage_date')
            ->orderBy('next_growth_stage_date', 'ASC')
            ->get();
    }

    /**
     * シミュレーションに必要な既存のパネル情報を取得
     *
     * @param  string $factory_code
     * @param  array $date_term
     * @return \App\Models\Plan\Collections\PanelStateCollection
     */
    public function getSimulatingPanelStates(string $factory_code, array $date_term): PanelStateCollection
    {
        return $this->model
            ->select([
                'panel_state.panel_id',
                'panel_state.date',
                'panel_state.number_of_holes',
                'factory_beds.floor',
                'panel_state.bed_row',
                'panel_state.bed_column',
                'panel_state.x_coordinate_panel',
                'panel_state.y_coordinate_panel',
                'panel_state.x_current_bed_position',
                'panel_state.y_current_bed_position',
                'panel_state.factory_species_code',
                'panel_state.growing_stage_sequence_number',
                'panel_state.cycle_pattern',
                'panel_state.stage_start_date',
                'panel_state.current_growth_stage',
                'panel_state.next_growth_stage',
                'panel_state.next_growth_stage_date',
                'panel_state.using_hole_count',
                'panel_state.panel_status'
            ])
            ->join('factory_beds', function ($join) {
                $join->on('factory_beds.factory_code', '=', 'panel_state.factory_code')
                    ->on('factory_beds.row', '=', 'panel_state.bed_row')
                    ->on('factory_beds.column', '=', 'panel_state.bed_column');
            })
            ->where('panel_state.factory_code', $factory_code)
            ->whereBetween('panel_state.date', [
                head($date_term)->format('Y-m-d'),
                last($date_term)->format('Y-m-d')
            ])
            ->whereNotNull('panel_state.factory_species_code')
            ->orderBy('panel_state.date', 'asc')
            ->orderBy('panel_state.bed_row', 'asc')
            ->orderBy('panel_state.bed_column', 'asc')
            ->orderBy('panel_state.x_current_bed_position', 'asc')
            ->orderBy('panel_state.y_current_bed_position', 'asc')
            ->get();
    }

    /**
     * 指定された日付に次ステージに移行するパネルの枚数を取得
     *
     * @param  \App\Models\Master\FactorySpecies $factory_species
     * @param  string $next_growth_stage_date
     * @return \App\Models\Plan\Collections\PanelStateCollection
     */
    public function getPanelCountsStageHasChanged(
        FactorySpecies $factory_species,
        string $next_growth_stage_date
    ): PanelStateCollection {
        return $this->model
            ->select([
                'next_growing_stage_sequence_number',
                $this->db->raw('COUNT(`next_growing_stage_sequence_number`) AS `panel_count_previous`')
            ])
            ->where('factory_code', $factory_species->factory_code)
            ->where('factory_species_code', $factory_species->factory_species_code)
            ->where('next_growth_stage_date', $next_growth_stage_date)
            ->whereRaw('panel_state.date = DATE_ADD(panel_state.next_growth_stage_date, INTERVAL -1 DAY)')
            ->groupBy('next_growing_stage_sequence_number')
            ->get();
    }

    /**
     * 指定された日付からステージが開始したパネルの枚数を取得
     *
     * @param  \App\Models\Master\FactorySpecies $factory_species
     * @param  string $stage_start_date
     * @return \App\Models\Plan\Collections\PanelStateCollection
     */
    public function getPanelCountsTransferedNextStage(
        FactorySpecies $factory_species,
        string $stage_start_date
    ): PanelStateCollection {
        return $this->model
            ->select([
                'growing_stage_sequence_number',
                $this->db->raw('COUNT(`growing_stage_sequence_number`) AS `panel_count_next`')
            ])
            ->where('factory_code', $factory_species->factory_code)
            ->where('factory_species_code', $factory_species->factory_species_code)
            ->where('stage_start_date', $stage_start_date)
            ->whereRaw('panel_state.date = DATE_ADD(panel_state.next_growth_stage_date, INTERVAL -1 DAY)')
            ->groupBy('growing_stage_sequence_number')
            ->get();
    }

    /**
     * 指定された日付に収穫段階に達するパネルの枚数と株数を取得
     *
     * @param  \App\Models\Master\FactorySpecies $factory_species
     * @param  string $next_growth_stage_date
     * @return \App\Models\Plan\PanelState
     */
    public function getPanelCountsWillHarvesting(
        FactorySpecies $factory_species,
        string $stage_start_date
    ): ?PanelState {
        return $this->model
            ->select([
                $this->db->raw('COUNT(*) as `harvesting_panel_count`'),
                $this->db->raw('SUM(`using_hole_count`) AS `harvesting_hole_count`')
            ])
            ->where('factory_code', $factory_species->factory_code)
            ->where('factory_species_code', $factory_species->factory_species_code)
            ->where('next_growth_stage_date', $stage_start_date)
            ->where('current_growth_stage', GrowingStage::PLANTING)
            ->whereRaw('panel_state.date = DATE_ADD(panel_state.next_growth_stage_date, INTERVAL -1 DAY)')
            ->first();
    }

    /**
     * 指定された日付に播種ステージから移行したパネル数を取得
     *
     * @param  \App\Models\Master\FactorySpecies $factory_species
     * @param  string $date
     * @return \App\Models\Plan\PanelState
     */
    public function getPanelCountAfterSeeding(FactorySpecies $factory_species, string $working_date): ?PanelState
    {
        return $this->model
            ->select([
                'factory_growing_stages.number_of_holes',
                $this->db->raw('COUNT(panel_id) as panel_count'),
            ])
            ->join('factory_growing_stages', function ($join) {
                $join->on('factory_growing_stages.factory_code', '=', 'panel_state.factory_code')
                    ->on('factory_growing_stages.factory_species_code', '=', 'panel_state.factory_species_code')
                    ->on('factory_growing_stages.sequence_number', '=', 'panel_state.growing_stage_sequence_number');
            })
            ->where('panel_state.factory_code', $factory_species->factory_code)
            ->where('panel_state.factory_species_code', $factory_species->factory_species_code)
            ->where('panel_state.stage_start_date', $working_date)
            ->where('panel_state.growing_stage_sequence_number', 2)
            ->whereRaw('panel_state.date = DATE_ADD(panel_state.next_growth_stage_date, INTERVAL -1 DAY)')
            ->groupBy('factory_growing_stages.number_of_holes')
            ->first();
    }

    /**
     * 工場別施設利用状況取得
     *
     * @param  \App\Models\Master\Factory $factory
     * @param  array $working_date_term
     * @return \Illuminate\Support\Collection
     */
    public function getUsingBedNumbers(Factory $factory, array $working_date_term): Collection
    {
        $sub_query_a = $this->model
            ->select(['date', 'bed_row', 'bed_column', 'factory_species_code'])
            ->where('panel_state.factory_code', $factory->factory_code)
            ->whereBetween('panel_state.date', [
                $working_date_term['from']->format('Y-m-d'),
                $working_date_term['to']->format('Y-m-d')
            ])
            ->where('panel_state.panel_status', PanelStatus::OPERATION)
            ->groupBy(['date', 'bed_row', 'bed_column', 'factory_species_code']);

        $sub_query_b = $this->db->table($this->db->raw("({$sub_query_a->toSql()}) a"))
            ->select(['date', 'bed_row', $this->db->raw('COUNT(bed_column) bed_column'), 'factory_species_code'])
            ->mergeBindings($sub_query_a->getQuery())
            ->groupBy(['date', 'bed_row', 'factory_species_code']);

        return $this->db->table($this->db->raw("({$sub_query_b->toSql()}) b"))
            ->select(['date', $this->db->raw('SUM(bed_column) number_of_beds'), 'factory_species_code'])
            ->mergeBindings($sub_query_a->getQuery())
            ->groupBy(['date', 'factory_species_code'])
            ->get();
    }

    /**
     * 生育ステージ別ベッド利用状況取得
     *
     * @param  \App\Models\Master\Factory $factory
     * @param  array $working_date_term
     * @return \Illuminate\Support\Collection
     */
    public function getBedUsingStatusList(Factory $factory, array $working_date_term): Collection
    {
        $sub_query_a = $this->model
            ->select([
                'date',
                'bed_row',
                'bed_column',
                'factory_species_code',
                'current_growth_stage',
                'number_of_holes',
                'growing_stage_sequence_number'
            ])
            ->where('panel_state.factory_code', $factory->factory_code)
            ->whereBetween('panel_state.date', [
                $working_date_term['from']->format('Y-m-d'),
                $working_date_term['to']->format('Y-m-d')
            ])
            ->where('panel_state.panel_status', PanelStatus::OPERATION)
            ->groupBy([
                'date',
                'bed_row',
                'bed_column',
                'factory_species_code',
                'current_growth_stage',
                'number_of_holes',
                'growing_stage_sequence_number'
            ]);

        $sub_query_b = $this->db->table($this->db->raw("({$sub_query_a->toSql()}) a"))
            ->select([
                'date',
                'bed_row',
                $this->db->raw('COUNT(bed_column) bed_column'),
                'factory_species_code',
                'current_growth_stage',
                'number_of_holes',
                'growing_stage_sequence_number'
            ])
            ->mergeBindings($sub_query_a->getQuery())
            ->groupBy([
                'date',
                'bed_row',
                'factory_species_code',
                'current_growth_stage',
                'number_of_holes',
                'growing_stage_sequence_number'
            ]);

        return $this->db->table($this->db->raw("({$sub_query_b->toSql()}) b"))
            ->select([
                'date',
                $this->db->raw('SUM(bed_column) number_of_beds'),
                'factory_species_code',
                'current_growth_stage',
                'number_of_holes',
                'growing_stage_sequence_number'
            ])
            ->mergeBindings($sub_query_a->getQuery())
            ->groupBy([
                'date',
                'factory_species_code',
                'current_growth_stage',
                'number_of_holes',
                'growing_stage_sequence_number'
            ])
            ->get();
    }

    /**
     * 施設利用状況一覧 生育ステージ別パネル状況取得
     *
     * @param  \App\Models\Master\Factory $factory
     * @param  array $working_date_term
     * @return \App\Models\Plan\Collections\PanelStateCollection
     */
    public function getGrowingPanelStates(Factory $factory, array $working_date_term): PanelStateCollection
    {
        return $this->model
            ->select([
                'date',
                'factory_species_code',
                'current_growth_stage',
                'number_of_holes',
                $this->db->raw('SUM(using_hole_count) using_hole_count'),
                $this->db->raw('COUNT(number_of_holes) number_of_holes_count'),
                'growing_stage_sequence_number'
            ])
            ->where('panel_state.factory_code', $factory->factory_code)
            ->whereBetween('panel_state.date', [
                $working_date_term['from']->format('Y-m-d'),
                $working_date_term['to']->format('Y-m-d')
            ])
            ->where('panel_state.panel_status', PanelStatus::OPERATION)
            ->groupBy([
                'date',
                'factory_species_code',
                'current_growth_stage',
                'number_of_holes',
                'growing_stage_sequence_number'
            ])
            ->get();
    }

    /**
     * 指定された工場、作業日に栽培中の工場品種の情報を取得
     *
     * @param  array $params
     * @return \App\Models\Plan\Collections\PanelStateCollection
     */
    public function getActivityResults($params): PanelStateCollection
    {
        return $this->model
            ->select([
                'factory_species.factory_code',
                'species.species_name',
                'factory_species.factory_species_code',
                'factory_species.factory_species_name'
            ])
            ->join('factory_species', function ($join) {
                $join->on('panel_state.factory_code', '=', 'factory_species.factory_code')
                    ->on('panel_state.factory_species_code', '=', 'factory_species.factory_species_code');
            })
            ->join('species', 'factory_species.species_code', '=', 'species.species_code')
            ->where('panel_state.factory_code', $params['factory_code'])
            ->where(function ($query) use ($params) {
                if ($factory_species_code = $params['factory_species_code'] ?? null) {
                    $query->where('panel_state.factory_species_code', $factory_species_code);
                } else {
                    $query->whereNotNull('panel_state.factory_species_code');
                }
            })
            ->where('panel_state.date', $params['working_date'])
            ->groupBy('panel_state.factory_code', 'panel_state.date', 'panel_state.factory_species_code')
            ->orderBy('panel_state.factory_species_code', 'ASC')
            ->get();
    }

    /**
     * 指定された工場品種、作業日のパネル状況を取得
     *
     * @param  \App\Models\Master\FactorySpecies $factory_species
     * @param  array $params
     * @return \App\Models\Plan\Collections\PanelStateCollection
     */
    public function getActivityPanels(FactorySpecies $factory_species, array $params): PanelStateCollection
    {
        return $this->model->select([
                'panel_state.panel_id',
                'panel_state.x_current_bed_position',
                'panel_state.y_current_bed_position',
                'panel_state.number_of_holes',
                'factory_growing_stages.growing_stage_name',
                'panel_state.using_hole_count',
                'panel_state.panel_status'
            ])
            ->join('factory_growing_stages', function ($join) {
                $join->on('panel_state.factory_code', '=', 'factory_growing_stages.factory_code')
                    ->on('panel_state.factory_species_code', '=', 'factory_growing_stages.factory_species_code')
                    ->on('panel_state.growing_stage_sequence_number', '=', 'factory_growing_stages.sequence_number');
            })
            ->where('panel_state.factory_code', $factory_species->factory_code)
            ->where('panel_state.date', $params['working_date'])
            ->where('panel_state.factory_species_code', $factory_species->factory_species_code)
            ->where('panel_state.bed_row', $params['row'])
            ->where('panel_state.bed_column', $params['column'])
            ->where('panel_state.stage_start_date', $params['working_date'])
            ->orderBy('y_current_bed_position', 'ASC')
            ->orderBy('x_current_bed_position', 'ASC')
            ->get();
    }

    /**
     * 指定された工場、作業週のデータを取得
     *
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\ValueObjects\Date\WorkingDate $working_date
     * @return \App\Models\Plan\Collections\PanelStateCollection
     */
    public function getPanelStatesByFactoryAndWorkingDate(
        Factory $factory,
        WorkingDate $working_date
    ): PanelStateCollection {
        return $this->model
            ->select([
                'panel_state.factory_code',
                'panel_state.date',
                'panel_state.bed_column',
                'panel_state.bed_row',
                'factory_beds.floor',
                'panel_state.x_coordinate_panel',
                'panel_state.y_coordinate_panel',
                'panel_state.x_current_bed_position',
                'panel_state.y_current_bed_position',
                'panel_state.factory_species_code',
                'panel_state.growing_stage_sequence_number',
                'panel_state.cycle_pattern',
                'panel_state.stage_start_date'
            ])
            ->join('factory_beds', function ($join) {
                $join->on('factory_beds.factory_code', '=', 'panel_state.factory_code')
                    ->on('factory_beds.row', '=', 'panel_state.bed_row')
                    ->on('factory_beds.column', '=', 'panel_state.bed_column');
            })
            ->where('panel_state.factory_code', $factory->factory_code)
            ->whereBetween('panel_state.date', [
                $working_date->startOfWeek()->format('Y-m-d'),
                $working_date->endOfWeek()->format('Y-m-d')
            ])
            ->whereNotNull('panel_state.factory_species_code')
            ->orderBy('panel_state.date', 'ASC')
            ->orderBy('panel_state.bed_column', 'ASC')
            ->orderBy('panel_state.bed_row', 'ASC')
            ->orderBy('panel_state.x_current_bed_position', 'ASC')
            ->orderBy('panel_state.y_current_bed_position', 'ASC')
            ->get();
    }

    /**
     * 指定工場のパネルID最大値を取得
     *
     * @param  string $factory_code
     * @return int
     */
    public function getMaxPanelId(string $factory_code): int
    {
        return $this->model
            ->where('factory_code', $factory_code)
            ->max('panel_id') ?: 0;
    }

    /**
     * 活動実績入力（パネル状況）の更新
     *
     * @param array $params
     * @return void
     */
    public function updatePanelStatus(array $params): void
    {
        $this->model->where('factory_code', $params['factory_code'])
            ->where('panel_id', $params['panel_id'])
            ->where('bed_row', $params['bed_row'])
            ->where('bed_column', $params['bed_column'])
            ->update(array_only($params, ['panel_status', 'using_hole_count', 'updated_by']));
    }

    /**
     * 指定されたパネル配置図データのベッドに含まれるパネルの前日の状態を取得
     *
     * @param  \App\Models\Plan\PlannedArrangementStatusWork $pasw
     * @return \App\Models\Plan\Collections\PanelStateCollection
     */
    public function getPanelStatesOfPreviousDay(PlannedArrangementStatusWork $pasw): PanelStateCollection
    {
        return $this->model
            ->where('factory_code', $pasw->factory_code)
            ->where('date', Chronos::parse($pasw->date)->subDay()->format('Y-m-d'))
            ->where('bed_column', $pasw->bed_column)
            ->orderBy('bed_row', 'ASC')
            ->orderBy('x_current_bed_position', 'ASC')
            ->orderBy('y_current_bed_position', 'ASC')
            ->get();
    }

    /**
     * 作業日ごとの生育段階のパネル枚数を取得
     *
     * @param  array $params
     * @return \App\Models\Plan\Collections\PanelStateCollection
     */
    public function getGrowingPanelCountList(array $params): PanelStateCollection
    {
        return $this->model
            ->select([
                'factory_code',
                'factory_species_code',
                'growing_stage_sequence_number',
                'number_of_holes',
                'stage_start_date AS date',
                $this->db->raw('COUNT(panel_state.panel_id) AS panel_count')
            ])
            ->where('factory_code', $params['factory_code'])
            ->where(function ($query) use ($params) {
                if ($factory_species_code = $params['factory_species_code'] ?? null) {
                    $query->where('factory_species_code', $factory_species_code);
                }
            })
            ->whereBetween('stage_start_date', [
                $params['date_from']->format('Y-m-d'),
                $params['date_to']->addWeeks(2)->format('Y-m-d')
            ])
            ->where('panel_status', PanelStatus::OPERATION)
            ->whereRaw('`date` = stage_start_date')
            ->groupBy([
                'factory_code',
                'factory_species_code',
                'growing_stage_sequence_number',
                'number_of_holes',
                'stage_start_date'
            ])
            ->get();
    }

    /**
     * 作業日ごとの収穫段階のパネル枚数を取得
     *
     * @param  array $params
     * @return \App\Models\Plan\Collections\PanelStateCollection
     */
    public function getHravestingPanelCountList($params): PanelStateCollection
    {
        return $this->model
            ->select([
                'factory_code',
                'factory_species_code',
                'growing_stage_sequence_number',
                'next_growth_stage_date AS date',
                $this->db->raw('COUNT(panel_state.panel_id) AS panel_count')
            ])
            ->where('factory_code', $params['factory_code'])
            ->where(function ($query) use ($params) {
                if ($factory_species_code = $params['factory_species_code'] ?? null) {
                    $query->where('factory_species_code', $factory_species_code);
                }
            })
            ->whereBetween('next_growth_stage_date', [
                $params['date_from']->format('Y-m-d'),
                $params['date_to']->format('Y-m-d')
            ])
            ->where('panel_state.current_growth_stage', GrowingStage::PLANTING)
            ->where('panel_status', PanelStatus::OPERATION)
            ->whereRaw('next_growth_stage_date = DATE_ADD(date, INTERVAL 1 day)')
            ->groupBy([
                'factory_code', 'factory_species_code', 'growing_stage_sequence_number', 'next_growth_stage_date'
            ])
            ->get();
    }

    /**
     * ベッドごとの最新のパネル状況日付を取得
     *
     * @param  array $params
     * @return \App\Models\Plan\Collections\PanelStateCollection
     */
    public function getLateseDatePerBed(array $params): PanelStateCollection
    {
        $query = $this->model
            ->select([
                'factory_code',
                'bed_row',
                'bed_column',
                $this->db->raw('MAX(`date`) AS `date`')
            ])
            ->where(function ($query) use ($params) {
                if ($factory_code = $params['factory_code']) {
                    $query->where('factory_code', $factory_code);
                }
            })
            ->where(function ($query) use ($params) {
                if ($factory_code = $params['bed_row']) {
                    $query->where('bed_row', $factory_code);
                }
            })
            ->where(function ($query) use ($params) {
                if ($factory_code = $params['bed_column']) {
                    $query->where('bed_column', $factory_code);
                }
            })
            ->groupBy(['factory_code', 'bed_row', 'bed_column']);

        if ($date = $params['date']) {
            $query->having('date', '<', $date);
        }

        return $query->get();
    }

    /**
     * パネル状況データの再登録
     *
     * @param  \App\Models\Plan\PlannedArrangementStatusWork $pasw
     * @param  array $panel_states_list
     * @return void
     */
    public function replacePanels(PlannedArrangementStatusWork $pasw, array $panel_states_list): void
    {
        if (count($panel_states_list) === 0) {
            return;
        }

        $this->db->table($this->model->getTable())
            ->where('factory_code', $pasw->factory_code)
            ->where('date', $pasw->date)
            ->whereIn('bed_row', array_keys($panel_states_list))
            ->where('bed_column', $pasw->bed_column)
            ->delete();

        $this->db->table($this->model->getTable())->insert(
            array_reduce(
                $panel_states_list,
                function ($flatten, $panel_states) {
                    foreach ($panel_states as $ps) {
                        $flatten[] = $ps;
                    }

                    return $flatten;
                },
                []
            )
        );
    }

    /**
     * 指定したベッドの指定日から設定日数までのパネル状況を複製する
     *
     * @param  string $factory_code
     * @param  array $moving_panel_dates
     * @return void
     */
    public function replicateFixedPanelStates(string $factory_code, array $moving_panel_dates): void
    {
        foreach ($moving_panel_dates as $bed_column => $rows) {
            foreach ($rows as $bed_row => $date) {
                if ($date === '') {
                    continue;
                }

                $this->model->where('factory_code', $factory_code)
                    ->where('bed_row', $bed_row)
                    ->where('bed_column', $bed_column)
                    ->where('date', '>', $date)
                    ->delete();

                $this->replicatePanelStates(
                    $factory_code,
                    $bed_row,
                    $bed_column,
                    Chronos::parse($date)->addMonth()->format('Y-m-d')
                );
            }
        }
    }

    /**
     * 特定のベッドのパネル状況を指定日まで複製する
     *
     * @param  string $factory_code
     * @param  int $bed_row
     * @param  int $bed_column
     * @param  string $date
     * @return void
     */
    public function replicatePanelStates(string $factory_code, int $bed_row, int $bed_column, string $date): void
    {
        $this->db->statement("CALL replication_panel_state('{$factory_code}', {$bed_row}, {$bed_column}, '{$date}')");
    }
}

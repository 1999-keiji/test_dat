<?php

declare(strict_types=1);

namespace App\Repositories\Shipment;

use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder;
use App\Models\Master\Factory;
use App\Models\Master\Species;
use App\Models\Shipment\ProductizedResult;
use App\Models\Shipment\Collections\ProductizedResultCollection;
use App\ValueObjects\Date\HarvestingDate;
use App\ValueObjects\Enum\GrowingStage;

class ProductizedResultRepository
{
    /**
     * @var \Illuminate\Database\Connection
     */
    private $db;

    /**
     * @var \App\Models\Shipment\ProductizedResult
     */
    private $model;

    /**
     * @param \Illuminate\Database\Connection $db
     * @param \App\Models\Shipment\ProductizedResult $model
     * @return void
     */
    public function __construct(Connection $db, ProductizedResult $model)
    {
        $this->db = $db;
        $this->model = $model;
    }

    /**
     * 主キーにより、製品化実績を取得
     *
     * @param  array $primary_key
     * @return \App\Models\Shipment\ProductizedResult
     */
    public function find(array $primary_key): ProductizedResult
    {
        $query = $this->model->newQuery();
        foreach ($this->model->getKeyName() as $key) {
            $query->where($key, $primary_key[$key]);
        }

        return $query->firstOrFail();
    }

    /**
     * 製品化実績の検索
     *
     * @param  array $params
     * @return \App\Models\Shipment\Collections\ProductizedResultCollection
     */
    public function search(array $params): ProductizedResultCollection
    {
        $sub_query = $this->getAggregatePanelStateQuery()
            ->from($this->db->raw('panel_state USE INDEX (panel_state_factory_code_next_growth_stage_date_index)'))
            ->where('panel_state.factory_code', $params['factory_code'])
            ->where(function ($query) use ($params) {
                $harvesting_date = $params['harvesting_date'] ?? null;
                if (! is_null($harvesting_date)) {
                    $query->where('panel_state.next_growth_stage_date', $params['harvesting_date']);
                }
                if (is_null($harvesting_date)) {
                    $query->whereRaw(
                        'panel_state.next_growth_stage_date BETWEEN DATE_SUB(CURRENT_DATE, INTERVAL 6 DAY) AND '.
                        'CURRENT_DATE'
                    );
                }
            })
            ->where(function ($query) use ($params) {
                if ($species_code = $params['species_code'] ?? null) {
                    $query->where('factory_species.species_code', $species_code);
                }
            })
            ->where(
                'panel_state.date',
                $this->db->raw('DATE_ADD(panel_state.next_growth_stage_date, INTERVAL -1 day)')
            )
            ->where('panel_state.current_growth_stage', GrowingStage::PLANTING);

        $productized_result_detail_query = $this->db
            ->table('productized_result_details')
            ->select([
                'factory_code',
                'species_code',
                'harvesting_date',
                $this->db->raw('SUM(number_of_heads * product_quantity) AS producted_quantity')
            ])
            ->groupBy('factory_code', 'species_code', 'harvesting_date');

        return $this->model
            ->select([
                'forecasted_product_rates.factory_code',
                'forecasted_product_rates.species_code',
                'species.species_name',
                'forecasted_product_rates.date AS harvesting_date',
                'forecasted_product_rates.crop_failure AS forecasted_crop_failure',
                'forecasted_product_rates.advanced_harvest AS forecasted_advanced_harvest',
                $this->db->raw('COALESCE(panel_states.harvesting_quantity, 0) AS harvesting_quantity'),
                'productized_results.crop_failure',
                'productized_results.advanced_harvest',
                'productized_result_details.producted_quantity'
            ])
            ->rightJoin('forecasted_product_rates', function ($join) {
                $join->on('forecasted_product_rates.factory_code', '=', 'productized_results.factory_code')
                    ->on('forecasted_product_rates.species_code', '=', 'productized_results.species_code')
                    ->on('forecasted_product_rates.date', '=', 'productized_results.harvesting_date');
            })
            ->leftJoin('species', 'species.species_code', '=', 'forecasted_product_rates.species_code')
            ->leftJoin(
                $this->db->raw("({$sub_query->toSql()}) AS panel_states"),
                function ($join) {
                    $join->on('panel_states.factory_code', '=', 'forecasted_product_rates.factory_code')
                        ->on('panel_states.species_code', '=', 'forecasted_product_rates.species_code')
                        ->on('panel_states.harvesting_date', '=', 'forecasted_product_rates.date');
                }
            )
            ->leftJoin(
                $this->db->raw("({$productized_result_detail_query->toSql()}) AS productized_result_details"),
                function ($join) {
                    $join->on('productized_result_details.factory_code', '=', 'productized_results.factory_code')
                        ->on('productized_result_details.species_code', '=', 'productized_results.species_code')
                        ->on('productized_result_details.harvesting_date', '=', 'productized_results.harvesting_date');
                }
            )
            ->setBindings($sub_query->getBindings())
            ->where('forecasted_product_rates.factory_code', $params['factory_code'])
            ->where(function ($query) use ($params) {
                if ($species_code = $params['species_code'] ?? null) {
                    $query->where('forecasted_product_rates.species_code', $species_code);
                }
            })
            ->where(function ($query) use ($params) {
                $harvesting_date = $params['harvesting_date'] ?? null;
                if (! is_null($harvesting_date)) {
                    $query->where('forecasted_product_rates.date', $params['harvesting_date']);
                }
                if (is_null($harvesting_date)) {
                    $query->whereRaw(
                        'forecasted_product_rates.date BETWEEN DATE_SUB(CURRENT_DATE, INTERVAL 6 DAY) AND CURRENT_DATE'
                    );
                }
            })
            ->orderBy('forecasted_product_rates.date', 'ASC')
            ->orderBy('forecasted_product_rates.species_code', 'ASC')
            ->get();
    }

    /**
     * 指定された収穫日の製品化実績データを取得
     *
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Master\Species $species
     * @param  \App\ValueObjects\Date\HarvestingDate $harvesting_date
     * @return \App\Models\Shipment\ProductizedResult
     */
    public function getProductizedResult(
        Factory $factory,
        Species $species,
        HarvestingDate $harvesting_date
    ): ProductizedResult {
        $sub_query = $this->getAggregatePanelStateQuery()
            ->from($this->db->raw('panel_state USE INDEX (panel_state_factory_code_next_growth_stage_date_index)'))
            ->where('panel_state.factory_code', $factory->factory_code)
            ->where('factory_species.species_code', $species->species_code)
            ->where('panel_state.next_growth_stage_date', $harvesting_date->format('Y-m-d'))
            ->where(
                'panel_state.date',
                $this->db->raw('DATE_ADD(panel_state.next_growth_stage_date, INTERVAL -1 day)')
            )
            ->where('panel_state.current_growth_stage', GrowingStage::PLANTING);

        return $this->model
            ->select([
                'forecasted_product_rates.date AS harvesting_date',
                $this->db->raw('COALESCE(panel_states.harvesting_quantity, 0) AS harvesting_quantity'),
                'forecasted_product_rates.product_rate',
                'forecasted_product_rates.crop_failure AS forecasted_crop_failure',
                'forecasted_product_rates.advanced_harvest AS forecasted_advanced_harvest',
                'productized_results.triming',
                'productized_results.product_failure',
                'productized_results.packing',
                'productized_results.crop_failure',
                'productized_results.sample',
                'productized_results.advanced_harvest',
                'productized_results.weight_of_discarded'
            ])
            ->rightJoin('forecasted_product_rates', function ($join) {
                $join->on('forecasted_product_rates.factory_code', '=', 'productized_results.factory_code')
                    ->on('forecasted_product_rates.species_code', '=', 'productized_results.species_code')
                    ->on('forecasted_product_rates.date', '=', 'productized_results.harvesting_date');
            })
            ->leftJoin(
                $this->db->raw("({$sub_query->toSql()}) AS panel_states"),
                function ($join) {
                    $join->on('panel_states.factory_code', '=', 'forecasted_product_rates.factory_code')
                        ->on('panel_states.species_code', '=', 'forecasted_product_rates.species_code')
                        ->on('panel_states.harvesting_date', '=', 'forecasted_product_rates.date');
                }
            )
            ->setBindings($sub_query->getBindings())
            ->where('forecasted_product_rates.factory_code', $factory->factory_code)
            ->where('forecasted_product_rates.species_code', $species->species_code)
            ->where('forecasted_product_rates.date', $harvesting_date->format('Y-m-d'))
            ->first();
    }

    /**
     * 指定された期間の収穫日の分だけ、製品化実績データを取得
     *
     * @param  \App\Models\Master\Factory $factory
     * @param  array $harvesting_dates
     * @return \App\Models\Shipment\Collections\ProductizedResultCollection
     */
    public function getProductizedResultsByHarvestingDate(
        Factory $factory,
        array $harvesting_dates
    ): ProductizedResultCollection {
        $sub_query = $this->getAggregatePanelStateQuery()
            ->from($this->db->raw('panel_state USE INDEX (panel_state_factory_code_next_growth_stage_date_index)'))
            ->where('panel_state.factory_code', $factory->factory_code)
            ->whereBetween('panel_state.next_growth_stage_date', [
                head($harvesting_dates)->format('Y-m-d'),
                last($harvesting_dates)->format('Y-m-d'),
            ])
            ->where('panel_state.date', $this->db->raw('DATE_ADD(panel_state.next_growth_stage_date, INTERVAL -1 day)'))
            ->where('panel_state.current_growth_stage', GrowingStage::PLANTING);

        $productized_result_detail_query = $this->db
            ->table('productized_result_details')
            ->select([
                'factory_code',
                'species_code',
                'harvesting_date',
                $this->db->raw('SUM(number_of_heads * product_quantity) AS producted_quantity'),
                $this->db->raw('SUM(weight_per_number_of_heads * product_quantity) AS producted_weight')
            ])
            ->groupBy('factory_code', 'species_code', 'harvesting_date');

        return $this->model
            ->select([
                'forecasted_product_rates.species_code',
                'species.species_name',
                'forecasted_product_rates.date AS harvesting_date',
                $this->db->raw('COALESCE(panel_states.harvesting_quantity, 0) AS harvesting_quantity'),
                'productized_results.triming',
                'productized_results.product_failure',
                'productized_results.packing',
                'productized_results.crop_failure',
                'productized_results.sample',
                'productized_results.advanced_harvest',
                'productized_results.weight_of_discarded',
                'productized_result_details.producted_quantity',
                'productized_result_details.producted_weight'
            ])
            ->rightJoin('forecasted_product_rates', function ($join) {
                $join->on('forecasted_product_rates.factory_code', '=', 'productized_results.factory_code')
                    ->on('forecasted_product_rates.species_code', '=', 'productized_results.species_code')
                    ->on('forecasted_product_rates.date', '=', 'productized_results.harvesting_date');
            })
            ->leftJoin(
                $this->db->raw("({$sub_query->toSql()}) AS panel_states"),
                function ($join) {
                    $join->on('panel_states.factory_code', '=', 'forecasted_product_rates.factory_code')
                        ->on('panel_states.species_code', '=', 'forecasted_product_rates.species_code')
                        ->on('panel_states.harvesting_date', '=', 'forecasted_product_rates.date');
                }
            )
            ->leftJoin('species', 'species.species_code', '=', 'forecasted_product_rates.species_code')
            ->setBindings($sub_query->getBindings())
            ->leftJoin(
                $this->db->raw("({$productized_result_detail_query->toSql()}) AS productized_result_details"),
                function ($join) {
                    $join->on('productized_result_details.factory_code', '=', 'productized_results.factory_code')
                        ->on('productized_result_details.species_code', '=', 'productized_results.species_code')
                        ->on('productized_result_details.harvesting_date', '=', 'productized_results.harvesting_date');
                }
            )
            ->where('forecasted_product_rates.factory_code', $factory->factory_code)
            ->whereBetween('forecasted_product_rates.date', [
                head($harvesting_dates)->format('Y-m-d'),
                last($harvesting_dates)->format('Y-m-d'),
            ])
            ->orderBy('forecasted_product_rates.species_code', 'ASC')
            ->orderBy('forecasted_product_rates.date', 'ASC')
            ->get();
    }

    /**
     * 集計パネル状況Tクエリ生成
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function getAggregatePanelStateQuery(): Builder
    {
        return $this->db
            ->table('panel_state')
            ->select([
                'panel_state.factory_code',
                'factory_species.species_code',
                $this->db->raw('panel_state.next_growth_stage_date AS harvesting_date'),
                $this->db->raw(
                    'SUM(CASE WHEN panel_state.using_hole_count IS NULL '.
                    'THEN panel_state.number_of_holes '.
                    'ELSE panel_state.using_hole_count '.
                    'END) AS harvesting_quantity'
                )
            ])
            ->join('factory_species', function ($join) {
                $join->on('factory_species.factory_code', '=', 'panel_state.factory_code')
                    ->on('factory_species.factory_species_code', '=', 'panel_state.factory_species_code');
            })
            ->groupBy(
                'panel_state.factory_code',
                'factory_species.species_code',
                'panel_state.next_growth_stage_date'
            );
    }

    /**
     * 製品化実績の登録
     *
     * @param  array $params
     * @return \App\Models\Shipment\ProductizedResult
     */
    public function create(array $params): ProductizedResult
    {
        $params['weight_of_discarded'] = $params['weight_of_discarded'] * 1000;

        $productized_result = $this->model->create($params);
        return $productized_result;
    }

    /**
     * 製品化実績の更新
     *
     * @param  \App\Models\Shipment\ProductizedResult $productized_result
     * @param  array $params
     * @return \App\Models\Shipment\ProductizedResult
     */
    public function update(ProductizedResult $productized_result, array $params): ProductizedResult
    {
        $params['weight_of_discarded'] = $params['weight_of_discarded'] * 1000;

        $productized_result->fill($params)->save();
        return $productized_result;
    }
}

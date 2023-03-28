<?php

declare(strict_types=1);

namespace App\Repositories\Plan;

use Illuminate\Database\Connection;
use App\Models\Master\Factory;
use App\Models\Master\Species;
use App\Models\Plan\ForecastedProductRate;
use App\Models\Plan\Collections\ForecastedProductRateCollection;
use App\ValueObjects\Date\HarvestingDate;

class ForecastedProductRateRepository
{
    /**
     * @var \Illuminate\Database\Connection
     */
    private $db;

    /**
     * @var \App\Models\Plan\ForecastedProductRate
     */
    private $model;

    /**
     * @param  \Illuminate\Database\Connection $db
     * @param  \App\Models\Plan\ForecastedProductRate $model
     * @return void
     */
    public function __construct(Connection $db, ForecastedProductRate $model)
    {
        $this->db = $db;
        $this->model = $model;
    }

    /**
     * 工場と品種と収穫日を指定して予想製品化率を取得
     *
     * @param  array $params
     * @return \App\Models\Plan\ForecastedProductRate
     */
    public function getForecastedProductRate(array $params): ?ForecastedProductRate
    {
        return $this->model
            ->select([
                'forecasted_product_rates.factory_code',
                'forecasted_product_rates.species_code',
                'forecasted_product_rates.date',
                'forecasted_product_rates.product_rate',
                $this->db->raw(
                    '(CASE WHEN (productized_results.factory_code IS NULL AND '.
                    'productized_results.species_code IS NULL AND '.
                    'productized_results.harvesting_date IS NULL) '.
                    'THEN 1 ELSE 0 END) AS can_import'
                ),
                'forecasted_product_rates.updated_at'
            ])
            ->leftJoin('productized_results', function ($join) {
                $join->on('productized_results.factory_code', '=', 'forecasted_product_rates.factory_code')
                    ->on('productized_results.species_code', '=', 'forecasted_product_rates.species_code')
                    ->on('productized_results.harvesting_date', '=', 'forecasted_product_rates.date');
            })
            ->where('forecasted_product_rates.factory_code', $params['factory_code'])
            ->where('forecasted_product_rates.species_code', $params['species_code'])
            ->where('forecasted_product_rates.date', $params['date'])
            ->first();
    }

    /**
     * 指定された品種、収穫期間に応じて予想製品化率データを取得
     *
     * @param  array $params
     * @param  array $harvesting_date_term
     * @return \App\Models\Plan\Collections\ForecastedProductRateCollection
     */
    public function getForecastedProductRatesBySpeciesAndHarvestingDate(
        array $params,
        array $harvesting_date_term
    ): ForecastedProductRateCollection {
        $sub_query = $this->db->table('stocks')
            ->select([
                'factory_code',
                'species_code',
                'disposal_at',
                $this->db->raw('SUM(disposal_weight) AS disposal_weight')
            ])
            ->where('species_code', $params['species_code'])
            ->where(function ($query) use ($params) {
                if ($factory_code = $params['factory_code'] ?? null) {
                    $query->where('factory_code', $factory_code);
                }
            })
            ->whereBetween('disposal_at', [
                $harvesting_date_term['from']->format('Y-m-d'),
                $harvesting_date_term['to']->format('Y-m-d')
            ])
            ->groupBy(['factory_code', 'species_code', 'disposal_at']);

        $query = $this->model
            ->select(['forecasted_product_rates.factory_code', 'forecasted_product_rates.species_code'])
            ->leftJoin($this->db->raw("({$sub_query->toSql()}) AS disposed_stocks"), function ($join) {
                $join->on('disposed_stocks.factory_code', '=', 'forecasted_product_rates.factory_code')
                    ->on('disposed_stocks.species_code', '=', 'forecasted_product_rates.species_code')
                    ->on('disposed_stocks.disposal_at', '=', 'forecasted_product_rates.date');
            })
            ->setBindings($sub_query->getBindings())
            ->where(function ($query) use ($params) {
                if ($factory_code = $params['factory_code'] ?? null) {
                    $query->where('forecasted_product_rates.factory_code', $factory_code);
                }
            })
            ->where('forecasted_product_rates.species_code', $params['species_code']);

        if ($params['display_term'] === 'date') {
            $query
                ->addSelect([
                    $this->db->raw('forecasted_product_rates.date AS harvesting_date'),
                    'forecasted_product_rates.product_rate',
                    'forecasted_product_rates.crop_failure',
                    'forecasted_product_rates.advanced_harvest',
                    $this->db->raw(
                        '(CASE WHEN (productized_results.factory_code IS NOT NULL AND '.
                        'productized_results.species_code IS NOT NULL AND '.
                        'productized_results.harvesting_date IS NOT NULL) '.
                        'THEN 1 ELSE 0 END) AS has_productized'
                    ),
                    $this->db->raw('COALESCE(productized_results.crop_failure, 0) AS actual_crop_failure'),
                    $this->db->raw('COALESCE(productized_results.advanced_harvest, 0) AS actual_advanced_harvest'),
                    $this->db->raw('COALESCE(productized_results.triming, 0) AS triming'),
                    $this->db->raw('COALESCE(productized_results.product_failure, 0) AS product_failure'),
                    $this->db->raw('COALESCE(productized_results.packing, 0) AS packing'),
                    $this->db->raw('COALESCE(productized_results.sample, 0) AS sample'),
                    $this->db->raw('COALESCE(productized_results.weight_of_discarded, 0) AS weight_of_discarded'),
                    'disposed_stocks.disposal_weight'
                ])
                ->leftJoin('productized_results', function ($join) {
                    $join->on('productized_results.factory_code', '=', 'forecasted_product_rates.factory_code')
                        ->on('productized_results.species_code', '=', 'forecasted_product_rates.species_code')
                        ->on('productized_results.harvesting_date', '=', 'forecasted_product_rates.date');
                })
                ->where(
                    'forecasted_product_rates.date',
                    '>=',
                    $harvesting_date_term['from']->subWeek(4)->startOfWeek()->format('Y-m-d')
                )
                ->where('forecasted_product_rates.date', '<=', $harvesting_date_term['to']->format('Y-m-d'))
                ->orderBy('forecasted_product_rates.date', 'ASC');
        }
        if ($params['display_term'] === 'month') {
            $query
                ->addSelect([
                    $this->db->raw("DATE_FORMAT(forecasted_product_rates.date, '%Y%m') AS harvesting_month"),
                    $this->db->raw('AVG(forecasted_product_rates.product_rate) AS product_rate'),
                    $this->db->raw('SUM(disposed_stocks.disposal_weight) AS disposal_weight')
                ])
                ->whereBetween('forecasted_product_rates.date', [
                    $harvesting_date_term['from']->format('Y-m-d'),
                    $harvesting_date_term['to']->format('Y-m-d')
                ])
                ->groupBy(
                    'forecasted_product_rates.factory_code',
                    'forecasted_product_rates.species_code',
                    $this->db->raw("DATE_FORMAT(forecasted_product_rates.date, '%Y%m')")
                )
                ->orderBy('harvesting_month', 'ASC');
        }

        return $query->get();
    }

    /**
     * 工場と品種を指定して、既定の日数分、収穫日ごとの予想製品化率を取得
     *
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Master\Species $species
     * @param  \App\ValueObjects\Date\HarvestingDate $harvesting_date
     * @return \App\Models\Plan\Collections\ForecastedProductRateCollection
     */
    public function getForecastedProductRatesByFactoryAndSpeciesAndHarvestingDate(
        Factory $factory,
        Species $species,
        HarvestingDate $harvesting_date
    ): ForecastedProductRateCollection {
        return $this->model
            ->select([
                'forecasted_product_rates.factory_code',
                'forecasted_product_rates.species_code',
                'forecasted_product_rates.date AS harvesting_date',
                'forecasted_product_rates.product_rate',
                'forecasted_product_rates.crop_failure',
                'forecasted_product_rates.advanced_harvest',
                $this->db->raw('COALESCE(productized_results.crop_failure, 0) AS actual_crop_failure'),
                $this->db->raw('COALESCE(productized_results.advanced_harvest, 0) AS actual_advanced_harvest'),
                $this->db->raw(
                    '(CASE WHEN (productized_results.factory_code IS NULL AND '.
                    'productized_results.species_code IS NULL AND '.
                    'productized_results.harvesting_date IS NULL) '.
                    'THEN 1 ELSE 0 END) AS can_import'
                ),
                'forecasted_product_rates.updated_at'
            ])
            ->leftJoin('productized_results', function ($join) {
                $join->on('productized_results.factory_code', '=', 'forecasted_product_rates.factory_code')
                    ->on('productized_results.species_code', '=', 'forecasted_product_rates.species_code')
                    ->on('productized_results.harvesting_date', '=', 'forecasted_product_rates.date');
            })
            ->where('forecasted_product_rates.factory_code', $factory->factory_code)
            ->where('forecasted_product_rates.species_code', $species->species_code)
            ->whereBetween('forecasted_product_rates.date', [
                $harvesting_date->format('Y-m-d'),
                $harvesting_date->getEndOfDateOfGrowthSaleManagement()->format('Y-m-d')
            ])
            ->orderBy('forecasted_product_rates.date', 'ASC')
            ->get();
    }

    /**
     * 予想製品化率の登録
     *
     * @param  array $params
     * @return \App\Models\Plan\ForecastedProductRate
     */
    public function create(array $params): ForecastedProductRate
    {
        return $this->model->create([
            'factory_code' => $params['factory_code'],
            'species_code' => $params['species_code'],
            'date' => $params['date'],
            'product_rate' => $params['product_rate'],
            'crop_failure' => $params['crop_failure'] ?: 0,
            'advanced_harvest' => $params['advanced_harvest'] ?: 0
        ]);
    }

    /**
     * 予想製品化率の更新
     *
     * @param  \App\Models\Plan\ForecastedProductRate $forecasted_product_rate
     * @param  array $params
     * @return \App\Models\Plan\ForecastedProductRate $forecasted_product_rate
     */
    public function update(ForecastedProductRate $forecasted_product_rate, array $params): ForecastedProductRate
    {
        $forecasted_product_rate->fill([
            'product_rate' => $params['product_rate'],
            'crop_failure' => $params['crop_failure'] ?: 0,
            'advanced_harvest' => $params['advanced_harvest'] ?: 0
        ])
            ->save();

        return $forecasted_product_rate;
    }
}

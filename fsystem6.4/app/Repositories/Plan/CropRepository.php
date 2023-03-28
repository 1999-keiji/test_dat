<?php

declare(strict_types=1);

namespace App\Repositories\Plan;

use Illuminate\Database\Connection;
use App\Models\Master\Factory;
use App\Models\Master\Species;
use App\Models\Plan\Crop;
use App\Models\Plan\Collections\CropCollection;
use App\ValueObjects\Date\HarvestingDate;
use App\ValueObjects\Date\WorkingDate;

class CropRepository
{
    /**
     * @var \Illuminate\Database\Connection
     */
    private $db;

    /**
     * @var \App\Models\Plan\Crop
     */
    private $model;

    /**
     * @param  \Illuminate\Database\Connection $db
     * @param  \App\Models\Plan\Crop $model
     * @return void
     */
    public function __construct(Connection $db, Crop $model)
    {
        $this->db = $db;
        $this->model = $model;
    }

    /**
     * 出来高データの取得
     *
     * @param  array $params
     * @return \App\Models\Plan\Crop
     */
    public function getCrop($params): ?Crop
    {
        return $this->model
            ->select([
                'crop.factory_code',
                'crop.species_code',
                'crop.number_of_heads',
                'crop.weight_per_number_of_heads',
                'crop.input_group',
                'crop.date',
                $this->db->raw(
                    '(CASE WHEN (productized_result_details.factory_code IS NULL AND '.
                    'productized_result_details.species_code IS NULL AND '.
                    'productized_result_details.harvesting_date IS NULL AND '.
                    'productized_result_details.number_of_heads IS NULL AND '.
                    'productized_result_details.weight_per_number_of_heads IS NULL AND '.
                    'productized_result_details.input_group IS NULL) '.
                    'THEN 1 ELSE 0 END) AS can_import'
                ),
                'crop.updated_at'
            ])
            ->leftJoin('productized_result_details', function ($join) {
                $join->on('productized_result_details.factory_code', '=', 'crop.factory_code')
                    ->on('productized_result_details.species_code', '=', 'crop.species_code')
                    ->on('productized_result_details.harvesting_date', '=', 'crop.date')
                    ->on('productized_result_details.number_of_heads', '=', 'crop.number_of_heads')
                    ->on(
                        'productized_result_details.weight_per_number_of_heads',
                        '=',
                        'crop.weight_per_number_of_heads'
                    )
                    ->on('productized_result_details.input_group', '=', 'crop.input_group');
            })
            ->where('crop.factory_code', $params['factory_code'])
            ->where('crop.species_code', $params['species_code'])
            ->where('crop.number_of_heads', $params['number_of_heads'])
            ->where('crop.weight_per_number_of_heads', $params['weight_per_number_of_heads'])
            ->where('crop.input_group', $params['input_group'])
            ->where('crop.date', $params['date'])
            ->first();
    }

    /**
     * 工場コード、品種コード、作業日を指定してデータを取得
     *
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Master\Species $species
     * @param  \App\ValueObjects\Date\WorkingDate $working_date
     * @return \App\Models\Plan\Collections\CropCollection
     */
    public function getCrops(Factory $factory, Species $species, WorkingDate $working_date): CropCollection
    {
        return $this->model
            ->select([
                'crop.number_of_heads',
                'crop.weight_per_number_of_heads',
                'crop.input_group',
                'crop.crop_number',
                'productized_result_details.product_quantity'
            ])
            ->leftJoin('productized_result_details', function ($join) {
                $join->on('productized_result_details.factory_code', '=', 'crop.factory_code')
                    ->on('productized_result_details.species_code', '=', 'crop.species_code')
                    ->on('productized_result_details.harvesting_date', '=', 'crop.date')
                    ->on('productized_result_details.number_of_heads', '=', 'crop.number_of_heads')
                    ->on(
                        'productized_result_details.weight_per_number_of_heads',
                        '=',
                        'crop.weight_per_number_of_heads'
                    )
                    ->on('productized_result_details.input_group', '=', 'crop.input_group');
            })
            ->where('crop.factory_code', $factory->factory_code)
            ->where('crop.species_code', $species->species_code)
            ->where('crop.date', $working_date->format('Y-m-d'))
            ->orderBy('crop.number_of_heads', 'ASC')
            ->orderBy('crop.weight_per_number_of_heads', 'ASC')
            ->orderBy('crop.input_group', 'ASC')
            ->get();
    }

    /**
     * 指定された品種、期間に応じて出来高データを取得
     *
     * @param  array $params
     * @param  array $harvesting_date_term
     * @return \App\Models\Plan\Collections\CropCollection
     */
    public function getCropsBySpeciesAndHarvestingDate(array $params, array $harvesting_date_term): CropCollection
    {
        $query = $this->model
            ->select([
                'crop.factory_code',
                'crop.species_code',
                $this->db->raw('SUM(crop.crop_stock_number) AS crop_quantity'),
                $this->db->raw('SUM(crop.product_weight) AS crop_weight'),
                $this->db->raw(
                    'COALESCE(SUM(ROUND(productized_result_details.product_quantity * '.
                    'productized_result_details.number_of_heads)), 0) AS product_quantity'
                ),
                $this->db->raw(
                    'COALESCE(SUM(productized_result_details.product_quantity * '.
                    'productized_result_details.weight_per_number_of_heads), 0) AS product_weight'
                )
            ])
            ->leftJoin('productized_result_details', function ($join) {
                $join->on('productized_result_details.factory_code', '=', 'crop.factory_code')
                    ->on('productized_result_details.species_code', '=', 'crop.species_code')
                    ->on('productized_result_details.harvesting_date', '=', 'crop.date')
                    ->on('productized_result_details.number_of_heads', '=', 'crop.number_of_heads')
                    ->on(
                        'productized_result_details.weight_per_number_of_heads',
                        '=',
                        'crop.weight_per_number_of_heads'
                    )
                    ->on('productized_result_details.input_group', '=', 'crop.input_group');
            })
            ->where('crop.species_code', $params['species_code'])
            ->where(function ($query) use ($params) {
                if ($factory_code = $params['factory_code'] ?? null) {
                    $query->where('crop.factory_code', $factory_code);
                }
            })
            ->groupBy('crop.factory_code', 'crop.species_code')
            ->orderBy('crop.factory_code', 'ASC');

        if ($params['display_term'] === 'date') {
            $query->addSelect('crop.date AS harvesting_date')
                ->where(
                    'crop.date',
                    '>=',
                    $harvesting_date_term['from']->subWeek(4)->startOfWeek()->format('Y-m-d')
                )
                ->where('crop.date', '<=', $harvesting_date_term['to']->format('Y-m-d'))
                ->groupBy('crop.date')
                ->orderBy('crop.date', 'ASC');
        }
        if ($params['display_term'] === 'month') {
            $query
                ->addSelect(
                    $this->db->raw("DATE_FORMAT(crop.date, '%Y%m') AS harvesting_month")
                )
                ->whereBetween('crop.date', [
                    $harvesting_date_term['from']->format('Y-m-d'),
                    $harvesting_date_term['to']->format('Y-m-d')
                ])
                ->groupBy($this->db->raw("DATE_FORMAT(date, '%Y%m')"))
                ->orderBy('harvesting_month', 'ASC');
        }

        return $query->get();
    }

    /**
     * 品種、工場、商品規格を指定して、既定の分だけ収穫日ごとの出来高株数を取得
     *
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Master\Species $species
     * @param  \App\ValueObjects\Date\HarvestingDate $harvesting_date
     * @return \App\Models\Plan\Collections\CropCollection
     */
    public function getCropsByFactoryAndSpeciesAndHarvestingDate(
        Factory $factory,
        Species $species,
        HarvestingDate $harvesting_date,
        array $packaging_style
    ): CropCollection {
        $stocks_query = $this->db->table('stocks')
            ->select([
                'factory_code',
                'species_code',
                'disposal_at',
                'number_of_heads',
                'weight_per_number_of_heads',
                'input_group',
                $this->db->raw('SUM(stocks.disposal_quantity) AS disposal_quantity')
            ])
            ->where('factory_code', $factory->factory_code)
            ->where('species_code', $species->species_code)
            ->where('number_of_heads', $packaging_style['number_of_heads'])
            ->where('weight_per_number_of_heads', $packaging_style['weight_per_number_of_heads'])
            ->where('input_group', $packaging_style['input_group'])
            ->whereBetween('disposal_at', [
                $harvesting_date->format('Y-m-d'),
                $harvesting_date->getEndOfDateOfGrowthSaleManagement()->format('Y-m-d')
            ])
            ->groupBy(
                'factory_code',
                'species_code',
                'disposal_at',
                'number_of_heads',
                'weight_per_number_of_heads',
                'input_group'
            );

        return $this->model
            ->select([
                'crop.factory_code',
                'crop.species_code',
                'crop.date AS harvesting_date',
                'crop.crop_number',
                $this->db->raw('COALESCE(productized_result_details.product_quantity, 0) AS product_quantity'),
                $this->db->raw('COALESCE(dispoed_stocks.disposal_quantity, 0) AS disposal_quantity'),
                'crop.updated_at'
            ])
            ->leftJoin('productized_result_details', function ($join) {
                $join->on('productized_result_details.factory_code', '=', 'crop.factory_code')
                    ->on('productized_result_details.species_code', '=', 'crop.species_code')
                    ->on('productized_result_details.harvesting_date', '=', 'crop.date')
                    ->on('productized_result_details.number_of_heads', '=', 'crop.number_of_heads')
                    ->on(
                        'productized_result_details.weight_per_number_of_heads',
                        '=',
                        'crop.weight_per_number_of_heads'
                    )
                    ->on('productized_result_details.input_group', '=', 'crop.input_group');
            })
            ->leftJoin($this->db->raw("({$stocks_query->toSql()}) AS dispoed_stocks"), function ($join) {
                $join->on('dispoed_stocks.factory_code', '=', 'crop.factory_code')
                    ->on('dispoed_stocks.species_code', '=', 'crop.species_code')
                    ->on('dispoed_stocks.disposal_at', '=', 'crop.date')
                    ->on('dispoed_stocks.number_of_heads', '=', 'crop.number_of_heads')
                    ->on('dispoed_stocks.weight_per_number_of_heads', '=', 'crop.weight_per_number_of_heads')
                    ->on('dispoed_stocks.input_group', '=', 'crop.input_group');
            })
            ->setBindings($stocks_query->getBindings())
            ->where('crop.factory_code', $factory->factory_code)
            ->where('crop.species_code', $species->species_code)
            ->where('crop.number_of_heads', $packaging_style['number_of_heads'])
            ->where('crop.weight_per_number_of_heads', $packaging_style['weight_per_number_of_heads'])
            ->where('crop.input_group', $packaging_style['input_group'])
            ->whereBetween('crop.date', [
                $harvesting_date->format('Y-m-d'),
                $harvesting_date->getEndOfDateOfGrowthSaleManagement()->format('Y-m-d')
            ])
            ->orderBy('crop.date', 'ASC')
            ->get();
    }

    /**
     * 出来高の登録
     *
     * @param  array $params
     * @return \App\Models\Plan\Crop
     */
    public function create(array $params): Crop
    {
        return $this->model->create([
            'factory_code' => $params['factory_code'],
            'species_code' => $params['species_code'],
            'number_of_heads' => $params['number_of_heads'],
            'weight_per_number_of_heads' => $params['weight_per_number_of_heads'],
            'input_group' => $params['input_group'],
            'date' => $params['date'],
            'crop_number' => $params['crop_number'],
            'crop_stock_number' => $params['crop_stock_number'],
            'product_rate' => $params['product_rate'],
            'product_weight' => $params['product_weight']
        ]);
    }

    /**
     * 出来高の更新
     *
     * @param  \App\Models\Plan\Crop $crop
     * @param  array $params
     * @return \App\Models\Plan\Crop $crop
     */
    public function update(Crop $crop, array $params): Crop
    {
        $crop->fill([
            'crop_number' => $params['crop_number'],
            'crop_stock_number' => $params['crop_stock_number'],
            'product_rate' => $params['product_rate'],
            'product_weight' => $params['product_weight']
        ])
            ->save();

        return $crop;
    }
}

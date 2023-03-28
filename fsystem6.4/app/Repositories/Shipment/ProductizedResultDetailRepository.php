<?php

declare(strict_types=1);

namespace App\Repositories\Shipment;

use App\Models\Master\Factory;
use App\Models\Master\Species;
use App\Models\Shipment\ProductizedResultDetail;
use App\Models\Shipment\Collections\ProductizedResultDetailCollection;
use App\ValueObjects\Date\HarvestingDate;

class ProductizedResultDetailRepository
{
    /**
     * @var \App\Models\Shipment\ProductizedResultDetail
     */
    private $model;

    /**
     * @param \App\Models\Shipment\ProductizedResultDetail $model
     * @return void
     */
    public function __construct(ProductizedResultDetail $model)
    {
        $this->model = $model;
    }

    /**
     * 製品化実績明細データ取得
     *
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Master\Species $species
     * @param  \App\ValueObjects\Date\HarvestingDate $harvesting_date
     * @return App\Models\Shipment\Collections\ProductizedResultDetailCollection
     */
    public function getProductizedResultDetails(
        Factory $factory,
        Species $species,
        HarvestingDate $harvesting_date
    ): ProductizedResultDetailCollection {
        return $this->model
            ->select([
                'crop.number_of_heads',
                'crop.weight_per_number_of_heads',
                'crop.input_group',
                'crop.crop_number',
                'productized_result_details.product_quantity',
            ])
            ->rightJoin('crop', function ($join) {
                $join->on('crop.factory_code', '=', 'productized_result_details.factory_code')
                    ->on('crop.species_code', '=', 'productized_result_details.species_code')
                    ->on('crop.date', '=', 'productized_result_details.harvesting_date')
                    ->on('crop.number_of_heads', '=', 'productized_result_details.number_of_heads')
                    ->on(
                        'crop.weight_per_number_of_heads',
                        '=',
                        'productized_result_details.weight_per_number_of_heads'
                    )
                    ->on('crop.input_group', '=', 'productized_result_details.input_group');
            })
            ->where('crop.factory_code', $factory->factory_code)
            ->where('crop.species_code', $species->species_code)
            ->where('crop.date', $harvesting_date->format('Y-m-d'))
            ->orderBy('crop.number_of_heads', 'ASC')
            ->orderBy('crop.weight_per_number_of_heads', 'ASC')
            ->orderBy('crop.input_group', 'ASC')
            ->get();
    }

    /**
     * 製品化実績明細の登録
     *
     * @param  array $params
     * @return \App\Models\Shipment\ProductizedResultDetail
     */
    public function create(array $params): ProductizedResultDetail
    {
        $productized_result_detail = $this->model->create($params);
        return $productized_result_detail;
    }

    /**
     * 製品化実績明細の更新
     *
     * @param  \App\Models\Shipment\ProductizedResultDetail $productized_result_detail
     * @param  int $product_quantity
     * @return \App\Models\Shipment\ProductizedResultDetail
     */
    public function update(
        ProductizedResultDetail $productized_result_detail,
        int $product_quantity
    ): ProductizedResultDetail {
        $productized_result_detail->product_quantity = $product_quantity;
        $productized_result_detail->save();
        return $productized_result_detail;
    }
}

<?php

declare(strict_types=1);

namespace App\Repositories\Master;

use App\Models\Master\ProductSpecialPrice;
use App\Models\Master\Collections\ProductSpecialPriceCollection;

class ProductSpecialPriceRepository
{
    /**
     * @var \App\Models\Master\ProductSpecialPrice
     */
    private $model;

    /**
     * @param  \App\Models\Master\ProductSpecialPrice $model
     * @return void
     */
    public function __construct(ProductSpecialPrice $model)
    {
        $this->model = $model;
    }

    /**
     * 主キーにより一意のレコードを取得
     * @param  array $params
     */
    public function getProductSpecialPrice($params)
    {
        return $this->model
            ->where('delivery_destination_code', $params['delivery_destination_code'])
            ->where('factory_code', $params['factory_code'])
            ->where('currency_code', $params['currency_code'])
            ->where('application_started_on', $params['application_started_on'])
            ->where('application_ended_on', $params['application_ended_on'])
            ->first();
    }

    /**
     * 商品特価マスタを条件に応じて検索
     *
     * @param  array $params
     * @return \App\Models\Master\Collections\ProductSpecialPriceCollection
     */
    public function getProductSpecialPrices($params): ProductSpecialPriceCollection
    {
        return $this->model->select([
            'currency_code',
            'application_started_on',
            'application_ended_on',
            'unit_price'
        ])
            ->where('delivery_destination_code', $params['delivery_destination_code'])
            ->where('factory_code', $params['factory_code'])
            ->where('product_code', $params['product_code'])
            ->orderBy('currency_code', 'ASC')
            ->orderBy('application_started_on', 'ASC')
            ->get();
    }

    /**
     * 商品特価マスタの登録
     *
     * @param  array $params
     * @return \App\Models\Master\ProductSpecialPrice
     */
    public function create(array $params): ProductSpecialPrice
    {
        return $this->model->create(array_filter($params, 'is_not_null'));
    }

    /**
     * 商品特価マスタの更新
     *
     * @param \App\Models\Master\ProductSpecialPrice $product_special_price
     * @param  array $params
     * @return \App\Models\Master\ProductSpecialPrice $product_special_price
     */
    public function update(ProductSpecialPrice $product_special_price, array $params): ProductSpecialPrice
    {
        $product_special_price->fill(array_filter($params, 'is_not_null'))->save();
        return $product_special_price;
    }
}

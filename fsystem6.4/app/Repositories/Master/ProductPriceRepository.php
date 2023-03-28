<?php

declare(strict_types=1);

namespace App\Repositories\Master;

use App\Models\Master\ProductPrice;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Master\Collections\ProductPriceCollection;

class ProductPriceRepository
{
    /**
     * @var \App\Models\Master\ProductPrice
     */
    private $model;

    /**
     * @param \App\Models\Master\ProductPrice $model
     * @return void
     */
    public function __construct(ProductPrice $model)
    {
        $this->model = $model;
    }

    /**
     * 主キーにより一意のレコードを取得
     * @param  array $params
     */
    public function getProductPrice($params)
    {
        return $this->model
            ->where('factory_code', $params['factory_code'])
            ->where('product_code', $params['product_code'])
            ->where('currency_code', $params['currency_code'])
            ->where('application_started_on', $params['application_started_on'])
            ->first();
    }

    /**
     * 商品価格マスタを条件に応じて検索
     *
     * @param  array $params
     * @return \App\Models\Master\Collections\ProductPriceCollection
     */
    public function getProductPrices($params): ProductPriceCollection
    {
        return $this->model->select([
            'application_started_on',
            'currency_code',
            'unit_price'
        ])
            ->where('factory_code', $params['factory_code'])
            ->where('product_code', $params['product_code'])
            ->orderBy('application_started_on', 'ASC')
            ->orderBy('currency_code', 'ASC')
            ->get();
    }

    /**
     * 商品価格マスタの登録
     *
     * @param  array $params
     * @return \App\Models\Master\ProductPrice
     */
    public function create(array $params): ProductPrice
    {
        return $this->model->create(array_filter($params, 'is_not_null'));
    }

    /**
     * 商品価格マスタの更新
     *
     * @param \App\Models\Master\ProductPrice $product_price
     * @param  array $params
     * @return \App\Models\Master\ProductPrice $product_price
     */
    public function update(ProductPrice $product_price, array $params): ProductPrice
    {
        $product_price->fill(array_filter($params, 'is_not_null'))->save();
        return $product_price;
    }
}

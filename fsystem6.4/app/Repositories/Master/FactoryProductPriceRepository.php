<?php

declare(strict_types=1);

namespace App\Repositories\Master;

use App\Models\Master\FactoryProduct;
use App\Models\Master\FactoryProductPrice;
use App\Models\Master\Collections\FactoryProductPriceCollection;

class FactoryProductPriceRepository
{
    /**
     * @var \App\Models\Master\FactoryProductPrice
     */
    private $model;

    /**
     * @param  \App\Models\Master\FactoryProductPrice $model
     * @return void
     */
    public function __construct(FactoryProductPrice $model)
    {
        $this->model = $model;
    }

    /**
     * 工場商品価格マスタの登録
     *
     * @param  array $params
     * @return \App\Models\Master\FactoryProductPrice
     */
    public function create(array $params): FactoryProductPrice
    {
        return $this->model->create(array_filter($params, 'is_not_null'));
    }

    /**
     * 工場取扱商品に紐づく工場商品価格マスタの削除
     *
     * @param  \App\Models\Master\FactoryProduct $factory_product
     * @return void
     */
    public function deleteFactoryProductPrices(FactoryProduct $factory_product): void
    {
        $factory_product->factory_product_prices->each(function ($fpp) {
            $fpp->delete();
        });
    }

    /**
     * 適用される工場商品価格を取得
     *
     * @param  array $params
     * @return \App\Models\Master\FactoryProductPrice
     */
    public function getAppliedFactoryProductPrice($params): ?FactoryProductPrice
    {
        return $this->model
            ->select([
                'factory_product_prices.unit_price',
                'factory_product_prices.currency_code',
                'factory_products.unit'
            ])
            ->join('factory_products', function ($join) {
                $join->on('factory_product_prices.factory_code', '=', 'factory_products.factory_code')
                    ->on(
                        'factory_product_prices.factory_product_sequence_number',
                        '=',
                        'factory_products.sequence_number'
                    );
            })
            ->where('factory_product_prices.factory_code', $params['factory_code'])
            ->where(
                'factory_product_prices.factory_product_sequence_number',
                $params['factory_product_sequence_number']
            )
            ->where('factory_product_prices.currency_code', $params['currency_code'])
            ->where('factory_product_prices.application_started_on', '<=', $params['date'])
            ->orderBy('factory_product_prices.application_started_on', 'DESC')
            ->first();
    }
}

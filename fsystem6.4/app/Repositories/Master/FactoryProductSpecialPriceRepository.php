<?php

declare(strict_types=1);

namespace App\Repositories\Master;

use App\Models\Master\DeliveryFactoryProduct;
use App\Models\Master\FactoryProductSpecialPrice;
use App\Models\Master\Collections\FactoryProductSpecialPriceCollection;

class FactoryProductSpecialPriceRepository
{
    /**
     * @var \App\Models\Master\FactoryProductSpecialPrice
     */
    private $model;

    /**
     * @param  \App\Models\Master\FactoryProductSpecialPrice $model
     * @return void
     */
    public function __construct(FactoryProductSpecialPrice $model)
    {
        $this->model = $model;
    }

    /**
     * 工場商品特価マスタの登録
     *
     * @param  \App\Models\Master\DeliveryFactoryProduct $delivery_factory_product
     * @param  array $params
     * @return \App\Models\Master\FactoryProductSpecialPrice
     */
    public function create(DeliveryFactoryProduct $delivery_factory_product, array $params): FactoryProductSpecialPrice
    {
        return $this->model->create(array_merge([
            'delivery_destination_code' => $delivery_factory_product->delivery_destination_code,
            'factory_code' => $delivery_factory_product->factory_code,
            'factory_product_sequence_number' => $delivery_factory_product->factory_product_sequence_number
        ], $params));
    }

    /**
     * 工場商品特価マスタの削除
     *
     * @param  \App\Models\Master\DeliveryFactoryProduct $delivery_factory_product
     * @return void
     */
    public function delete(DeliveryFactoryProduct $delivery_factory_product): void
    {
        $this->model
            ->where('delivery_destination_code', $delivery_factory_product->delivery_destination_code)
            ->where('factory_code', $delivery_factory_product->factory_code)
            ->where('factory_product_sequence_number', $delivery_factory_product->factory_product_sequence_number)
            ->delete();
    }

    /**
     * 適用される工場商品特価を取得
     *
     * @param  array $params
     * @return \App\Models\Master\FactoryProductSpecialPrice
     */
    public function getAppliedFactoryProductSpecialPrice($params): ?FactoryProductSpecialPrice
    {
        return $this->model
            ->select([
                'factory_product_special_prices.unit_price',
                'factory_product_special_prices.currency_code',
                'factory_products.unit'
            ])
            ->join('delivery_factory_products', function ($join) {
                $join->on('factory_product_special_prices.factory_code', '=', 'delivery_factory_products.factory_code')
                    ->on(
                        'factory_product_special_prices.delivery_destination_code',
                        '=',
                        'delivery_factory_products.delivery_destination_code'
                    )
                    ->on(
                        'factory_product_special_prices.factory_product_sequence_number',
                        '=',
                        'delivery_factory_products.factory_product_sequence_number'
                    );
            })
            ->join('factory_products', function ($join) {
                $join->on('delivery_factory_products.factory_code', '=', 'factory_products.factory_code')
                    ->on(
                        'delivery_factory_products.factory_product_sequence_number',
                        '=',
                        'factory_products.sequence_number'
                    );
            })
            ->where('factory_product_special_prices.delivery_destination_code', $params['delivery_destination_code'])
            ->where('factory_product_special_prices.factory_code', $params['factory_code'])
            ->where(
                'factory_product_special_prices.factory_product_sequence_number',
                $params['factory_product_sequence_number']
            )
            ->where('factory_product_special_prices.currency_code', $params['currency_code'])
            ->where('factory_product_special_prices.application_started_on', '<=', $params['date'])
            ->where('factory_product_special_prices.application_ended_on', '>=', $params['date'])
            ->orderBy('factory_product_special_prices.application_started_on', 'DESC')
            ->first();
    }
}

<?php

declare(strict_types=1);

namespace App\Repositories\Order;

use App\Models\Order\ReturnedProduct;

class ReturnedProductRepository
{
    /**
     * @var \App\Models\Order\ReturnedProduct
     */
    private $model;

    /**
     * @param  \App\Models\Order\ReturnedProduct $model
     * @return void
     */
    public function __construct(ReturnedProduct $model)
    {
        $this->model = $model;
    }

    /**
     * 返品データの更新
     *
     * @param  \App\Models\Order\ReturnedProduct $returned_product
     * @param  array $params
     * @return \App\Models\Order\ReturnedProduct $returned_product
     */
    public function update(ReturnedProduct $returned_product, array $params): ReturnedProduct
    {
        $returned_product->fill($params)->save();
        return $returned_product;
    }
}

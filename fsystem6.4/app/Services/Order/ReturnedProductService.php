<?php

declare(strict_types=1);

namespace App\Services\Order;

use Illuminate\Database\Connection;
use App\Models\Master\FactoryProduct;
use App\Models\Order\Order;
use App\Models\Order\ReturnedProduct;
use App\Repositories\Order\ReturnedProductRepository;
use App\Repositories\Stock\StockRepository;

class ReturnedProductService
{
    /**
     * @var \Illuminate\Database\Connection
     */
    private $db;

    /**
     * @var \App\Repositories\Order\ReturnedProductRepository
     */
    private $returned_product_repo;

    /**
     * @var \App\Repositories\Stock\StockRepository
     */
    private $stock_repo;

    /**
     * @param  \Illuminate\Database\Connection $db
     * @param  \App\Repositories\Order\ReturnedProductRepository $returned_product_repositry
     * @param  \App\Repositories\Stock\StockRepository $stock_repo
     * @return void
     */
    public function __construct(
        Connection $db,
        ReturnedProductRepository $returned_product_repositry,
        StockRepository $stock_repo
    ) {
        $this->db = $db;
        $this->returned_product_repo = $returned_product_repositry;
        $this->stock_repo = $stock_repo;
    }

    /**
     * 返品商品情報の登録
     *
     * @param  \App\Models\Order\Order $order
     * @param  \App\Models\Master\FactoryProduct $factory_product
     * @param  array $params
     * @return \App\Models\Order\ReturnedProduct
     */
    public function createReturnedProduct(Order $order, FactoryProduct $factory_product, array $params): ReturnedProduct
    {
        return $this->db->transaction(function () use ($order, $factory_product, $params) {
            $returned_product = new ReturnedProduct([
                'order_number' => $order->order_number,
                'returned_on' => $params['returned_on'],
                'product_code' => $factory_product->product_code,
                'factory_product_sequence_number' => $factory_product->sequence_number,
                'unit_price' => $params['unit_price'],
                'quantity' => $params['quantity'],
                'remark' => $params['remark'] ?: ''
            ]);

            $stock = $this->stock_repo->createDefectiveStockByReturnedProdyct($returned_product);
            $returned_product->stock_id = $stock->stock_id;

            $returned_product->save();
            return $returned_product;
        });
    }

    /**
     * 返品データの更新
     *
     * @param  \App\Models\Order\ReturnedProduct $returned_product
     * @param  \App\Models\Master\FactoryProduct $factory_product
     * @param  array $params
     * @return \App\Models\Order\ReturnedProduct
     */
    public function updateReturnedProduct(
        ReturnedProduct $returned_product,
        FactoryProduct $factory_product,
        array $params
    ): ReturnedProduct {
        return $this->returned_product_repo->update($returned_product, [
            'returned_on' => $params['returned_on'],
            'product_code' => $factory_product->product_code,
            'factory_product_sequence_number' => $factory_product->sequence_number,
            'unit_price' => $params['unit_price'],
            'quantity' => $params['quantity'],
            'remark' => $params['remark'] ?: ''
        ]);
    }
}

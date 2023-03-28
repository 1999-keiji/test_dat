<?php

declare(strict_types=1);

namespace App\Repositories\Master;

use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Master\Product;
use App\Models\Master\Collections\ProductCollection;

class ProductRepository
{
    /**
     * @var \App\Models\Master\Product
     */
    private $model;

    /**
     * @param  \App\Models\Master\Product $model
     * @return void
     */
    public function __construct(Product $model)
    {
        $this->model = $model;
    }

    /**
     * すべての商品マスタを取得
     *
     * @return App\Models\Master\Collections\ProductCollection
     */
    public function all(): ProductCollection
    {
        return $this->model->all();
    }

    /**
     * 商品マスタを条件に応じて検索
     *
     * @param  array $params
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function search($params): LengthAwarePaginator
    {
        return $this->model
            ->select([
                'products.product_code',
                'products.species_code',
                'products.creating_type',
                'products.product_name'
            ])
            ->where(function ($query) use ($params) {
                if ($species_code = $params['species_code']) {
                    $query->where('products.species_code', $species_code);
                }
            })
            ->where(function ($query) use ($params) {
                if ($product_code = $params['product_code']) {
                    $query->where('products.product_code', $product_code);
                }
            })
            ->where(function ($query) use ($params) {
                if ($product_name = $params['product_name']) {
                    $query->where('products.product_name', 'LIKE', "%{$product_name}%")
                        ->orWhere('products.result_addup_name', 'LIKE', "%{$product_name}%")
                        ->orWhere('products.result_addup_abbreviation', 'LIKE', "%{$product_name}%");
                }
            })
            ->sortable(['species_code' => 'ASC'])
            ->with('species')
            ->paginate();
    }

    /**
     * 商品コードより一意のレコードを取得
     *
     * @param  string $product_code
     */
    public function getProduct($product_code)
    {
        return $this->model->find($product_code);
    }

    /**
     * 指定された品種に紐づく商品を取得
     *
     * @param  array $params
     * @return \App\Models\Master\Collections\ProductCollection
     */
    public function getProductsBySpecies(array $params): ProductCollection
    {
        return $this->model
            ->select([
                'product_code',
                'product_name'
            ])
            ->where('species_code', $params['species_code'])
            ->orderBy('product_code', 'ASC')
            ->get();
    }

    /**
     * 商品マスタの登録
     *
     * @param  array $params
     * @return \App\Models\Master\Product
     */
    public function create(array $params): Product
    {
        return $this->model->create(array_filter($params, 'is_not_null'));
    }

    /**
     * 商品マスタの更新
     *
     * @param  \App\Models\Master\Product $product
     * @param  array $params
     * @return \App\Models\Master\Product $product
     */
    public function update(Product $product, array $params): Product
    {
        $product->fill(array_filter($params, 'is_not_null'))->save();
        return $product;
    }
}

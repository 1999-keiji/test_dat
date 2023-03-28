<?php

declare(strict_types=1);

namespace App\Repositories\Master;

use Illuminate\Database\Connection;
use App\Models\Master\FactoryProduct;
use App\Models\Master\Collections\FactoryProductCollection;

class FactoryProductRepository
{
    /**
     * @var \Illuminate\Database\Connection
     */
    private $db;

    /**
     * @var \App\Models\Master\FactoryProduct
     */
    private $model;

    /**
     * @param  \Illuminate\Database\Connection $db
     * @param  \App\Models\Master\FactoryProduct $model
     * @return void
     */
    public function __construct(Connection $db, FactoryProduct $model)
    {
        $this->db = $db;
        $this->model = $model;
    }

    /**
     * 工場取扱商品マスタを取得
     *
     * @param  $primary_key
     * @return \App\Models\Master\FactoryProduct
     */
    public function find($primary_key): FactoryProduct
    {
        $query = $this->model->newQuery();
        foreach ($this->model->getKeyName() as $key) {
            $query->where($key, $primary_key[$key]);
        }

        return $query->first();
    }

    /**
     * 指定された工場の工場取扱商品を取得
     *
     * @param  string $factory_code
     * @param  array $order
     * @param  string $species_code
     * @return \App\Models\Master\Collections\FactoryProductCollection
     */
    public function getFactoryProductsByFactoryCode(
        string $factory_code,
        array $order,
        ?string $species_code
    ): FactoryProductCollection {
        $query = $this->model
            ->select([
                'factory_products.factory_code',
                'factory_products.sequence_number',
                'factory_products.product_code',
                'products.product_name',
                'products.species_code',
                'species.species_name',
                'factory_products.factory_product_name',
                'factory_products.factory_product_abbreviation',
                'factory_products.number_of_heads',
                'factory_products.weight_per_number_of_heads',
                'factory_products.input_group',
                'factory_products.number_of_cases',
                'factory_products.unit',
                'factory_products.remark',
                'factory_products.updated_at'
            ])
            ->join('products', 'factory_products.product_code', '=', 'products.product_code')
            ->join('species', 'products.species_code', '=', 'species.species_code')
            ->where('factory_products.factory_code', $factory_code)
            ->where(function ($query) use ($species_code) {
                if ($species_code) {
                    $query->where('products.species_code', $species_code);
                }
            });

        if (array_key_exists('sort', $order) && array_key_exists('order', $order)) {
            $query->orderBy($order['sort'], $order['order']);
        }

        return $query->get();
    }

    /**
     * 工場コードと商品コードで工場取扱商品を取得
     *
     * @param  string $factory_code
     * @param  string $factory_code
     * @return \App\Models\Master\Collections\FactoryProductCollection
     */
    public function getFactoryProductsByFactoryAndProduct(
        string $factory_code,
        string $product_code
    ): FactoryProductCollection {
        return $this->model->select('*')
            ->where('factory_code', $factory_code)
            ->where('product_code', $product_code)
            ->orderBy('sequence_number', 'ASC')
            ->get();
    }

    /**
     * 工場コードと品種コードで工場取扱商品を取得
     *
     * @param  string $factory_code
     * @param  string $factory_code
     * @return \App\Models\Master\Collections\FactoryProductCollection
     */
    public function getFactoryProductsByFactoryAndSpecies(
        string $factory_code,
        string $species_code
    ): FactoryProductCollection {
        return $this->model
            ->select('factory_products.*')
            ->join('products', 'products.product_code', '=', 'factory_products.product_code')
            ->where('factory_products.factory_code', $factory_code)
            ->where('products.species_code', $species_code)
            ->orderBy('factory_products.number_of_heads', 'ASC')
            ->orderBy('factory_products.weight_per_number_of_heads', 'ASC')
            ->orderBy('factory_products.input_group', 'ASC')
            ->orderBy('factory_products.number_of_cases', 'ASC')
            ->orderBy('factory_products.sequence_number', 'ASC')
            ->get();
    }

    /**
     * 製品引当データの件数を工場取扱商品マスタで取得
     *
     * @param  \App\Models\Master\FactoryProduct $factory_product
     * @return int
     */
    public function countProductAllocationsByFactoryProduct(FactoryProduct $factory_product): int
    {
        return $this->db->table('product_allocations')
            ->join('orders', function ($join) use ($factory_product) {
                $join->on('orders.order_number', '=', 'product_allocations.order_number')
                    ->where('orders.factory_code', $factory_product->factory_code)
                    ->where('orders.factory_product_sequence_number', $factory_product->sequence_number);
            })
            ->join('factory_products', function ($join) {
                $join->on('factory_products.factory_code', '=', 'orders.factory_code')
                    ->on('factory_products.sequence_number', '=', 'orders.factory_product_sequence_number');
            })
            ->where('product_allocations.factory_code', $factory_product->factory_code)
            ->where('product_allocations.species_code', $factory_product->product->species_code)
            ->count();
    }

    /**
     * 工場取扱商品マスタの登録
     *
     * @param  array $params
     * @return \App\Models\Master\FactoryProduct
     */
    public function create(array $params): FactoryProduct
    {
        return $this->model->create(array_filter($params, 'is_not_null'));
    }

    /**
     * 工場取扱商品マスタの更新
     *
     * @param  \App\Models\Master\FactoryProduct $factory_product
     * @param  array $params
     * @return \App\Models\Master\FactoryProduct $factory_product
     */
    public function update(FactoryProduct $factory_product, array $params): FactoryProduct
    {
        $factory_product->fill(array_filter($params, 'is_not_null'))->save();
        return $factory_product;
    }
}

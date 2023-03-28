<?php

declare(strict_types=1);

namespace App\Services\Master;

use Illuminate\Database\Connection;
use App\Models\Master\Factory;
use App\Models\Master\FactoryProduct;
use App\Models\Master\FactoryProductPrice;
use App\Models\Master\Collections\FactoryProductCollection;
use App\Repositories\Master\FactoryProductRepository;
use App\Repositories\Master\FactoryProductPriceRepository;

class FactoryProductService
{
    /**
     * @var \Illuminate\Database\Connection
     */
    private $db;

    /**
     * @var \App\Repositories\Master\FactoryProductRepository
     */
    private $factory_product_repo;

    /**
     * @var \App\Repositories\Master\FactoryProductPriceRepository
     */
    private $factory_product_price_repo;

    /**
     * @param  \Illuminate\Database\Connection $db
     * @param  \App\Repositories\Master\FactoryProductRepository $factory_product_repo
     * @param  \App\Repositories\Master\FactoryProductPriceRepository $factory_product_price_repo
     * @return void
     */
    public function __construct(
        Connection $db,
        FactoryProductRepository $factory_product_repo,
        FactoryProductPriceRepository $factory_product_price_repo
    ) {
        $this->db = $db;
        $this->factory_product_repo = $factory_product_repo;
        $this->factory_product_price_repo = $factory_product_price_repo;
    }

    /**
     * 指定された工場取扱商品マスタを取得
     *
     * @param  array $primary_key
     * @return \App\Models\Master\FactoryProduct
     */
    public function findFactoryProduct(array $primary_key): FactoryProduct
    {
        if (isset($primary_key['factory_product_sequence_number']) && ! isset($primary_key['sequence_number'])) {
            $primary_key['sequence_number'] = $primary_key['factory_product_sequence_number'];
        }

        return $this->factory_product_repo->find($primary_key);
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
        ?string $species_code = ''
    ): FactoryProductCollection {
        return $this->factory_product_repo->getFactoryProductsByFactoryCode($factory_code, $order, $species_code);
    }

    /**
     * API用に工場取扱商品マスタを検索
     *
     * @param  array $params
     * @return array
     */
    public function getFactoryProductsForSearchingApi(array $params): array
    {
        $factory_code = $params['factory_code'] ?? null;
        $species_code = $params['species_code'] ?? null;

        return $this->getFactoryProductsByFactoryCode($factory_code, [], $species_code)
            ->toResponseForSearchingApi();
    }

    /**
     * API用 適用される工場商品価格を取得
     *
     * @param  array $params
     * @return \App\Models\Master\FactoryProductPrice
     */
    public function getAppliedFactoryProductPrice(array $params): ?FactoryProductPrice
    {
        $params = [
            'delivery_destination_code' => $params['delivery_destination_code'] ?? null,
            'factory_code' => $params['factory_code'] ?? null,
            'factory_product_sequence_number' => $params['factory_product_sequence_number'] ?? null,
            'currency_code' => $params['currency_code'] ?? null,
            'date' => $params['date'] ?? null
        ];

        return $this->factory_product_price_repo->getAppliedFactoryProductPrice($params);
    }

    /**
     * 修正可能な工場取扱商品マスタかどうか判定する
     *
     * @param  \App\Models\Master\FactoryProduct $factory_product
     * @param  array $params
     * @return bool
     */
    public function isUpdatableFactoryProduct(FactoryProduct $factory_product, array $params): bool
    {
        if ((float)$factory_product->number_of_heads === (float)$params['number_of_heads'] &&
            $factory_product->weight_per_number_of_heads === (int)$params['weight_per_number_of_heads'] &&
            $factory_product->input_group === $params['input_group'] &&
            $factory_product->number_of_cases === (int)$params['number_of_cases']) {
            return true;
        }

        return $this->factory_product_repo->countProductAllocationsByFactoryProduct($factory_product) === 0;
    }

    /**
     * 工場取扱商品マスタ登録
     *
     * @param  \App\Models\Master\Factory $factory
     * @param  array $params
     * @return \App\Models\Master\FactoryProduct
     */
    public function createFactoryProduct(Factory $factory, array $params): FactoryProduct
    {
        return $this->db->transaction(function () use ($factory, $params) {
            $factory_product = $this->factory_product_repo->create([
                'factory_code' => $factory->factory_code,
                'sequence_number' => ($factory->factory_products->max('sequence_number') ?: 0) + 1,
                'product_code' => $params['product_code'],
                'factory_product_name' => $params['factory_product_name'],
                'factory_product_abbreviation' => $params['factory_product_abbreviation'],
                'number_of_heads' => $params['number_of_heads'],
                'weight_per_number_of_heads' => $params['weight_per_number_of_heads'],
                'input_group' => $params['input_group'],
                'number_of_cases' => $params['number_of_cases'],
                'unit' => $params['unit'],
                'can_be_transported_double' => $params['can_be_transported_double'] ?? false,
                'remark' => $params['remark'] ?: ''
            ]);

            foreach ($params['factory_product_prices'] as $fpp) {
                $this->factory_product_price_repo->create([
                    'factory_code' => $factory->factory_code,
                    'factory_product_sequence_number' => $factory_product->sequence_number,
                    'currency_code' => $fpp['currency_code'],
                    'application_started_on' => $fpp['application_started_on'],
                    'unit_price' => $fpp['unit_price'],
                    'cost' => $fpp['cost']
                ]);
            }

            return $factory_product;
        });
    }

    /**
     * 工場取扱商品マスタの更新
     *
     * @param  \App\Models\Master\FactoryProduct $factory_product
     * @param  array $params
     * @return \App\Models\Master\FactoryProduct
     */
    public function updateFactoryProduct(FactoryProduct $factory_product, array $params): FactoryProduct
    {
        return $this->db->transaction(function () use ($factory_product, $params) {
            $factory_product = $this->factory_product_repo->update($factory_product, [
                'factory_product_name' => $params['factory_product_name'],
                'factory_product_abbreviation' => $params['factory_product_abbreviation'],
                'number_of_heads' => $params['number_of_heads'],
                'weight_per_number_of_heads' => $params['weight_per_number_of_heads'],
                'input_group' => $params['input_group'],
                'number_of_cases' => $params['number_of_cases'],
                'unit' => $params['unit'],
                'can_be_transported_double' => $params['can_be_transported_double'] ?? false,
                'remark' => $params['remark'] ?: ''
            ]);

            $this->factory_product_price_repo->deleteFactoryProductPrices($factory_product);
            foreach ($params['factory_product_prices'] as $fpp) {
                $this->factory_product_price_repo->create([
                    'factory_code' => $factory_product->factory_code,
                    'factory_product_sequence_number' => $factory_product->sequence_number,
                    'currency_code' => $fpp['currency_code'],
                    'application_started_on' => $fpp['application_started_on'],
                    'unit_price' => $fpp['unit_price'],
                    'cost' => $fpp['cost']
                ]);
            }

            return $factory_product;
        });
    }

    /**
     * 工場取扱商品マスタの削除
     *
     * @param  \App\Models\Master\FactoryProduct $factory_product
     * @return void
     */
    public function deleteFactoryProduct(FactoryProduct $factory_product): void
    {
        $this->db->transaction(function () use ($factory_product) {
            $this->factory_product_price_repo->deleteFactoryProductPrices($factory_product);
            $factory_product->delete();
        });
    }
}

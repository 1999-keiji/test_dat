<?php

declare(strict_types=1);

namespace App\Services\Master;

use Illuminate\Database\Connection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Exceptions\PageOverException;
use App\Models\Master\DeliveryDestination;
use App\Models\Master\DeliveryFactoryProduct;
use App\Models\Master\FactoryProduct;
use App\Models\Master\FactoryProductSpecialPrice;
use App\Models\Master\Collections\DeliveryFactoryProductCollection;
use App\Models\Order\Collections\OrderCollection;
use App\Repositories\Master\DeliveryFactoryProductRepository;
use App\Repositories\Master\FactoryProductSpecialPriceRepository;
use App\ValueObjects\Date\ApplicationStartedOn;
use App\ValueObjects\Enum\OutputCondition;

class DeliveryFactoryProductService
{
    /**
     * @var \Illuminate\Database\Connection
     */
    private $db;

    /**
     * @var \App\ValueObjects\Date\ApplicationStartedOn
     */
    private $application_started_on;

    /**
     * @var \App\Repositories\Master\DeliveryFactoryProductRepository
     */
    private $delivery_factory_product_repo;

    /**
     * @var \App\Repositories\Master\FactoryProductSpecialPriceRepository
     */
    private $factory_product_special_price_repo;

    /**
     * @param  \Illuminate\Database\Connection $db
     * @param  \App\ValueObjects\Date\ApplicationStartedOn $application_started_on
     * @param  \App\Repositories\Master\DeliveryFactoryProductRepository $delivery_factory_product_repositry
     * @param  \App\Repositories\Master\FactoryProductSpecialPriceRepository $factory_product_special_price_repo
     * @return void
     */
    public function __construct(
        Connection $db,
        FactoryProductService $factory_product_service,
        ApplicationStartedOn $application_started_on,
        DeliveryFactoryProductRepository $delivery_factory_product_repo,
        FactoryProductSpecialPriceRepository $factory_product_special_price_repo
    ) {
        $this->db = $db;
        $this->application_started_on = $application_started_on;
        $this->delivery_factory_product_repo = $delivery_factory_product_repo;
        $this->factory_product_special_price_repo = $factory_product_special_price_repo;
    }

    /**
     * 納入先に紐づく工場取扱商品マスタを取得
     *
     * @param  \App\Models\Master\DeliveryDestination $delivery_destination
     * @param  int $page
     * @return \Illuminate\Pagination\LengthAwarePaginator
     * @throws \App\Exceptions\PageOverException
     */
    public function getFactoryProductsByDeliveryDestination(
        DeliveryDestination $delivery_destination,
        int $page
    ): LengthAwarePaginator {
        $delivery_factory_products = $this->delivery_factory_product_repo
            ->getFactoryProductsByDeliveryDestination($delivery_destination);
        if ($page > $delivery_factory_products->lastPage() && $delivery_factory_products->lastPage() !== 0) {
            throw new PageOverException('target page not exists.');
        }

        return $delivery_factory_products;
    }

    /**
     * 適用期間が重複していないことを判定
     *
     * @param  array $params
     * @return bool
     */
    public function isNotOverlappedApplicationTerm(array $params): bool
    {
        $terms = [];
        foreach ($params['application_started_on'] ?? [] as $idx => $application_started_on) {
            $terms[] = [
                'application_started_on' => $application_started_on,
                'application_ended_on' => $params['application_ended_on'][$idx]
            ];
        }

        return $this->application_started_on->isNotOverlapped($terms);
    }

    /**
     * 納入先に対して工場商品マスタを紐づけることが可能か判定
     *
     * @param  \App\Models\Master\DeliveryDestination $delivery_destination
     * @param  \App\Models\Master\FactoryProduct $factory_product
     * @return bool
     */
    public function canLinkFactoryProduct(
        DeliveryDestination $delivery_destination,
        FactoryProduct $factory_product
    ): bool {
        $linked_product_code_list = $delivery_destination
            ->delivery_factory_products
            ->filterByFactory($factory_product->factory_code)
            ->pluckProductCode();

        return ! in_array($factory_product->product_code, $linked_product_code_list, true);
    }

    /**
     * 工場と品種を指定して、納入工場商品を工場商品別に集約して取得
     *
     * @param  array $params
     * @return array
     */
    public function getGroupedDeliveryFactoryProductsPerProduct(array $params): array
    {
        return $this->delivery_factory_product_repo
            ->getDeliveryFactoryProductsByFactoryAndSpecies($params)
            ->groupByFactoryProduct();
    }

    /**
     * 工場と品種を指定して、納入工場商品を規格別に集約して取得
     *
     * @param  array $params
     * @return array
     */
    public function getGroupedDeliveryFactoryProductsPerPackagingStyle(
        array $params,
        ?OrderCollection $orders = null
    ): array {
        $delivery_factory_products = $this
            ->delivery_factory_product_repo->getDeliveryFactoryProductsByFactoryAndSpecies($params);

        $output_condition = (int)($params['output_condition'] ?? OutputCondition::ALL);
        if (! is_null($orders) && $output_condition === OutputCondition::WITH_ORDERS) {
            $delivery_factory_products = $delivery_factory_products->filterByOrders($orders);
        }

        return $delivery_factory_products->groupByPackagingStyle();
    }

    /**
     * 納入先と工場を指定して納入工場商品を取得
     *
     * @param  array
     * @return array
     */
    public function getDeliveryFactoryProductsByDeliveryDestinationAndFactory(array $params): array
    {
        $params = [
            'factory_code' => $params['factory_code'] ?? null,
            'delivery_destination_code' => $params['delivery_destination_code'] ?? null
        ];

        return $this->delivery_factory_product_repo
            ->getDeliveryFactoryProductsByDeliveryDestinationAndFactory($params)
            ->all();
    }

    /**
     * API用 適用される工場商品特価を取得
     *
     * @param  array $params
     * @return \App\Models\Master\FactoryProductSpecialPrice
     */
    public function getAppliedFactoryProductSpecialPrice(array $params): ?FactoryProductSpecialPrice
    {
        $params = [
            'delivery_destination_code' => $params['delivery_destination_code'] ?? null,
            'factory_code' => $params['factory_code'] ?? null,
            'factory_product_sequence_number' => $params['factory_product_sequence_number'] ?? null,
            'currency_code' => $params['currency_code'] ?? null,
            'date' => $params['date'] ?? null
        ];

        return $this->factory_product_special_price_repo->getAppliedFactoryProductSpecialPrice($params);
    }

    /**
     * 納入工場商品マスタの登録
     *
     * @param  array $params
     * @return \App\Models\Master\DeliveryFactoryProduct $delivery_factory_product
     */
    public function createDeliveryFactoryProduct(array $params): DeliveryFactoryProduct
    {
        return $this->db->transaction(function () use ($params) {
            $delivery_factory_product = $this->delivery_factory_product_repo->create($params);
            foreach ($params['currency_code'] ?? [] as $idx => $currency_code) {
                $this->factory_product_special_price_repo->create($delivery_factory_product, [
                    'currency_code' => $currency_code,
                    'application_started_on' => $params['application_started_on'][$idx],
                    'application_ended_on' => $params['application_ended_on'][$idx],
                    'unit_price' => $params['unit_price'][$idx]
                ]);
            }

            return $delivery_factory_product;
        });
    }

    /**
     * 納入工場商品マスタの更新
     *
     * @param  \App\Models\Master\DeliveryFactoryProduct $delivery_factory_product
     * @param  array $params
     * @return void
     */
    public function updateDeliveryFactoryProduct(DeliveryFactoryProduct $delivery_factory_product, array $params): void
    {
        $this->db->transaction(function () use ($delivery_factory_product, $params) {
            $this->factory_product_special_price_repo->delete($delivery_factory_product);
            foreach ($params['currency_code'] ?? [] as $idx => $currency_code) {
                $this->factory_product_special_price_repo->create($delivery_factory_product, [
                    'currency_code' => $currency_code,
                    'application_started_on' => $params['application_started_on'][$idx],
                    'application_ended_on' => $params['application_ended_on'][$idx],
                    'unit_price' => $params['unit_price'][$idx]
                ]);
            }
        });
    }

    /**
     * 納入工場商品マスタの削除
     *
     * @param  \App\Models\Master\DeliveryFactoryProduct $delivery_factory_product
     * @return void
     */
    public function deleteDeliveryFactoryProduct(DeliveryFactoryProduct $delivery_factory_product): void
    {
        $this->db->transaction(function () use ($delivery_factory_product) {
            $this->factory_product_special_price_repo->delete($delivery_factory_product);
            $delivery_factory_product->delete();
        });
    }
}

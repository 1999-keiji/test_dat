<?php

declare(strict_types=1);

namespace App\Repositories\Master;

use Illuminate\Database\Connection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Exceptions\DataLinkException;
use App\Models\Master\DeliveryDestination;
use App\Models\Master\DeliveryFactoryProduct;
use App\Models\Master\FactoryProduct;
use App\Models\Master\Collections\DeliveryFactoryProductCollection;
use App\ValueObjects\Enum\ProcessClass;
use App\ValueObjects\Enum\SlipStatusType;
use App\ValueObjects\Enum\SlipType;

class DeliveryFactoryProductRepository
{
    /**
     * @var \Illuminate\Database\Connection
     */
    private $db;

    /**
     * @var \App\Models\Master\DeliveryFactoryProduct
     */
    private $model;

    /**
     * @var \App\Repositories\Master\FactoryProductRepository
     */
    private $factory_product_repo;

    /**
     * @param  \Illuminate\Database\Connection $db
     * @param  \App\Models\Master\DeliveryFactoryProduct $model
     * @param  \App\Repositories\Master\FactoryProductRepository $factory_product_repo
     * @return void
     */
    public function __construct(
        Connection $db,
        DeliveryFactoryProduct $model,
        FactoryProductRepository $factory_product_repo
    ) {
        $this->db = $db;
        $this->model = $model;
        $this->factory_product_repo = $factory_product_repo;
    }

    /**
     * 納入先に紐づく納入工場商品マスタを取得
     *
     * @param  \App\Models\Master\DeliveryDestination $delivery_destination
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getFactoryProductsByDeliveryDestination(
        DeliveryDestination $delivery_destination
    ): LengthAwarePaginator {
        return $this->model
            ->select([
                'delivery_factory_products.delivery_destination_code',
                'delivery_factory_products.factory_code',
                'factories.factory_name',
                'factories.factory_abbreviation',
                'delivery_factory_products.factory_product_sequence_number',
                'factory_products.product_code',
                'products.product_name',
                'factory_products.factory_product_name',
                'factory_products.factory_product_abbreviation'
            ])
            ->join('factory_products', function ($join) {
                $join->on('delivery_factory_products.factory_code', '=', 'factory_products.factory_code')
                    ->on(
                        'delivery_factory_products.factory_product_sequence_number',
                        '=',
                        'factory_products.sequence_number'
                    );
            })
            ->join('factories', 'factory_products.factory_code', '=', 'factories.factory_code')
            ->join('products', 'factory_products.product_code', '=', 'products.product_code')
            ->where(
                'delivery_factory_products.delivery_destination_code',
                $delivery_destination->delivery_destination_code
            )
            ->affiliatedFactories('factories')
            ->paginate();
    }

    /**
     * 工場商品に紐づく納入先マスタを取得
     *
     * @param  \App\Models\Master\FactoryProduct $factory_product
     * @return \App\Models\Master\Collections\DeliveryFactoryProductCollection
     */
    public function getDeliveryDestinationsByFactoryProduct(
        FactoryProduct $factory_product
    ): DeliveryFactoryProductCollection {
        $warehouse_query = $this->db->table('delivery_warehouses')
            ->select(['delivery_destination_code', 'warehouse_code', 'delivery_lead_time', 'shipment_lead_time'])
            ->where('warehouse_code', function ($query) use ($factory_product) {
                $query->select('warehouse_code')
                    ->from('factory_warehouses')
                    ->where('factory_code', $factory_product->factory_code)
                    ->orderBy('priority', 'ASC')
                    ->limit(1);
            });

        $order_query = $this->db->table('orders')
            ->select([
                'delivery_destination_code',
                'factory_code',
                'factory_product_sequence_number',
                $this->db->raw('MAX(delivery_date) AS latest_delivery_date')
            ])
            ->where('factory_code', $factory_product->factory_code)
            ->where('process_class', '<>', ProcessClass::CANCEL_PROCESS)
            ->where('slip_type', SlipType::NORMAL_SLIP)
            ->where('slip_status_type', '<>', SlipStatusType::RELATION_TEMP_ORDER)
            ->where('factory_cancel_flag', false)
            ->groupBy('delivery_destination_code')
            ->groupBy('factory_code')
            ->groupBy('factory_product_sequence_number');

        return $this->model
            ->select([
                'delivery_factory_products.delivery_destination_code',
                'delivery_destinations.delivery_destination_name',
                'delivery_destinations.delivery_destination_abbreviation',
                'delivery_factory_products.factory_code',
                'delivery_factory_products.factory_product_sequence_number',
                'delivery_warehouses.shipment_lead_time',
                'orders.latest_delivery_date'
            ])
            ->join(
                'delivery_destinations',
                'delivery_destinations.delivery_destination_code',
                '=',
                'delivery_factory_products.delivery_destination_code'
            )
            ->leftJoin(
                $this->db->raw("({$warehouse_query->toSql()}) AS delivery_warehouses"),
                'delivery_warehouses.delivery_destination_code',
                '=',
                'delivery_destinations.delivery_destination_code'
            )
            ->leftJoin(
                $this->db->raw("({$order_query->toSql()}) AS orders"),
                function ($join) {
                    $join->on(
                        'orders.delivery_destination_code',
                        '=',
                        'delivery_factory_products.delivery_destination_code'
                    )
                    ->on('orders.factory_code', '=', 'delivery_factory_products.factory_code')
                    ->on(
                        'orders.factory_product_sequence_number',
                        '=',
                        'delivery_factory_products.factory_product_sequence_number'
                    );
                }
            )
            ->setBindings(array_merge($warehouse_query->getBindings(), $order_query->getBindings()))
            ->where('delivery_factory_products.factory_code', $factory_product->factory_code)
            ->where('delivery_factory_products.factory_product_sequence_number', $factory_product->sequence_number)
            ->where('delivery_destinations.can_display', true)
            ->orderBy('delivery_factory_products.delivery_destination_code')
            ->get();
    }

    /**
     * 指定された納入先と工場に紐づく工場商品マスタを取得
     *
     * @param  array $params
     * @return \App\Models\Master\Collections\DeliveryFactoryProductCollection
     */
    public function getDeliveryFactoryProductsByDeliveryDestinationAndFactory(
        array $params
    ): DeliveryFactoryProductCollection {
        return $this->model
            ->select([
                'delivery_factory_products.factory_code',
                'delivery_factory_products.factory_product_sequence_number',
                'factory_products.product_code',
                'products.product_name',
                'factory_products.factory_product_name',
                'factory_products.factory_product_abbreviation',
                'factory_products.unit',
            ])
            ->join('factory_products', function ($join) {
                $join->on('delivery_factory_products.factory_code', '=', 'factory_products.factory_code')
                    ->on(
                        'delivery_factory_products.factory_product_sequence_number',
                        '=',
                        'factory_products.sequence_number'
                    );
            })
            ->join('products', 'factory_products.product_code', '=', 'products.product_code')
            ->where('delivery_factory_products.delivery_destination_code', $params['delivery_destination_code'])
            ->where('delivery_factory_products.factory_code', $params['factory_code'])
            ->orderBy('delivery_factory_products.factory_product_sequence_number', 'ASC')
            ->get();
    }

    /**
     * 納入先コードと工場コードと商品コードで納入工場商品を取得
     *
     * @param  string $delivery_destination_code
     * @param  string $factory_code
     * @param  string $product_code
     * @return \App\Models\Master\Collections\DeliveryFactoryProductCollection
     */
    public function getDeliveryFactoryProductsByDeliveryDestinationAndProduct(
        string $delivery_destination_code,
        string $factory_code,
        string $product_code
    ): DeliveryFactoryProductCollection {
        return $this->model
            ->select([
                'delivery_factory_products.*',
                'factory_products.weight_per_number_of_heads',
                'factory_products.number_of_cases'
            ])
            ->join('factory_products', function ($join) {
                $join->on('factory_products.factory_code', '=', 'delivery_factory_products.factory_code')
                    ->on(
                        'factory_products.sequence_number',
                        '=',
                        'delivery_factory_products.factory_product_sequence_number'
                    );
            })
            ->where('delivery_factory_products.delivery_destination_code', $delivery_destination_code)
            ->where('delivery_factory_products.factory_code', $factory_code)
            ->where('factory_products.product_code', $product_code)
            ->orderBy('delivery_factory_products.factory_product_sequence_number', 'ASC')
            ->get();
    }

    /**
     * 工場と品種を指定して、最新の納入日とともに納入工場商品を取得
     *
     * @param  array $params
     * @return \App\Models\Master\Collections\DeliveryFactoryProductCollection
     */
    public function getDeliveryFactoryProductsByFactoryAndSpecies(array $params): DeliveryFactoryProductCollection
    {
        $warehouse_query = $this->db->table('delivery_warehouses')
            ->select(['delivery_destination_code', 'warehouse_code', 'delivery_lead_time', 'shipment_lead_time'])
            ->where('warehouse_code', function ($query) use ($params) {
                $query->select('warehouse_code')
                    ->from('factory_warehouses')
                    ->where('factory_code', $params['factory_code'])
                    ->orderBy('priority', 'ASC')
                    ->limit(1);
            });

        $order_query = $this->db->table('orders')
            ->select([
                'delivery_destination_code',
                'factory_code',
                'factory_product_sequence_number',
                $this->db->raw('MAX(delivery_date) AS latest_delivery_date')
            ])
            ->where('factory_code', $params['factory_code'])
            ->where('process_class', '<>', ProcessClass::CANCEL_PROCESS)
            ->where('slip_type', SlipType::NORMAL_SLIP)
            ->where('slip_status_type', '<>', SlipStatusType::RELATION_TEMP_ORDER)
            ->where('factory_cancel_flag', false)
            ->groupBy('delivery_destination_code')
            ->groupBy('factory_code')
            ->groupBy('factory_product_sequence_number');

        return $this->model
            ->select([
                'delivery_factory_products.delivery_destination_code',
                'delivery_destinations.delivery_destination_abbreviation',
                'delivery_factory_products.factory_code',
                'delivery_factory_products.factory_product_sequence_number',
                'factory_products.factory_product_abbreviation',
                'factory_products.number_of_heads',
                'factory_products.weight_per_number_of_heads',
                'factory_products.input_group',
                'factory_products.number_of_cases',
                'delivery_destinations.transport_company_code',
                'transport_companies.transport_company_abbreviation',
                'transport_companies.note',
                'delivery_destinations.collection_time_sequence_number',
                'collection_time.collection_time',
                'delivery_warehouses.warehouse_code',
                'delivery_warehouses.delivery_lead_time',
                'delivery_warehouses.shipment_lead_time',
                'orders.latest_delivery_date'
            ])
            ->join(
                'delivery_destinations',
                'delivery_destinations.delivery_destination_code',
                '=',
                'delivery_factory_products.delivery_destination_code'
            )
            ->join('factory_products', function ($join) {
                $join
                    ->on(
                        'factory_products.factory_code',
                        '=',
                        'delivery_factory_products.factory_code'
                    )
                    ->on(
                        'factory_products.sequence_number',
                        '=',
                        'delivery_factory_products.factory_product_sequence_number'
                    );
            })
            ->join('products', 'products.product_code', '=', 'factory_products.product_code')
            ->leftJoin(
                'transport_companies',
                'transport_companies.transport_company_code',
                '=',
                'delivery_destinations.transport_company_code'
            )
            ->leftJoin('collection_time', function ($join) {
                $join
                    ->on(
                        'collection_time.transport_company_code',
                        '=',
                        'delivery_destinations.transport_company_code'
                    )
                    ->on(
                        'collection_time.sequence_number',
                        '=',
                        'delivery_destinations.collection_time_sequence_number'
                    );
            })
            ->leftJoin(
                $this->db->raw("({$warehouse_query->toSql()}) AS delivery_warehouses"),
                'delivery_warehouses.delivery_destination_code',
                '=',
                'delivery_destinations.delivery_destination_code'
            )
            ->leftJoin(
                $this->db->raw("({$order_query->toSql()}) AS orders"),
                function ($join) {
                    $join->on(
                        'orders.delivery_destination_code',
                        '=',
                        'delivery_factory_products.delivery_destination_code'
                    )
                    ->on('orders.factory_code', '=', 'delivery_factory_products.factory_code')
                    ->on(
                        'orders.factory_product_sequence_number',
                        '=',
                        'delivery_factory_products.factory_product_sequence_number'
                    );
                }
            )
            ->setBindings(array_merge($warehouse_query->getBindings(), $order_query->getBindings()))
            ->where('delivery_factory_products.factory_code', $params['factory_code'])
            ->where('products.species_code', $params['species_code'])
            ->where(function ($query) use ($params) {
                if ($delivery_destination_code = $params['delivery_destination_code'] ?? null) {
                    $query->where(
                        'delivery_factory_products.delivery_destination_code',
                        $delivery_destination_code
                    );
                }
            })
            ->where(function ($query) use ($params) {
                if ($factory_product_sequence_number = $params['factory_product_sequence_number'] ?? null) {
                    $query->where(
                        'delivery_factory_products.factory_product_sequence_number',
                        $factory_product_sequence_number
                    );
                }
            })
            ->where('delivery_destinations.can_display', true)
            ->orderBy('factory_products.number_of_heads', 'ASC')
            ->orderBy('factory_products.weight_per_number_of_heads', 'ASC')
            ->orderBy('factory_products.input_group', 'ASC')
            ->orderBy('factory_products.number_of_cases', 'ASC')
            ->orderBy('factory_products.sequence_number', 'ASC')
            ->orderBy('delivery_factory_products.delivery_destination_code', 'ASC')
            ->get();
    }

    /**
     * 注文書で指定された工場納入商品を取得
     *
     * @param  array $params
     * @return \App\Models\Master\DeliveryFactoryProduct
     */
    public function getPurchasedDeliveryFactoryProduct(array $params): ?DeliveryFactoryProduct
    {
        return $this->model
            ->select([
                'delivery_factory_products.delivery_destination_code',
                'delivery_factory_products.factory_code',
                'delivery_factory_products.factory_product_sequence_number',
                'factory_products.product_code',
                'products.product_name',
                'factory_products.weight_per_number_of_heads',
                'factory_products.number_of_cases',
                'factory_products.unit'
            ])
            ->join('factory_products', function ($join) {
                $join->on('factory_products.factory_code', '=', 'delivery_factory_products.factory_code')
                    ->on(
                        'factory_products.sequence_number',
                        '=',
                        'delivery_factory_products.factory_product_sequence_number'
                    );
            })
            ->join('products', 'products.product_code', '=', 'factory_products.product_code')
            ->where('delivery_factory_products.delivery_destination_code', $params['delivery_destination_code'])
            ->where('delivery_factory_products.factory_code', $params['factory_code'])
            ->where(function ($query) use ($params) {
                if ($params['product_code'] !== '') {
                    $query->where('product_code', $params['product_code']);
                }
                if ($params['product_code'] === '') {
                    $query->where('product_name', $params['product_name']);
                }
            })
            ->first();
    }

    /**
     * 納入工場商品マスタの登録
     *
     * @param  array $params
     * @return \App\Models\Master\DeliveryFactoryProduct
     */
    public function create(array $params): DeliveryFactoryProduct
    {
        return $this->model->create([
            'delivery_destination_code' => $params['delivery_destination_code'],
            'factory_code' => $params['factory_code'],
            'factory_product_sequence_number' => $params['factory_product_sequence_number']
        ]);
    }

    /**
     * 納入先と工場商品を自動で紐づけ
     *
     * @param  string $delivery_destination_code
     * @param  string $factory_code
     * @param  string $product_code
     * @return \App\Models\Master\DeliveryFactoryProduct
     * @throws \App\Exceptions\DataLinkException
     */
    public function linkDeliveryDestinationToFactoryProductAutomatically(
        string $delivery_destination_code,
        string $factory_code,
        string $product_code
    ): DeliveryFactoryProduct {
        $factory_product = $this->factory_product_repo
            ->getFactoryProductsByFactoryAndProduct($factory_code, $product_code)
            ->first();

        if (is_null($factory_product)) {
            $message = 'factory product does not exist. factory code: %s, product code: %s';
            throw new DataLinkException(sprintf($message, $factory_code, $product_code));
        }

        $delivery_factory_product = $this->create([
            'delivery_destination_code' => $delivery_destination_code,
            'factory_code' => $factory_product->factory_code,
            'factory_product_sequence_number' => $factory_product->sequence_number
        ]);

        $delivery_factory_product->weight_per_number_of_heads = $factory_product->weight_per_number_of_heads;
        $delivery_factory_product->number_of_cases = $factory_product->number_of_cases;

        return $delivery_factory_product;
    }
}

<?php

declare(strict_types=1);

namespace App\Repositories\Order;

use InvalidArgumentException;
use Illuminate\Auth\AuthManager;
use Illuminate\Database\Connection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Model;
use App\Models\Master\Customer;
use App\Models\Master\DeliveryFactoryProduct;
use App\Models\Master\EndUser;
use App\Models\Master\Factory;
use App\Models\Master\Species;
use App\Models\Master\Warehouse;
use App\Models\Order\Order;
use App\Models\Order\Collections\OrderCollection;
use App\Models\Shipment\Invoice;
use App\Models\Stock\StocktakingDetail;
use App\ValueObjects\Date\Date;
use App\ValueObjects\Date\DeliveryDate;
use App\ValueObjects\Date\ShippingDate;
use App\ValueObjects\Date\HarvestingDate;
use App\ValueObjects\Enum\AllocationStatus;
use App\ValueObjects\Enum\CreatingType;
use App\ValueObjects\Enum\FsystemStatementOfDeliveryOutputClass;
use App\ValueObjects\Enum\OutputFile;
use App\ValueObjects\Enum\PrintState;
use App\ValueObjects\Enum\ProcessClass;
use App\ValueObjects\Enum\RelatedOrderStatusType;
use App\ValueObjects\Enum\ShipmentStatus;
use App\ValueObjects\Enum\SlipStatusType;
use App\ValueObjects\Enum\SlipType;
use App\ValueObjects\Enum\StatementOfShipmentOutputClass;

class OrderRepository
{
    /**
     * @var \Illuminate\Auth\AuthManager
     */
    private $auth;

    /**
     * @var \Illuminate\Database\Connection
     */
    private $db;

    /**
     * @var \App\Models\Order\Order
     */
    private $model;

    /**
     * @var \App\Models\Master\EndUser
     */
    private $end_user;

    /**
     * @param  \Illuminate\Auth\AuthManager $auth
     * @param  \Illuminate\Database\Connection $db
     * @param  \App\Models\Order\Order $model
     * @param  \App\Models\Master\EndUser $end_user
     * @return void
     */
    public function __construct(
        AuthManager $auth,
        Connection $db,
        Order $model,
        EndUser $end_user
    ) {
        $this->auth = $auth;
        $this->db = $db;
        $this->model = $model;
        $this->end_user = $end_user;
    }

    /**
     * 注文情報の取得
     *
     * @param  string $order_number
     * @return \App\Models\Order\Order
     */
    public function find(string $order_number): Order
    {
        return $this->model->find($order_number);
    }

    /**
     * 注文情報の登録
     *
     * @param  array $params
     * @return \App\Models\Order\Order
     */
    public function create(array $params): Order
    {
        return $this->model->create(array_filter($params, 'is_not_null'));
    }

    /**
     * 注文情報の更新
     *
     * @param  \App\Models\Order\Order $order
     * @param  array $params
     * @return \App\Models\Order\Order $order
     */
    public function update(Order $order, array $params): Order
    {
        $order->fill(array_filter($params, 'is_not_null'))->save();
        return $order;
    }

    /**
     * 手動で登録された注文データの取得
     *
     * @param  array $params
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function searchManualCreatedOrders(array $params): LengthAwarePaginator
    {
        return $this->model
            ->select([
                'orders.*',
                $this->db->raw("DATE_FORMAT(orders.delivery_date, '%Y/%m/%d') as delivery_date"),
                'delivery_destinations.delivery_destination_abbreviation',
                'products.product_name',
                'currencies.order_unit_decimals',
                'currencies.order_amount_decimals'
            ])
            ->join(
                'delivery_destinations',
                'delivery_destinations.delivery_destination_code',
                '=',
                'orders.delivery_destination_code'
            )
            ->join('products', 'products.product_code', '=', 'orders.product_code')
            ->join('currencies', 'currencies.currency_code', '=', 'orders.currency_code')
            ->where('orders.factory_code', $params['factory_code'])
            ->where('orders.received_date', $params['received_date'])
            ->where('orders.customer_code', $params['customer_code'])
            ->where('orders.end_user_code', $params['end_user_code'])
            ->where('orders.delivery_destination_code', $params['delivery_destination_code'])
            ->where(function ($query) use ($params) {
                if ($factory_product_sequence_number = $params['factory_product_sequence_number'] ?? null) {
                    $query->where('orders.factory_product_sequence_number', $factory_product_sequence_number);
                }
            })
            ->where('orders.creating_type', CreatingType::MANUAL_CREATED)
            ->where('orders.slip_type', SlipType::NORMAL_SLIP)
            ->where('orders.slip_status_type', SlipStatusType::TEMP_ORDER)
            ->where('orders.process_class', '<>', ProcessClass::CANCEL_PROCESS)
            ->orderBy('orders.delivery_date', 'ASC')
            ->orderBy('orders.order_number', 'ASC')
            ->paginate();
    }

    /**
     * 注文一覧 検索
     *
     * @param  array $params
     * @param  array $order
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function searchOrders(array $params, array $order): LengthAwarePaginator
    {
        $product_allocation_query = $this->db->table('product_allocations')
            ->select([
                'order_number',
                $this->db->raw('SUM(product_allocations.allocation_quantity) AS allocation_quantity')
            ])
            ->groupBy('order_number')
            ->toSql();

        $query = $this->model
            ->select([
                $this->db->raw("DATE_FORMAT(orders.received_date, '%Y/%m/%d') as received_date"),
                'orders.order_number',
                'orders.slip_type',
                'orders.slip_status_type',
                $this->db->raw("DATE_FORMAT(orders.delivery_date, '%Y/%m/%d') as delivery_date"),
                'orders.end_user_code',
                'orders.delivery_destination_code',
                'delivery_destinations.delivery_destination_name',
                'delivery_destinations.delivery_destination_abbreviation',
                'orders.product_name',
                'orders.order_quantity',
                'orders.place_order_unit_code',
                'orders.order_unit',
                'orders.supplier_place_order_unit',
                'orders.order_amount',
                'orders.currency_code',
                'currencies.order_unit_decimals',
                'currencies.order_amount_decimals',
                'orders.base_plus_order_number',
                'orders.base_plus_order_chapter_number',
                'orders.end_user_order_number',
                $this->db->raw('COALESCE(product_allocations.allocation_quantity, 0) AS allocation_quantity'),
                'orders.factory_code',
                'orders.factory_product_sequence_number',
                'factory_products.number_of_cases',
                'orders.process_class',
                'orders.fixed_shipping_at',
                'orders.order_message',
                'orders.factory_cancel_flag',
                'orders.updated_by',
                'orders.updated_at',
                'orders.creating_type',
                'orders.related_order_status_type'
            ])
            ->join(
                'delivery_destinations',
                'delivery_destinations.delivery_destination_code',
                '=',
                'orders.delivery_destination_code'
            )
            ->join('currencies', 'currencies.currency_code', '=', 'orders.currency_code')
            ->leftJoin('factory_products', function ($join) {
                $join->on('factory_products.factory_code', '=', 'orders.factory_code')
                    ->on('factory_products.sequence_number', '=', 'orders.factory_product_sequence_number');
            })
            ->leftJoin(
                $this->db->raw("({$product_allocation_query}) AS product_allocations"),
                function ($join) {
                    $join->on('product_allocations.order_number', '=', 'orders.order_number');
                }
            )
            ->where('orders.factory_code', $params['factory_code'])
            ->where('orders.customer_code', $params['customer_code'])
            ->where(function ($query) use ($params) {
                if ($params['order_status'] === 'temporary') {
                    $query->where('orders.slip_status_type', SlipStatusType::TEMP_ORDER);
                }
                if ($params['order_status'] === 'fixed') {
                    $query->where('orders.slip_status_type', SlipStatusType::FIXED_ORDER);
                }
                if ($params['order_status'] === 'cancel') {
                    $query->where('orders.process_class', ProcessClass::CANCEL_PROCESS)
                        ->orWhere('orders.factory_cancel_flag', true);
                }
                if ($params['order_status'] === 'slip') {
                    $query->where('orders.slip_type', SlipType::CREDIT_SLIP);
                }
            })
            ->where(function ($query) use ($params) {
                if (! is_null($params['end_user_code'])) {
                    $query->where('orders.end_user_code', $params['end_user_code']);
                }
            })
            ->where(function ($query) use ($params) {
                if (! is_null($params['delivery_destination_code'])) {
                    $query->where('orders.delivery_destination_code', $params['delivery_destination_code']);
                }
            })
            ->where(function ($query) use ($params) {
                if (! is_null($params['received_date_from'])) {
                    $query->where('orders.received_date', '>=', $params['received_date_from']);
                }
            })
            ->where(function ($query) use ($params) {
                if (! is_null($params['received_date_to'])) {
                    $query->where('orders.received_date', '<=', $params['received_date_to']);
                }
            })
            ->where(function ($query) use ($params) {
                if (! is_null($params['delivery_date_from'])) {
                    $query->where('orders.delivery_date', '>=', $params['delivery_date_from']);
                }
            })
            ->where(function ($query) use ($params) {
                if (! is_null($params['delivery_date_to'])) {
                    $query->where('orders.delivery_date', '<=', $params['delivery_date_to']);
                }
            })
            ->where(function ($query) use ($params) {
                if (! is_null($params['order_number'])) {
                    $query->where('orders.order_number', $params['order_number']);
                }
            })
            ->where(function ($query) use ($params) {
                if (! is_null($params['base_plus_order_number'])) {
                    $query->where('orders.base_plus_order_number', $params['base_plus_order_number']);
                }
            })
            ->where(function ($query) use ($params) {
                if (! is_null($params['base_plus_order_chapter_number'])) {
                    $query->where('orders.base_plus_order_chapter_number', $params['base_plus_order_chapter_number']);
                }
            })
            ->where(function ($query) use ($params) {
                $allocation_status = ! is_null($params['allocation_status']) ? (int)$params['allocation_status'] : null;
                if ($allocation_status === AllocationStatus::UNALLOCATED) {
                    $query->whereNull('product_allocations.allocation_quantity');
                }
                if ($allocation_status === AllocationStatus::ALLOCATED) {
                    $query->whereRaw(
                        'product_allocations.allocation_quantity = '.
                        '(orders.order_quantity * factory_products.number_of_cases)'
                    );
                }
                if ($allocation_status === AllocationStatus::PART_ALLOCATED) {
                    $query->where('product_allocations.allocation_quantity', '>', '0')
                        ->whereRaw(
                            'product_allocations.allocation_quantity <> '.
                            '(orders.order_quantity * factory_products.number_of_cases)'
                        );
                }
            })
            ->where(function ($query) use ($params) {
                $shipment_status = ! is_null($params['shipment_status'] ?? null) ?
                    (int)$params['shipment_status'] :
                    null;
                if ($shipment_status === ShipmentStatus::UNSHIPPED) {
                    $query->whereNull('orders.fixed_shipping_at');
                }
                if ($shipment_status === ShipmentStatus::SHIPPED) {
                    $query->whereNotNull('orders.fixed_shipping_at');
                }
            })
            ->where('orders.slip_status_type', '<>', SlipStatusType::RELATION_TEMP_ORDER);

        if (count($order) === 0) {
            $query->orderBy('received_date', 'ASC')
                ->orderBy('order_number', 'ASC');
        }
        if (count($order) !== 0) {
            $query->orderBy($order['sort'], $order['order']);
        }

        return $query->paginate();
    }

    /**
     * Excel出力用データ取得
     *
     * @param array $params
     * @return \App\Models\Order\Collections\OrderCollection
     */
    public function searchOrdersToExport(array $params): OrderCollection
    {
        $order_allocation_query = $this->db
            ->table('product_allocations')
            ->select([
                'order_number',
                $this->db->raw('SUM(allocation_quantity) AS allocation_quantity')
            ])
            ->groupBy('order_number')
            ->toSql();

        $returned_product_query = $this->db->table('returned_products')
            ->select([
                'returned_products.order_number',
                'returned_products.returned_on',
                'returned_products.unit_price',
                'returned_products.quantity',
                'returned_products.remark',
                'products.product_name'
            ])
            ->join('products', 'returned_products.product_code', '=', 'products.product_code')
            ->toSql();

        return $this->model
            ->select([
                'orders.*',
                'delivery_destinations.delivery_destination_name',
                'delivery_destinations.delivery_destination_abbreviation',
                $this->db->raw("DATE_FORMAT(orders.received_date, '%Y/%m/%d') AS received_date"),
                $this->db->raw("DATE_FORMAT(orders.delivery_date, '%Y/%m/%d') AS delivery_date"),
                $this->db->raw("DATE_FORMAT(orders.shipping_date, '%Y/%m/%d') AS shipping_date"),
                'currencies.order_unit_decimals',
                'currencies.order_amount_decimals',
                $this->db->raw("DATE_FORMAT(orders.fixed_shipping_at, '%Y/%m/%d') AS fixed_shipping_on"),
                $this->db->raw('COALESCE(product_allocations.allocation_quantity, 0) AS allocation_quantity'),
                $this->db->raw("DATE_FORMAT(returned_products.returned_on, '%Y/%m/%d') AS returned_on"),
                'returned_products.product_name AS returned_product_name',
                'returned_products.unit_price AS returned_unit_price',
                'returned_products.quantity AS returned_quantity',
                'returned_products.remark AS returned_remark',
                'factory_products.number_of_cases',
                'collection_time.collection_time'
            ])
            ->join(
                'delivery_destinations',
                'delivery_destinations.delivery_destination_code',
                '=',
                'orders.delivery_destination_code'
            )
            ->join('currencies', 'currencies.currency_code', '=', 'orders.currency_code')
            ->leftJoin(
                $this->db->raw("({$order_allocation_query}) AS product_allocations"),
                'product_allocations.order_number',
                '=',
                'orders.order_number'
            )
            ->leftJoin(
                $this->db->raw("({$returned_product_query}) AS returned_products"),
                'returned_products.order_number',
                '=',
                'orders.order_number'
            )
            ->leftJoin('factory_products', function ($join) {
                $join->on('factory_products.factory_code', '=', 'orders.factory_code')
                    ->on('factory_products.sequence_number', '=', 'orders.factory_product_sequence_number');
            })
            ->leftJoin('collection_time', function ($join) {
                $join->on('collection_time.transport_company_code', '=', 'orders.transport_company_code')
                    ->on('collection_time.sequence_number', '=', 'orders.collection_time_sequence_number');
            })
            ->where('orders.factory_code', $params['factory_code'])
            ->where('orders.customer_code', $params['customer_code'])
            ->where(function ($query) use ($params) {
                if ($params['order_status'] === 'temporary') {
                    $query->where('orders.slip_status_type', SlipStatusType::TEMP_ORDER);
                }
                if ($params['order_status'] === 'fixed') {
                    $query->where('orders.slip_status_type', SlipStatusType::FIXED_ORDER);
                }
                if ($params['order_status'] === 'cancel') {
                    $query->where('orders.process_class', ProcessClass::CANCEL_PROCESS)
                        ->orWhere('orders.factory_cancel_flag', true);
                }
                if ($params['order_status'] === 'slip') {
                    $query->where('orders.slip_type', SlipType::CREDIT_SLIP);
                }
            })
            ->where(function ($query) use ($params) {
                if (! is_null($params['end_user_code'])) {
                    $query->where('orders.end_user_code', $params['end_user_code']);
                }
            })
            ->where(function ($query) use ($params) {
                if (! is_null($params['delivery_destination_code'])) {
                    $query->where('orders.delivery_destination_code', $params['delivery_destination_code']);
                }
            })
            ->where(function ($query) use ($params) {
                if (! is_null($params['received_date_from'])) {
                    $query->where('orders.received_date', '>=', $params['received_date_from']);
                }
            })
            ->where(function ($query) use ($params) {
                if (! is_null($params['received_date_to'])) {
                    $query->where('orders.received_date', '<=', $params['received_date_to']);
                }
            })
            ->where(function ($query) use ($params) {
                if (! is_null($params['delivery_date_from'])) {
                    $query->where('orders.delivery_date', '>=', $params['delivery_date_from']);
                }
            })
            ->where(function ($query) use ($params) {
                if (! is_null($params['delivery_date_to'])) {
                    $query->where('orders.delivery_date', '<=', $params['delivery_date_to']);
                }
            })
            ->where(function ($query) use ($params) {
                if (! is_null($params['order_number'])) {
                    $query->where('orders.order_number', $params['order_number']);
                }
            })
            ->where(function ($query) use ($params) {
                if (! is_null($params['base_plus_order_number'])) {
                    $query->where('orders.base_plus_order_number', $params['base_plus_order_number']);
                }
            })
            ->where(function ($query) use ($params) {
                if (! is_null($params['base_plus_order_chapter_number'])) {
                    $query->where('orders.base_plus_order_chapter_number', $params['base_plus_order_chapter_number']);
                }
            })
            ->where(function ($query) use ($params) {
                $allocation_status = ! is_null($params['allocation_status']) ? (int)$params['allocation_status'] : null;
                if ($allocation_status === AllocationStatus::UNALLOCATED) {
                    $query->whereNull('product_allocations.allocation_quantity');
                }
                if ($allocation_status === AllocationStatus::ALLOCATED) {
                    $query->whereRaw(
                        'product_allocations.allocation_quantity = '.
                        '(orders.order_quantity * factory_products.number_of_cases)'
                    );
                }
                if ($allocation_status === AllocationStatus::PART_ALLOCATED) {
                    $query->where('product_allocations.allocation_quantity', '>', '0')
                        ->whereRaw(
                            'product_allocations.allocation_quantity <> '.
                            '(orders.order_quantity * factory_products.number_of_cases)'
                        );
                }
            })
            ->where(function ($query) use ($params) {
                $shipment_status = ! is_null($params['shipment_status'] ?? null) ?
                    (int)$params['shipment_status'] :
                    null;
                if ($shipment_status === ShipmentStatus::UNSHIPPED) {
                    $query->whereNull('orders.fixed_shipping_at');
                }
                if ($shipment_status === ShipmentStatus::SHIPPED) {
                    $query->whereNotNull('orders.fixed_shipping_at');
                }
            })
            ->where('orders.slip_status_type', '<>', SlipStatusType::RELATION_TEMP_ORDER)
            ->orderBy('received_date', 'ASC')
            ->get();
    }

    /**
     * 紐づけ可能な確定注文を検索する
     *
     * @param  array $params
     * @return \App\Models\Order\Collections\OrderCollection
     */
    public function searchLinkableFixedOrders(array $params): OrderCollection
    {
        $product_allocation_query = $this->db->table('product_allocations')
            ->select([
                'order_number',
                $this->db->raw('SUM(allocation_quantity) AS allocation_quantity')
            ])
            ->where('factory_code', $params['factory_code'])
            ->groupBy('order_number');

        return $this->model
            ->select([
                'orders.order_number',
                $this->db->raw("DATE_FORMAT(orders.received_date, '%Y/%m/%d') AS received_date"),
                $this->db->raw("DATE_FORMAT(orders.delivery_date, '%Y/%m/%d') AS delivery_date"),
                'orders.product_name',
                'orders.order_quantity',
                'orders.place_order_unit_code',
                'orders.order_unit',
                'orders.order_amount',
                'orders.currency_code',
                'currencies.order_unit_decimals',
                'currencies.order_amount_decimals',
                'orders.base_plus_order_number',
                'orders.base_plus_order_chapter_number',
                'orders.end_user_order_number',
                'orders.order_message'
            ])
            ->join('products', 'products.product_code', '=', 'orders.product_code')
            ->join('factory_products', function ($join) {
                $join->on('factory_products.factory_code', '=', 'orders.factory_code')
                    ->on('factory_products.sequence_number', '=', 'orders.factory_product_sequence_number');
            })
            ->join('currencies', 'currencies.currency_code', '=', 'orders.currency_code')
            ->leftJoin(
                $this->db->raw("({$product_allocation_query->toSql()}) AS product_allocations"),
                'product_allocations.order_number',
                '=',
                'orders.order_number'
            )
            ->setBindings($product_allocation_query->getBindings())
            ->where('orders.creating_type', CreatingType::BASE_PLUS_LINKED)
            ->where('orders.slip_type', SlipType::NORMAL_SLIP)
            ->where('orders.slip_status_type', SlipStatusType::FIXED_ORDER)
            ->where('orders.related_order_status_type', RelatedOrderStatusType::UN_RELATED)
            ->where('orders.factory_cancel_flag', false)
            ->whereNull('orders.fixed_shipping_at')
            ->where('orders.process_class', '<>', ProcessClass::CANCEL_PROCESS)
            ->where('orders.factory_code', $params['factory_code'])
            ->where('orders.customer_code', $params['customer_code'])
            ->where('orders.end_user_code', $params['end_user_code'])
            ->where('orders.delivery_destination_code', $params['delivery_destination_code'])
            ->where('orders.factory_product_sequence_number', $params['factory_product_sequence_number'])
            ->whereRaw('COALESCE(product_allocations.allocation_quantity, 0) = 0')
            ->where(function ($query) use ($params) {
                if ($params['received_date_from'] ?? null) {
                    $query->where('orders.received_date', '>=', $params['received_date_from']);
                }
                if ($params['received_date_to'] ?? null) {
                    $query->where('orders.received_date', '<=', $params['received_date_to']);
                }
            })
            ->where(function ($query) use ($params) {
                if ($params['delivery_date_from'] ?? null) {
                    $query->where('orders.delivery_date', '>=', $params['delivery_date_from']);
                }
                if ($params['delivery_date_to'] ?? null) {
                    $query->where('orders.delivery_date', '<=', $params['delivery_date_to']);
                }
            })
            ->where(function ($query) use ($params) {
                if ($params['order_number'] ?? null) {
                    $query->where('orders.order_number', $params['order_number']);
                }
            })
            ->where(function ($query) use ($params) {
                if ($params['base_plus_order_number'] ?? null) {
                    $query->where('orders.base_plus_order_number', $params['base_plus_order_number']);
                }
                if ($params['base_plus_order_chapter_number'] ?? null) {
                    $query->where('orders.base_plus_order_chapter_number', $params['base_plus_order_chapter_number']);
                }
            })
            ->limit(Model::API_SEARCHING_LIMIT)
            ->get();
    }

    /**
     * 返品情報とともに注文情報を取得
     *
     * @param  array $params
     * @param  array $order
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getOrdersWithReturnedProduct(array $params, array $order): LengthAwarePaginator
    {
        $query = $this->model
            ->select([
                'orders.order_number',
                'orders.base_plus_order_number',
                'orders.base_plus_order_chapter_number',
                'orders.end_user_order_number',
                $this->db->raw("DATE_FORMAT(orders.received_date, '%Y/%m/%d') AS received_date"),
                $this->db->raw("DATE_FORMAT(orders.delivery_date, '%Y/%m/%d') AS delivery_date"),
                'orders.end_user_code',
                'orders.delivery_destination_code',
                'delivery_destinations.delivery_destination_name',
                'delivery_destinations.delivery_destination_abbreviation',
                'orders.product_code',
                'orders.product_name',
                'products.species_code',
                'orders.factory_product_sequence_number',
                'factory_products.unit',
                'orders.order_quantity',
                'orders.order_unit',
                'orders.order_amount',
                'orders.currency_code',
                'returned_products.order_number AS returned_order_number',
                'returned_products.factory_product_sequence_number AS returned_factory_product_sequence_number',
                $this->db->raw("DATE_FORMAT(returned_products.returned_on, '%Y/%m/%d') AS returned_on"),
                'returned_products.product_code AS returned_product_code',
                'returned_product_products.product_name AS returned_product_name',
                'returned_products.unit_price AS returned_unit_price',
                'returned_products.quantity AS returned_quantity',
                $this->db->raw('(returned_products.unit_price * returned_products.quantity) AS returned_amount'),
                $this->db->raw(
                    '(orders.order_amount - (COALESCE(returned_products.unit_price * returned_products.quantity, 0))) '.
                    'AS amount_except_returned'
                ),
                'returned_products.remark AS returned_remark',
                'returned_products.updated_at AS returned_updated_at',
                'orders.factory_code'
            ])
            ->join('factory_products', function ($join) {
                $join->on('factory_products.factory_code', '=', 'orders.factory_code')
                    ->on('factory_products.sequence_number', '=', 'orders.factory_product_sequence_number');
            })
            ->join('products', 'products.product_code', '=', 'factory_products.product_code')
            ->join(
                'delivery_destinations',
                'delivery_destinations.delivery_destination_code',
                '=',
                'orders.delivery_destination_code'
            )
            ->leftJoin('returned_products', 'returned_products.order_number', '=', 'orders.order_number')
            ->leftjoin(
                'products AS returned_product_products',
                'returned_product_products.product_code',
                '=',
                'returned_products.product_code'
            )
            ->where('orders.factory_code', $params['factory_code'])
            ->where(function ($query) use ($params) {
                if ($order_number = $params['order_number']) {
                    $query->where('orders.order_number', $order_number);
                }
            })
            ->where(function ($query) use ($params) {
                if ($base_plus_order_number = $params['base_plus_order_number']) {
                    $query->where('orders.base_plus_order_number', $base_plus_order_number);
                }
                if ($base_plus_order_chapter_number = $params['base_plus_order_chapter_number']) {
                    $query->where('orders.base_plus_order_chapter_number', $base_plus_order_chapter_number);
                }
            })
            ->where(function ($query) use ($params) {
                if ($received_date = $params['received_date']) {
                    $query->where('orders.received_date', $received_date);
                }
            })
            ->where(function ($query) use ($params) {
                if ($product_code = $params['product_code'] ?? null) {
                    $query->where('orders.product_code', $product_code);
                }
            })
            ->where(function ($query) use ($params) {
                if ($delivery_date = $params['delivery_date']) {
                    $query->where('orders.delivery_date', $delivery_date);
                }
            })
            ->where(function ($query) use ($params) {
                if ($end_user_code = $params['end_user_code']) {
                    $query->where('orders.end_user_code', $end_user_code);
                }
            })
            ->where(function ($query) use ($params) {
                if ($delivery_destination_code = $params['delivery_destination_code']) {
                    $query->where('orders.delivery_destination_code', $delivery_destination_code);
                }
            })
            ->where('orders.process_class', '<>', ProcessClass::CANCEL_PROCESS)
            ->where('orders.slip_type', SlipType::NORMAL_SLIP)
            ->where('orders.slip_status_type', '<>', SlipStatusType::RELATION_TEMP_ORDER)
            ->where('orders.factory_cancel_flag', false)
            ->whereNotNull('orders.fixed_shipping_at')
            ->with('currency');

        if (array_key_exists('sort', $order) && array_key_exists('order', $order)) {
            $query->orderBy($order['sort'], $order['order']);
        }

        return $query->paginate();
    }

    /**
     * 出荷作業帳票出力 検索
     *
     * @param  array $params
     * @param  array $order_numbers
     * @return \App\Models\Order\Collections\OrderCollection
     */
    public function searchOrdersToOutputShipmentFiles(array $params, ?array $order_numbers = []): OrderCollection
    {
        $sub_query_sil = $this->db->table('shipment_infomation_logs')
            ->select([
                'order_number',
                $this->db->raw('COUNT(sequence_number) as print_number')
            ])
            ->groupBy('order_number');

        $sub_query_iril = $this->db->table('invoice_receipt_infomation_logs')
            ->select([
                'order_number',
                $this->db->raw('COUNT(sequence_number) as print_number')
            ])
            ->groupBy('order_number');

        $query = $this->model
            ->select([
                'orders.order_number',
                'orders.base_plus_order_number',
                'orders.base_plus_order_chapter_number',
                'orders.end_user_order_number',
                $this->db->raw("DATE_FORMAT(orders.printing_shipping_date, '%Y/%m/%d') AS shipping_date"),
                $this->db->raw("DATE_FORMAT(orders.delivery_date, '%Y/%m/%d') AS delivery_date"),
                'orders.end_user_code',
                'orders.delivery_destination_code',
                'delivery_destinations.delivery_destination_name',
                'delivery_destinations.delivery_destination_abbreviation',
                'delivery_destinations.postal_code AS delivery_destination_postal_code',
                'delivery_destinations.address AS delivery_destination_address',
                'delivery_destinations.phone_number AS delivery_destination_phone_number',
                'delivery_destinations.fsystem_statement_of_delivery_output_class AS '.
                    'fsystem_statement_of_delivery_output_class',
                'orders.slip_status_type AS slip_status_type',
                'orders.order_quantity',
                'orders.place_order_unit_code',
                'orders.product_name',
                'factory_products.unit',
                'orders.order_unit',
                'orders.order_amount',
                'orders.recived_order_unit AS received_order_unit',
                'orders.customer_recived_order_unit AS customer_received_order_amount',
                'orders.currency_code',
                'currencies.order_unit_decimals',
                'currencies.order_amount_decimals',
                'orders.order_message'
            ])
            ->join(
                'delivery_destinations',
                'delivery_destinations.delivery_destination_code',
                '=',
                'orders.delivery_destination_code'
            )
            ->join('currencies', 'currencies.currency_code', '=', 'orders.currency_code')
            ->join('factory_products', function ($join) {
                $join->on('factory_products.factory_code', '=', 'orders.factory_code')
                    ->on('factory_products.sequence_number', '=', 'orders.factory_product_sequence_number');
            })
            ->leftJoin(
                $this->db->raw("({$sub_query_sil->toSql()}) AS shipment_infomation_logs"),
                function ($join) {
                    $join->on('shipment_infomation_logs.order_number', '=', 'orders.order_number');
                }
            )
            ->leftJoin(
                $this->db->raw("({$sub_query_iril->toSql()}) AS invoice_receipt_infomation_logs"),
                function ($join) {
                    $join->on('invoice_receipt_infomation_logs.order_number', '=', 'orders.order_number');
                }
            )
            ->where(function ($query) use ($order_numbers) {
                if (count($order_numbers) !== 0) {
                    $query->whereIn('orders.order_number', $order_numbers);
                }
            })
            ->where('orders.factory_code', $params['factory_code'])
            ->where('orders.customer_code', $params['customer_code'])
            ->where(function ($query) use ($params) {
                if ((int)$params['print_state'] !== PrintState::ALL) {
                    if ((int)$params['output_file'] === OutputFile::SHIPPING_INFO) {
                        if ((int)$params['print_state'] === PrintState::UNPRINTED) {
                            $query->whereRaw('COALESCE(shipment_infomation_logs.print_number, 0) = 0');
                        }
                        if ((int)$params['print_state'] === PrintState::PRINTED) {
                            $query->whereRaw('COALESCE(shipment_infomation_logs.print_number, 0) > 0');
                        }
                    }

                    if ((int)$params['output_file'] === OutputFile::NOTE_RECEIPT) {
                        if ((int)$params['print_state'] === PrintState::UNPRINTED) {
                            $query->whereRaw('COALESCE(invoice_receipt_infomation_logs.print_number, 0) = 0');
                        }
                        if ((int)$params['print_state'] === PrintState::PRINTED) {
                            $query->whereRaw('COALESCE(invoice_receipt_infomation_logs.print_number, 0) > 0');
                        }
                    }
                }
            })
            ->where(function ($query) use ($params) {
                if ($end_user_code =  $params['end_user_code'] ?? null) {
                    $query->where('orders.end_user_code', $end_user_code);
                }
            })
            ->where(function ($query) use ($params) {
                if ($delivery_destination_code =  $params['delivery_destination_code'] ?? null) {
                    $query->where('orders.delivery_destination_code', $delivery_destination_code);
                }
            })
            ->where(function ($query) use ($params) {
                if ($shipping_date_from =  $params['shipping_date_from'] ?? null) {
                    $query->where('orders.printing_shipping_date', '>=', $shipping_date_from);
                }
            })
            ->where(function ($query) use ($params) {
                if ($shipping_date_to = $params['shipping_date_to'] ?? null) {
                    $query->where('orders.printing_shipping_date', '<=', $shipping_date_to);
                }
            })
            ->where(function ($query) use ($params) {
                if ($delivery_date_from = $params['delivery_date_from'] ?? null) {
                    $query->where('orders.delivery_date', '>=', $delivery_date_from);
                }
            })
            ->where(function ($query) use ($params) {
                if ($delivery_date_to = $params['delivery_date_to'] ?? null) {
                    $query->where('orders.delivery_date', '<=', $delivery_date_to);
                }
            })
            ->where(function ($query) use ($params) {
                if ($order_number = $params['order_number'] ?? null) {
                    $query->where('orders.order_number', $order_number);
                }
            })
            ->where(function ($query) use ($params) {
                if ($base_plus_order_number = $params['base_plus_order_number'] ?? null) {
                    $query->where('orders.base_plus_order_number', $base_plus_order_number);
                }
            })
            ->where(function ($query) use ($params) {
                if ($base_plus_order_chapter_number =  $params['base_plus_order_chapter_number'] ?? null) {
                    $query->where('orders.base_plus_order_chapter_number', $base_plus_order_chapter_number);
                }
            })
            ->where('orders.process_class', '<>', ProcessClass::CANCEL_PROCESS)
            ->where('orders.slip_type', SlipType::NORMAL_SLIP)
            ->where('orders.slip_status_type', '<>', SlipStatusType::RELATION_TEMP_ORDER)
            ->where('orders.factory_cancel_flag', false)
            ->orderBy('orders.printing_shipping_date', 'DESC')
            ->orderBy('orders.delivery_date', 'DESC')
            ->orderBy('orders.end_user_code', 'ASC')
            ->orderBy('orders.delivery_destination_code', 'ASC')
            ->orderBy('orders.order_number', 'ASC');

        if ((int)$params['output_file'] === OutputFile::SHIPPING_INFO) {
            $query
                ->addSelect([
                    $this->db->raw(
                        'COALESCE(shipment_infomation_logs.print_number, 0) AS count_shipment_infomation_logs'
                    ),
                    $this->db->raw(
                        "(CASE WHEN COALESCE(shipment_infomation_logs.print_number, 0) = 0 THEN '' ELSE '印字済' END) ".
                        'AS print_state'
                    )
                ])
                ->where(
                    'delivery_destinations.statement_of_shipment_output_class',
                    '<>',
                    StatementOfShipmentOutputClass::DISABLED
                );
        }
        if ((int)$params['output_file'] === OutputFile::NOTE_RECEIPT) {
            $query
                ->addSelect([
                    $this->db->raw(
                        'COALESCE(invoice_receipt_infomation_logs.print_number, 0) AS '.
                        'count_invoice_receipt_infomation_logs'
                    ),
                    $this->db->raw(
                        "(CASE WHEN COALESCE(invoice_receipt_infomation_logs.print_number, 0) = 0 THEN '' ELSE '印字済'".
                        ' END) AS print_state'
                    )
                ])
                ->where(
                    'delivery_destinations.fsystem_statement_of_delivery_output_class',
                    '<>',
                    FsystemStatementOfDeliveryOutputClass::DISABLED
                );
        }

        return $query->get();
    }

    /**
     * 集荷依頼書出力対象の注文情報を検索
     *
     * @param  array $params
     * @param  array $order_numbers
     * @return \App\Models\Order\Collections\OrderCollection
     */
    public function searchOrdersToOutputCollectionRequest(array $params, ?array $order_numbers = []): OrderCollection
    {
        return $this->model
            ->select([
                'orders.order_number',
                'orders.factory_code',
                $this->db->raw("DATE_FORMAT(orders.printing_shipping_date, '%Y/%m/%d') AS shipping_date"),
                $this->db->raw("DATE_FORMAT(orders.delivery_date, '%Y/%m/%d') AS delivery_date"),
                'orders.order_quantity',
                'orders.end_user_code',
                'orders.delivery_destination_code',
                'delivery_destinations.delivery_destination_name',
                'delivery_destinations.delivery_destination_abbreviation',
                'delivery_destinations.address AS delivery_destination_address',
                'delivery_destinations.needs_to_subtract_printing_delivery_date',
                'delivery_destinations.collection_request_remark',
                'orders.product_code',
                'orders.product_name',
                $this->db->raw(
                    '(factory_products.weight_per_number_of_heads * factory_products.number_of_cases) '.
                    'AS product_weight_per_case'
                ),
                'orders.transport_company_code',
                'transport_companies.transport_company_name',
                'transport_companies.transport_branch_name',
                'transport_companies.transport_company_abbreviation',
                'transport_companies.phone_number AS tarnsport_company_phone_number',
                'transport_companies.fax_number AS transport_company_fax_number',
                'orders.collection_time_sequence_number',
                'collection_time.collection_time',
                'orders.fixed_shipping_at',
                $this->db->raw(
                    '(factory_products.can_be_transported_double AND '.
                    'COALESCE(transport_companies.can_transport_double, 0)) AS is_transportable_one_in_two'
                )
            ])
            ->join('delivery_destinations', function ($join) {
                $join->on('delivery_destinations.delivery_destination_code', '=', 'orders.delivery_destination_code');
            })
            ->join('factory_products', function ($join) {
                $join->on('factory_products.factory_code', '=', 'orders.factory_code')
                    ->on('factory_products.sequence_number', '=', 'orders.factory_product_sequence_number');
            })
            ->leftJoin('transport_companies', function ($join) {
                $join->on('transport_companies.transport_company_code', '=', 'orders.transport_company_code');
            })
            ->leftJoin('collection_time', function ($join) {
                $join->on('collection_time.transport_company_code', '=', 'orders.transport_company_code')
                    ->on('collection_time.sequence_number', '=', 'orders.collection_time_sequence_number');
            })
            ->where(function ($query) use ($order_numbers) {
                if (count($order_numbers) !== 0) {
                    $query->whereIn('orders.order_number', $order_numbers);
                }
            })
            ->where('orders.factory_code', $params['factory_code'])
            ->where('orders.customer_code', $params['customer_code'])
            ->where(function ($query) use ($params) {
                if (! is_null($params['end_user_code'])) {
                    $query->where('orders.end_user_code', $params['end_user_code']);
                }
            })
            ->where(function ($query) use ($params) {
                if (! is_null($params['shipping_date'])) {
                    $query->where('orders.printing_shipping_date', $params['shipping_date']);
                }
            })
            ->where(function ($query) use ($params) {
                if (! is_null($params['transport_company_code'])) {
                    $query->where('orders.transport_company_code', $params['transport_company_code']);
                }
            })
            ->where(function ($query) use ($params) {
                if (! is_null($params['collection_time_sequence_number'] ?? null)) {
                    $query->where('orders.collection_time_sequence_number', $params['collection_time_sequence_number']);
                }
            })
            ->where('orders.process_class', '<>', ProcessClass::CANCEL_PROCESS)
            ->where('orders.slip_type', SlipType::NORMAL_SLIP)
            ->where('orders.slip_status_type', '<>', SlipStatusType::RELATION_TEMP_ORDER)
            ->where('orders.factory_cancel_flag', false)
            ->orderBy('orders.printing_shipping_date', 'DESC')
            ->orderBy('orders.transport_company_code', 'ASC')
            ->orderBy('orders.collection_time_sequence_number', 'ASC')
            ->orderBy('orders.end_user_code', 'ASC')
            ->orderBy('orders.delivery_destination_code', 'ASC')
            ->orderBy('orders.order_number', 'ASC')
            ->get();
    }

    /**
     * 注文情報を、更新前の注文数とともに取得
     *
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Master\Species $species
     * @param  \App\ValueObjects\Date\Date $date
     * @param  bool $only_one_month
     * @return \App\Models\Order\Collections\OrderCollection
     * @throws InvalidArgumentException
     */
    public function getOrdersWithPreviousOrderQuantity(
        Factory $factory,
        Species $species,
        Date $date,
        bool $only_one_month
    ): OrderCollection {
        $product_allocation_query = $this->db->table('product_allocations')
            ->select([
                'order_number',
                $this->db->raw('SUM(allocation_quantity) AS allocation_quantity')
            ])
            ->groupBy('order_number');

        $order_history_query = $this->db->raw(
            'SELECT oh.order_number, oh.order_quantity FROM order_histories AS oh '.
            'INNER JOIN ('.
            'SELECT order_number, MAX(registration_date) AS registration_date '.
            'FROM order_histories '.
            'WHERE slip_status_type = ? AND process_class <> ? GROUP BY order_number) AS latest '.
            'ON latest.order_number = oh.order_number AND latest.registration_date = oh.registration_date'
        );

        $date_term = [];
        if ($only_one_month) {
            $date_term = [
                'from' => head($date->toListOfDatesOfTheMonth())->format('Y-m-d'),
                'to' => last($date->toListOfDatesOfTheMonth())->format('Y-m-d')
            ];
        }
        if (! $only_one_month) {
            $date_term = [
                'from' => head($date->toListToExportOrderForecasts())->format('Y-m-d'),
                'to' => last($date->toListToExportOrderForecasts())->format('Y-m-d')
            ];
        }

        $target_date = '';
        if ($date instanceof DeliveryDate) {
            $target_date = 'delivery_date';
        }
        if ($date instanceof ShippingDate) {
            $target_date = 'shipping_date';
        }
        if ($target_date === '') {
            throw new InvalidArgumentException('target date was invalid:'. get_class($date));
        }

        return $this->model
            ->select([
                'orders.factory_code',
                'orders.factory_product_sequence_number',
                'factory_products.number_of_cases',
                'orders.delivery_destination_code',
                'orders.delivery_date',
                'orders.shipping_date',
                'orders.order_quantity',
                'orders.slip_status_type',
                'orders.transport_company_code',
                'orders.collection_time_sequence_number',
                $this->db->raw('COALESCE(product_allocations.allocation_quantity, 0) AS allocation_quantity'),
                'orders.fixed_shipping_at',
                $this->db->raw('COALESCE(order_histories.order_quantity, orders.order_quantity) AS prev_order_quantity')
            ])
            ->join('factory_products', function ($join) {
                $join->on('factory_products.factory_code', '=', 'orders.factory_code')
                    ->on('factory_products.sequence_number', '=', 'orders.factory_product_sequence_number');
            })
            ->join('products', 'products.product_code', '=', 'factory_products.product_code')
            ->leftJoin(
                $this->db->raw("({$product_allocation_query->toSql()}) AS product_allocations"),
                function ($join) {
                    $join->on('orders.order_number', '=', 'product_allocations.order_number');
                }
            )
            ->leftJoin(
                $this->db->raw("({$order_history_query}) AS order_histories"),
                function ($join) {
                    $join->on('orders.order_number', '=', 'order_histories.order_number');
                }
            )
            ->setBindings([SlipStatusType::FIXED_ORDER, ProcessClass::CANCEL_PROCESS])
            ->where('orders.factory_code', $factory->factory_code)
            ->where('products.species_code', $species->species_code)
            ->whereBetween("orders.{$target_date}", array_values($date_term))
            ->where('orders.process_class', '<>', ProcessClass::CANCEL_PROCESS)
            ->where('orders.slip_type', SlipType::NORMAL_SLIP)
            ->where('orders.slip_status_type', '<>', SlipStatusType::RELATION_TEMP_ORDER)
            ->where('orders.factory_cancel_flag', false)
            ->orderBy('orders.factory_product_sequence_number', 'ASC')
            ->orderBy('orders.delivery_destination_code', 'ASC')
            ->orderBy("orders.{$target_date}", 'ASC')
            ->get();
    }

    /**
     * 指定された納入工場商品の注文情報を、既定の収穫日の分だけ取得
     *
     * @param  \App\Models\Master\DeliveryFactoryProduct $delivery_factory_product
     * @param  \App\ValueObjects\Date\HarvestingDate $harvesting_date
     * @return \App\Models\Order\Collections\OrderCollection
     */
    public function getOrdersByDeliveryFactoryProductAndHarvestingDate(
        DeliveryFactoryProduct $delivery_factory_product,
        HarvestingDate $harvesting_date
    ): OrderCollection {
        return $this->model
            ->select([
                'delivery_destination_code',
                'factory_code',
                'factory_product_sequence_number',
                'shipping_date',
                $this->db->raw(
                    sprintf(
                        '(CASE WHEN slip_status_type = %d THEN 1 ELSE 0 END) AS is_temporary_order',
                        SlipStatusType::TEMP_ORDER
                    )
                ),
                $this->db->raw('SUM(orders.order_quantity) AS order_quantity'),
                $this->db->raw('SUM(orders.product_weight) AS product_weight')
            ])
            ->where('delivery_destination_code', $delivery_factory_product->delivery_destination_code)
            ->where('factory_code', $delivery_factory_product->factory_code)
            ->where('factory_product_sequence_number', $delivery_factory_product->factory_product_sequence_number)
            ->whereBetween('shipping_date', [
                $harvesting_date->format('Y-m-d'),
                $harvesting_date->getEndOfDateOfGrowthSaleManagement()->format('Y-m-d')
            ])
            ->where('process_class', '<>', ProcessClass::CANCEL_PROCESS)
            ->where('slip_type', SlipType::NORMAL_SLIP)
            ->where('slip_status_type', '<>', SlipStatusType::RELATION_TEMP_ORDER)
            ->where('factory_cancel_flag', false)
            ->groupBy([
                'delivery_destination_code',
                'factory_code',
                'factory_product_sequence_number',
                'shipping_date',
                'is_temporary_order'
            ])
            ->get();
    }

    /**
     * 指定された品種、期間に応じて注文データを取得
     *
     * @param  array $params
     * @param  array $shipping_date_term
     * @return \App\Models\Order\Collections\OrderCollection
     */
    public function getOrdersBySpeciesAndHarvestingDate(array $params, array $shipping_date_term): OrderCollection
    {
        $lastest_delivery_date_query = $this->db->table('orders')
            ->select([
                'delivery_destination_code',
                'factory_code',
                'factory_product_sequence_number',
                $this->db->raw('MAX(delivery_date) AS latest_delivery_date')
            ])
            ->where(function ($query) use ($params) {
                if ($delivery_destination_code = $params['delivery_destination_code'] ?? null) {
                    $query->where('delivery_destination_code', $delivery_destination_code);
                }
            })
            ->where(function ($query) use ($params) {
                if ($factory_code = $params['factory_code'] ?? null) {
                    $query->where('factory_code', $factory_code);
                }
            })
            ->where('process_class', '<>', ProcessClass::CANCEL_PROCESS)
            ->where('slip_type', SlipType::NORMAL_SLIP)
            ->where('slip_status_type', '<>', SlipStatusType::RELATION_TEMP_ORDER)
            ->where('factory_cancel_flag', false)
            ->groupBy(['delivery_destination_code', 'factory_code', 'factory_product_sequence_number']);

        $query = $this->model
            ->select([
                'orders.delivery_destination_code',
                'delivery_destinations.delivery_destination_abbreviation',
                'orders.factory_code',
                'factories.factory_abbreviation',
                'orders.factory_product_sequence_number',
                'factory_products.factory_product_name',
                'factory_products.factory_product_abbreviation',
                'factory_products.number_of_heads',
                'factory_products.weight_per_number_of_heads',
                'factory_products.input_group',
                'factory_products.number_of_cases',
                'lastest_delivery_dates.latest_delivery_date'
            ])
            ->join(
                'delivery_destinations',
                'delivery_destinations.delivery_destination_code',
                '=',
                'orders.delivery_destination_code'
            )
            ->join('factory_products', function ($join) {
                $join->on('factory_products.factory_code', '=', 'orders.factory_code')
                    ->on(
                        'factory_products.sequence_number',
                        '=',
                        'orders.factory_product_sequence_number'
                    );
            })
            ->join('factories', 'factories.factory_code', '=', 'factory_products.factory_code')
            ->join('products', 'products.product_code', '=', 'factory_products.product_code')
            ->leftJoin(
                $this->db->raw("({$lastest_delivery_date_query->toSql()}) AS lastest_delivery_dates"),
                function ($join) {
                    $join->on(
                        'lastest_delivery_dates.delivery_destination_code',
                        '=',
                        'orders.delivery_destination_code'
                    )
                    ->on('lastest_delivery_dates.factory_code', '=', 'orders.factory_code')
                    ->on(
                        'lastest_delivery_dates.factory_product_sequence_number',
                        '=',
                        'orders.factory_product_sequence_number'
                    );
                }
            )
            ->setBindings($lastest_delivery_date_query->getBindings())
            ->where('products.species_code', $params['species_code'])
            ->where(function ($query) use ($params) {
                $factory_code = $params['factory_code'] ?? null;
                if (! is_null($factory_code)) {
                    $query->where('orders.factory_code', $factory_code);
                }
                if (is_null($factory_code)) {
                    $query->affiliatedFactories('orders');
                }
            })
            ->where(function ($query) use ($params) {
                if ($delivery_destination_code = $params['delivery_destination_code'] ?? null) {
                    $query->where('orders.delivery_destination_code', $delivery_destination_code);
                }
            })
            ->where('orders.process_class', '<>', ProcessClass::CANCEL_PROCESS)
            ->where('orders.slip_type', SlipType::NORMAL_SLIP)
            ->where('orders.slip_status_type', '<>', SlipStatusType::RELATION_TEMP_ORDER)
            ->where('orders.factory_cancel_flag', false)
            ->orderBy('orders.factory_code', 'ASC')
            ->orderBy('factory_products.number_of_heads', 'ASC')
            ->orderBy('factory_products.weight_per_number_of_heads', 'ASC')
            ->orderBy('factory_products.input_group', 'ASC')
            ->orderBy('factory_products.number_of_cases', 'ASC')
            ->orderBy('orders.factory_product_sequence_number', 'ASC')
            ->orderBy('orders.delivery_destination_code', 'ASC');

        if ($params['display_term'] === 'date') {
            $query
                ->addSelect([
                    'orders.shipping_date',
                    'orders.order_quantity',
                    'orders.product_weight',
                    $this->db->raw(
                        sprintf(
                            '(CASE WHEN orders.slip_status_type = %d THEN 1 ELSE 0 END) AS is_temporary_order',
                            SlipStatusType::TEMP_ORDER
                        )
                    )
                ])
                ->whereBetween('orders.shipping_date', [
                    $shipping_date_term['from']->subDay()->toDateString(),
                    $shipping_date_term['to']->toDateString()
                ])
                ->orderBy('orders.shipping_date', 'ASC');
        }
        if ($params['display_term'] === 'month') {
            $query
                ->addSelect(
                    $this->db->raw("DATE_FORMAT((orders.shipping_date - INTERVAL 1 DAY), '%Y%m') AS shipping_month"),
                    $this->db->raw('SUM(orders.order_quantity) AS order_quantity'),
                    $this->db->raw('SUM(orders.product_weight) AS product_weight')
                )
                ->whereBetween('orders.shipping_date', [
                    $shipping_date_term['from']->toDateString(),
                    $shipping_date_term['to']->toDateString()
                ])
                ->groupBy(
                    'orders.factory_code',
                    'orders.factory_product_sequence_number',
                    'orders.delivery_destination_code',
                    $this->db->raw("DATE_FORMAT((orders.shipping_date - INTERVAL 1 DAY), '%Y%m')")
                )
                ->orderBy('shipping_month', 'ASC');
        }

        if ($factory_code = $params['factory_code'] ?? null) {
            $sub_query = $this->db->table('delivery_warehouses')
                ->select(['delivery_destination_code', 'warehouse_code', 'delivery_lead_time', 'shipment_lead_time'])
                ->where('warehouse_code', function ($query) use ($factory_code) {
                    $query->select('warehouse_code')
                        ->from('factory_warehouses')
                        ->whereRaw("factory_code = '{$factory_code}'")
                        ->orderBy('priority', 'ASC')
                        ->limit(1);
                })
                ->toSql();

            $query
                ->addSelect([
                    'delivery_warehouses.warehouse_code',
                    'delivery_warehouses.delivery_lead_time',
                    'delivery_warehouses.shipment_lead_time'
                ])
                ->leftJoin(
                    $this->db->raw("({$sub_query}) AS delivery_warehouses"),
                    'delivery_warehouses.delivery_destination_code',
                    '=',
                    'delivery_destinations.delivery_destination_code'
                );

            if ($params['display_term'] === 'month') {
                $query->groupBy('delivery_warehouses.warehouse_code');
            }
        }

        return $query->get();
    }

    /**
     * 出荷日を条件に未発送の注文情報を取得
     *
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Master\Species $species
     * @param  array $shipping_dates
     * @param  array $factory_product_sequence_numbers
     * @param  \App\Models\Master\Warehouse $excepted_warehouse
     * @return \App\Models\Order\Collections\OrderCollection
     */
    public function getNotDeliveredOrdersByShippingDate(
        Factory $factory,
        Species $species,
        array $shipping_dates,
        array $factory_product_sequence_numbers,
        ?Warehouse $excepted_warehouse
    ): OrderCollection {
        return $this->model
            ->select([
                'orders.order_number',
                'orders.factory_code',
                'products.species_code',
                'orders.delivery_destination_code',
                'orders.factory_product_sequence_number',
                'factory_products.number_of_heads',
                'factory_products.weight_per_number_of_heads',
                'factory_products.input_group',
                'factory_products.number_of_cases',
                'orders.order_quantity',
                $this->db->raw("DATE_FORMAT(orders.shipping_date, '%Y/%m/%d') AS shipping_date"),
                $this->db->raw("DATE_FORMAT(orders.delivery_date, '%Y/%m/%d') AS delivery_date")
            ])
            ->join('factory_products', function ($join) {
                $join->on('factory_products.factory_code', '=', 'orders.factory_code')
                    ->on('factory_products.sequence_number', '=', 'orders.factory_product_sequence_number');
            })
            ->join('products', 'products.product_code', '=', 'factory_products.product_code')
            ->where('orders.factory_code', $factory->factory_code)
            ->whereIn('orders.factory_product_sequence_number', $factory_product_sequence_numbers)
            ->where('products.species_code', $species->species_code)
            ->whereBetween('orders.shipping_date', [
                head($shipping_dates)->format('Y-m-d'),
                last($shipping_dates)->format('Y-m-d')
            ])
            ->where(function ($query) use ($factory, $species, $excepted_warehouse) {
                if (! is_null($excepted_warehouse)) {
                    $query->whereNotIn(
                        'orders.order_number',
                        function ($query) use ($factory, $species, $excepted_warehouse) {
                            $query->select('order_number')
                                ->from('product_allocations')
                                ->where('factory_code', $factory->factory_code)
                                ->where('species_code', $species->species_code)
                                ->where('warehouse_code', '<>', $excepted_warehouse->warehouse_code);
                        }
                    );
                }
            })
            ->where('orders.process_class', '<>', ProcessClass::CANCEL_PROCESS)
            ->where('orders.slip_type', SlipType::NORMAL_SLIP)
            ->where('orders.slip_status_type', '<>', SlipStatusType::RELATION_TEMP_ORDER)
            ->where('orders.factory_cancel_flag', false)
            ->whereNull('orders.fixed_shipping_at')
            ->orderBy('orders.order_number', 'ASC')
            ->get();
    }

    /**
     * 製品引当数とともに注文情報を取得
     *
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Master\Species $species
     * @param  array $shipping_dates
     * @param  array $packaging_style
     * @param  \App\Models\Master\Warehouse $warehouse
     * @return \App\Models\Order\Collections\OrderCollection
     */
    public function getOrdersWithAllocatedProducts(
        Factory $factory,
        Species $species,
        array $shipping_dates,
        array $packaging_style,
        Warehouse $warehouse
    ): OrderCollection {
        return $this->model
            ->select([
                'orders.order_number',
                'orders.factory_code',
                'orders.factory_product_sequence_number',
                'factory_products.factory_product_abbreviation',
                'factory_products.number_of_cases',
                'factory_products.unit',
                'orders.delivery_destination_code',
                'delivery_destinations.delivery_destination_abbreviation',
                'delivery_warehouses.delivery_lead_time',
                $this->db->raw("DATE_FORMAT(orders.delivery_date, '%Y/%m/%d') AS delivery_date"),
                'orders.order_quantity',
                'orders.shipping_date',
                'orders.fixed_shipping_at',
                $this->db->raw("DATE_FORMAT(product_allocations.harvesting_date, '%Y/%m/%d') AS harvesting_date"),
                'product_allocations.warehouse_code',
                $this->db->raw('COALESCE(product_allocations.allocation_quantity, 0) AS allocation_quantity')
            ])
            ->join('factory_products', function ($join) {
                $join->on('factory_products.factory_code', '=', 'orders.factory_code')
                    ->on('factory_products.sequence_number', '=', 'orders.factory_product_sequence_number');
            })
            ->join('products', 'products.product_code', '=', 'factory_products.product_code')
            ->join(
                'delivery_destinations',
                'delivery_destinations.delivery_destination_code',
                '=',
                'orders.delivery_destination_code'
            )
            ->leftJoin('delivery_warehouses', function ($join) use ($warehouse) {
                $join->on(
                    'delivery_warehouses.delivery_destination_code',
                    '=',
                    'delivery_destinations.delivery_destination_code'
                )
                    ->where('delivery_warehouses.warehouse_code', $warehouse->warehouse_code);
            })
            ->leftJoin('product_allocations', function ($join) use ($factory, $species) {
                $join->on('product_allocations.order_number', '=', 'orders.order_number')
                    ->where('product_allocations.factory_code', $factory->factory_code)
                    ->where('product_allocations.species_code', $species->species_code);
            })
            ->where('orders.factory_code', $factory->factory_code)
            ->where('products.species_code', $species->species_code)
            ->whereBetween('orders.shipping_date', [
                head($shipping_dates)->format('Y-m-d'),
                last($shipping_dates)->format('Y-m-d')
            ])
            ->where('orders.process_class', '<>', ProcessClass::CANCEL_PROCESS)
            ->where('orders.slip_type', SlipType::NORMAL_SLIP)
            ->where('orders.slip_status_type', '<>', SlipStatusType::RELATION_TEMP_ORDER)
            ->where('orders.factory_cancel_flag', false)
            ->where('factory_products.number_of_heads', $packaging_style['number_of_heads'] ?? null)
            ->where(
                'factory_products.weight_per_number_of_heads',
                $packaging_style['weight_per_number_of_heads'] ?? null
            )
            ->where('factory_products.input_group', $packaging_style['input_group'] ?? null)
            ->orderBy('orders.order_number', 'ASC')
            ->orderBy('product_allocations.harvesting_date', 'ASC')
            ->get();
    }

    /**
     * BASE+の注文番号で注文情報を取得
     *
     * @param  string $base_plus_order_number
     * @param  string $base_plus_order_chapter_number
     * @return \App\Models\Order\Order
     */
    public function getOrderByBasePlusOrderNumber(
        string $base_plus_order_number,
        string $base_plus_order_chapter_number
    ): ?Order {
        $product_allocation_query = $this->db->table('product_allocations')
            ->select([
                'order_number',
                $this->db->raw('SUM(allocation_quantity) AS allocation_quantity')
            ])
            ->groupBy('order_number');

        return $this->model
            ->select([
                'orders.order_number',
                'orders.creating_type',
                'orders.slip_type',
                'orders.order_quantity',
                $this->db->raw('COALESCE(product_allocations.allocation_quantity, 0) AS allocation_quantity'),
                'orders.factory_product_sequence_number',
                'factory_products.number_of_cases',
                'orders.fixed_shipping_at',
                'orders.supplier_flag',
                'orders.product_code',
                'orders.delivery_destination_code'
            ])
            ->join('factory_products', function ($join) {
                $join->on('orders.factory_code', '=', 'factory_products.factory_code')
                    ->on('orders.factory_product_sequence_number', '=', 'factory_products.sequence_number');
            })
            ->leftJoin(
                $this->db->raw("({$product_allocation_query->toSql()}) AS product_allocations"),
                function ($join) {
                    $join->on('orders.order_number', '=', 'product_allocations.order_number');
                }
            )
            ->where('orders.base_plus_order_number', $base_plus_order_number)
            ->where('orders.base_plus_order_chapter_number', $base_plus_order_chapter_number)
            ->where('orders.slip_status_type', '<>', SlipStatusType::RELATION_TEMP_ORDER)
            ->first();
    }

    /**
     * 未紐づけ状態の注文情報を取得
     *
     * @param  int $creating_type
     * @param  int $slip_status_type
     * @return \App\Models\Order\Collections\OrderCollection
     */
    public function getNotRelatedOrders(int $creating_type, int $slip_status_type): OrderCollection
    {
        $product_allocation_query = $this->db->table('product_allocations')
            ->select(['order_number', $this->db->raw('SUM(allocation_quantity) AS allocation_quantity')])
            ->groupBy('order_number');

        return $this->model
            ->select([
                'orders.order_number',
                'orders.product_code',
                'orders.delivery_date',
                'orders.end_user_code',
                'orders.order_quantity',
                'orders.order_unit',
                'orders.buyer_remark',
                'orders.delivery_destination_code',
                'orders.factory_code',
                'orders.factory_product_sequence_number',
                'orders.fixed_shipping_at',
                'factory_products.number_of_cases',
                $this->db->raw('COALESCE(product_allocations.allocation_quantity, 0) AS allocation_quantity')
            ])
            ->join('factory_products', function ($join) {
                $join->on('factory_products.factory_code', '=', 'orders.factory_code')
                    ->on('factory_products.sequence_number', '=', 'orders.factory_product_sequence_number');
            })
            ->leftJoin(
                $this->db->raw("({$product_allocation_query->toSql()}) AS product_allocations"),
                'product_allocations.order_number',
                '=',
                'orders.order_number'
            )
            ->where('orders.creating_type', $creating_type)
            ->where('orders.slip_type', SlipType::NORMAL_SLIP)
            ->where('orders.slip_status_type', $slip_status_type)
            ->where('orders.related_order_status_type', RelatedOrderStatusType::UN_RELATED)
            ->where('orders.factory_cancel_flag', false)
            ->where('orders.process_class', '<>', ProcessClass::CANCEL_PROCESS)
            ->orderBy('orders.delivery_date', 'DESC')
            ->orderBy('orders.order_number', 'ASC')
            ->get();
    }

    /**
     * 出荷可能な注文データを取得
     *
     * @param  array $params
     * @param  array $order_numbers
     * @return \App\Models\Order\Collections\OrderCollection
     */
    public function getShippableOrders($params): OrderCollection
    {
        $invoice_receipt_infomation_log_query = $this->db->table('invoice_receipt_infomation_logs')
            ->select([
                'order_number',
                $this->db->raw('COUNT(order_number) AS count_of_invoice_receipt_infomation_log')
            ])
            ->groupBy('order_number');

        $shipment_infomation_logs_query = $this->db->table('shipment_infomation_logs')
            ->select([
                'order_number',
                $this->db->raw('COUNT(order_number) AS count_of_shipment_infomation_logs')
            ])
            ->groupBy('order_number');

        $product_allocation_query = $this->db->table('product_allocations')
            ->select([
                'order_number',
                $this->db->raw('SUM(allocation_quantity) AS allocation_quantity')
            ])
            ->groupBy('order_number');

        return $this->model
            ->select([
                'orders.order_number',
                'orders.end_user_order_number',
                'orders.slip_status_type',
                $this->db->raw("DATE_FORMAT(orders.shipping_date, '%Y/%m/%d') AS shipping_date"),
                $this->db->raw("DATE_FORMAT(orders.delivery_date, '%Y/%m/%d') AS delivery_date"),
                'orders.end_user_code',
                'orders.delivery_destination_code',
                'delivery_destinations.delivery_destination_abbreviation',
                'orders.product_name',
                'orders.order_quantity',
                'orders.place_order_unit_code',
                'orders.order_amount',
                'orders.currency_code',
                'currencies.order_unit_decimals',
                'currencies.order_amount_decimals',
                'orders.fixed_shipping_at'
            ])
            ->join(
                'delivery_destinations',
                'delivery_destinations.delivery_destination_code',
                '=',
                'orders.delivery_destination_code'
            )
            ->join('currencies', 'currencies.currency_code', '=', 'orders.currency_code')
            ->join('factory_products', function ($join) {
                $join->on('factory_products.factory_code', '=', 'orders.factory_code')
                    ->on('factory_products.sequence_number', '=', 'orders.factory_product_sequence_number');
            })
            ->leftJoin(
                $this->db->raw("({$invoice_receipt_infomation_log_query->toSql()}) AS invoice_receipt_infomation_logs"),
                function ($join) {
                    $join->on('invoice_receipt_infomation_logs.order_number', '=', 'orders.order_number');
                }
            )
            ->leftJoin(
                $this->db->raw("({$shipment_infomation_logs_query->toSql()}) AS shipment_infomation_logs"),
                function ($join) {
                    $join->on('shipment_infomation_logs.order_number', '=', 'orders.order_number');
                }
            )
            ->join(
                $this->db->raw("({$product_allocation_query->toSql()}) AS product_allocations"),
                function ($join) {
                    $join->on('product_allocations.order_number', '=', 'orders.order_number')
                        ->whereRaw(
                            'product_allocations.allocation_quantity = '.
                            '(orders.order_quantity * factory_products.number_of_cases)'
                        );
                }
            )
            ->where('orders.factory_code', $params['factory_code'])
            ->where('orders.customer_code', $params['customer_code'])
            ->where('orders.process_class', '<>', ProcessClass::CANCEL_PROCESS)
            ->where('orders.slip_type', SlipType::NORMAL_SLIP)
            ->where('orders.slip_status_type', '<>', SlipStatusType::RELATION_TEMP_ORDER)
            ->where('orders.factory_cancel_flag', false)
            ->where(function ($query) {
                $query
                    ->whereRaw(
                        'COALESCE(invoice_receipt_infomation_logs.count_of_invoice_receipt_infomation_log, 0) > 0'
                    )
                    ->orWhereRaw(
                        'COALESCE(shipment_infomation_logs.count_of_shipment_infomation_logs, 0) > 0'
                    );
            })
            ->where(function ($query) use ($params) {
                if ($end_user_code = $params['end_user_code'] ?: null) {
                    $query->where('orders.end_user_code', $end_user_code);
                }
            })
            ->where(function ($query) use ($params) {
                if ($delivery_destination_code = $params['delivery_destination_code'] ?: null) {
                    $query->where('orders.delivery_destination_code', $delivery_destination_code);
                }
            })
            ->where(function ($query) use ($params) {
                if ($shipping_date_from = $params['shipping_date_from'] ?: null) {
                    $query->where('orders.shipping_date', '>=', $shipping_date_from);
                }
            })
            ->where(function ($query) use ($params) {
                if ($shipping_date_to = $params['shipping_date_to'] ?: null) {
                    $query->where('orders.shipping_date', '<=', $shipping_date_to);
                }
            })
            ->where(function ($query) use ($params) {
                if ($delivery_date_from = $params['delivery_date_from'] ?: null) {
                    $query->where('orders.delivery_date', '>=', $delivery_date_from);
                }
            })
            ->where(function ($query) use ($params) {
                if ($delivery_date_to = $params['delivery_date_to'] ?: null) {
                    $query->where('orders.delivery_date', '<=', $delivery_date_to);
                }
            })
            ->where(function ($query) use ($params) {
                if ($order_number = $params['order_number'] ?: null) {
                    $query->where('orders.order_number', $order_number);
                }
            })
            ->where(function ($query) use ($params) {
                if ($base_plus_order_number = $params['base_plus_order_number'] ?: null) {
                    $query->where('orders.base_plus_order_number', $base_plus_order_number);
                }
            })
            ->where(function ($query) use ($params) {
                if ($base_plus_order_chapter_number = $params['base_plus_order_chapter_number'] ?: null) {
                    $query->where('orders.base_plus_order_chapter_number', $base_plus_order_chapter_number);
                }
            })
            ->where(function ($query) use ($params) {
                $shipment_status = ! is_null($params['shipment_status'] ?? null) ?
                    (int)$params['shipment_status'] :
                    null;
                if ($shipment_status === ShipmentStatus::UNSHIPPED) {
                    $query->whereNull('orders.fixed_shipping_at');
                }
                if ($shipment_status === ShipmentStatus::SHIPPED) {
                    $query->whereNotNull('orders.fixed_shipping_at');
                }
            })
            ->get();
    }

    /**
     * 請求書出力対象の注文情報を取得
     *
     * @param  array $params
     * @param  \App\Models\Master\Customer $customer
     * @param  \App\ValueObjects\Date\DeliveryDate $delivery_month
     * @param  \App\Models\Shipment\Invoice $invoice
     * @return \App\Models\Order\Collections\OrderCollection
     */
    public function getOrdersThatWillOutputOnInvoice(
        array $params,
        Customer $customer,
        DeliveryDate $delivery_month,
        ?Invoice $invoice
    ): OrderCollection {
        return $this->model
            ->select([
                'orders.order_number',
                'orders.base_plus_order_number',
                'orders.base_plus_order_chapter_number',
                'orders.product_name',
                'orders.delivery_date',
                'orders.end_user_code',
                $this->db->raw('(orders.order_quantity - COALESCE(returned_products.quantity, 0)) AS order_quantity'),
                'orders.delivery_destination_code',
                'delivery_destinations.delivery_destination_name',
                'orders.place_order_unit_code',
                'orders.order_unit',
                $this->db->raw(
                    '(orders.order_amount - COALESCE(returned_products.unit_price * returned_products.quantity, 0)) '.
                    'AS order_amount'
                ),
                'orders.order_message',
                'orders.currency_code',
                'orders.product_weight',
                'orders.shipping_date'
            ])
            ->join(
                'delivery_destinations',
                'orders.delivery_destination_code',
                '=',
                'delivery_destinations.delivery_destination_code'
            )
            ->leftJoin('returned_products', 'returned_products.order_number', '=', 'orders.order_number')
            ->where('orders.factory_code', $params['factory_code'])
            ->where('orders.customer_code', $customer->customer_code)
            ->where(function ($query) use ($params) {
                if ($end_user_code = $params['end_user_code'] ?? null) {
                    $query->where('orders.end_user_code', $end_user_code);
                }
            })
            ->where('orders.process_class', '<>', ProcessClass::CANCEL_PROCESS)
            ->where('orders.slip_status_type', '<>', SlipStatusType::RELATION_TEMP_ORDER)
            ->where('orders.factory_cancel_flag', false)
            ->whereNotNull('orders.fixed_shipping_at')
            ->where(function ($query) use ($customer, $delivery_month, $invoice) {
                if (! is_null($invoice)) {
                    $query->where('orders.invoice_number', $invoice->invoice_number);
                }
                if (is_null($invoice)) {
                    $query->whereBetween(
                        'orders.delivery_date',
                        $customer->getDeliveryDateTermOfInvoice($delivery_month)
                    )
                        ->orWhere(function ($query) use ($customer, $delivery_month) {
                            $query->whereNull('invoice_number')
                                ->where(
                                    'delivery_date',
                                    '<',
                                    $customer->getFirstOfDeliveryDateOfInvoice($delivery_month)->format('Y-m-d')
                                );
                        });
                }
            })
            ->orderBy('orders.end_user_code', 'ASC')
            ->orderBy('orders.delivery_date', 'ASC')
            ->orderBy('orders.base_plus_order_number', 'ASC')
            ->orderBy('orders.base_plus_order_chapter_number', 'ASC')
            ->get();
    }

    /**
     * 在庫棚卸後の出荷数量の取得
     *
     * @param  \App\Models\Stock\StocktakingDetail $stocktaking_detail
     * @param  array $dates
     * @return \App\Models\Order\Collections\OrderCollection
     */
    public function getShippedStocksPerStockStyle(StocktakingDetail $stocktaking_detail, array $dates): OrderCollection
    {
        $product_allocation_query = $this->db->table('product_allocations')
            ->select(['order_number', $this->db->raw('SUM(allocation_quantity) AS allocation_quantity')])
            ->where('factory_code', $stocktaking_detail->factory_code)
            ->where('species_code', $stocktaking_detail->species_code)
            ->where('warehouse_code', $stocktaking_detail->warehouse_code)
            ->groupBy('order_number');

        return $this->model
            ->select([
                'orders.printing_shipping_date AS shipping_date',
                $this->db->raw('SUM(product_allocations.allocation_quantity) AS shipped_quantity')
            ])
            ->join(
                $this->db->raw("({$product_allocation_query->toSql()}) AS product_allocations"),
                'product_allocations.order_number',
                '=',
                'orders.order_number'
            )
            ->setBindings($product_allocation_query->getBindings())
            ->join('factory_products', function ($join) {
                $join->on('orders.factory_code', '=', 'factory_products.factory_code')
                    ->on('orders.factory_product_sequence_number', '=', 'factory_products.sequence_number');
            })
            ->where('orders.factory_code', $stocktaking_detail->factory_code)
            ->where('factory_products.number_of_heads', $stocktaking_detail->number_of_heads)
            ->where('factory_products.weight_per_number_of_heads', $stocktaking_detail->weight_per_number_of_heads)
            ->where('factory_products.input_group', $stocktaking_detail->input_group)
            ->where('factory_products.number_of_cases', $stocktaking_detail->number_of_cases)
            ->whereBetween('orders.printing_shipping_date', [
                head($dates)->toDateString(),
                last($dates)->toDateString()
            ])
            ->whereNotNull('orders.fixed_shipping_at')
            ->groupBy('orders.printing_shipping_date')
            ->get();
    }

    /**
     * 請求書番号の更新
     *
     * @param  \App\Models\Shipment\Invoice $invoice
     * @param  \App\Models\Master\Customer $customer
     * @return \App\Models\Order\Order
     */
    public function updateInvoiceNumber(Invoice $invoice, Customer $customer): Order
    {
        $this->model
            ->where('factory_code', $invoice->factory_code)
            ->where('customer_code', $invoice->customer_code)
            ->where(function ($query) use ($invoice, $customer) {
                $query->whereBetween(
                    'delivery_date',
                    $customer->getDeliveryDateTermOfInvoice($invoice->getDeliveryMonth())
                )
                    ->orWhere(function ($query) use ($invoice, $customer) {
                        $query->whereNull('invoice_number')
                            ->where(
                                'delivery_date',
                                '<',
                                $customer
                                    ->getFirstOfDeliveryDateOfInvoice($invoice->getDeliveryMonth())
                                    ->format('Y-m-d')
                            );
                    });
            })
            ->where('process_class', '<>', ProcessClass::CANCEL_PROCESS)
            ->where('slip_status_type', '<>', SlipStatusType::RELATION_TEMP_ORDER)
            ->where('factory_cancel_flag', false)
            ->whereNotNull('fixed_shipping_at')
            ->update(['invoice_number' => $invoice->invoice_number]);

        $tax_query = 'SELECT tax.application_started_on, '.
            "COALESCE(DATE_SUB(sub_tax.application_started_on, INTERVAL 1 DAY), '2099-12-31') ".
            'AS application_ended_on, tax.tax_rate '.
            'FROM tax '.
            'LEFT JOIN tax sub_tax ON sub_tax.application_started_on = ('.
            'SELECT MIN(application_started_on) AS application_started_on '.
            'FROM tax sub_tax2 '.
            'WHERE tax.application_started_on < sub_tax2.application_started_on)';

        return $this->model
            ->select([
                $this->db->raw('COUNT(orders.order_number) AS order_quantity'),
                $this->db->raw(
                    'SUM(orders.order_amount - '.
                    'COALESCE((returned_products.unit_price * returned_products.quantity), 0) + '.
                    sprintf(
                        '%s((orders.order_amount - '.
                        'COALESCE((returned_products.unit_price * returned_products.quantity), 0)) * taxes.tax_rate)',
                        $customer->getRoundingSql()
                    ).
                    ') AS order_amount'
                )
            ])
            ->leftJoin('returned_products', 'returned_products.order_number', '=', 'orders.order_number')
            ->join($this->db->raw("({$tax_query}) AS taxes"), function ($join) {
                $join->on('taxes.application_started_on', '<=', 'orders.shipping_date')
                    ->on('taxes.application_ended_on', '>=', 'orders.shipping_date');
            })
            ->where('invoice_number', $invoice->invoice_number)
            ->groupBy('invoice_number')
            ->first();
    }

    /**
     * 請求書番号を未採番の状態に戻す
     *
     * @param  \App\Models\Shipment\Invoice $invoice
     * @return void
     */
    public function cancelInvoiceNumber(Invoice $invoice): void
    {
        $this->model->where('invoice_number', $invoice->invoice_number)
            ->update(['invoice_number' => null]);
    }

    /**
     * 出荷確定した注文のうち、注文情報が未連携のものを取得
     *
     * @return \App\Models\Order\Collections\OrderCollection
     */
    public function getOrdersThatFixedShipping(): OrderCollection
    {
        return $this->model
            ->select('*')
            ->whereRaw('delivery_date <= CURRENT_DATE')
            ->where('process_class', '<>', ProcessClass::CANCEL_PROCESS)
            ->where('creating_type', CreatingType::BASE_PLUS_LINKED)
            ->where('slip_type', SlipType::NORMAL_SLIP)
            ->where('slip_status_type', SlipStatusType::FIXED_ORDER)
            ->where('factory_cancel_flag', false)
            ->whereNotNull('fixed_shipping_at')
            ->where('fixed_shipping_sharing_flag', false)
            ->get();
    }
}

<?php

declare(strict_types=1);

namespace App\Services\Order;

use Illuminate\Database\Connection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Exceptions\PageOverException;
use App\Models\Master\Customer;
use App\Models\Master\DeliveryDestination;
use App\Models\Master\Factory;
use App\Models\Master\FactoryProduct;
use App\Models\Order\Order;
use App\Models\Order\Collections\OrderCollection;
use App\Repositories\Order\OrderRepository;
use App\Repositories\Order\OrderHistoryRepository;
use App\Repositories\Order\AssignNumberRepository;
use App\ValueObjects\Date\DeliveryDate;
use App\ValueObjects\Enum\CreatingType;
use App\ValueObjects\Enum\SlipType;
use App\ValueObjects\Enum\ProcessClass;

class OrderInputService
{
    /**
     * @var \Illuminate\Database\Connection
     */
    private $db;

    /**
     * @var \App\Repositories\Order\OrderRepository
     */
    private $order_repo;

    /**
     * @var \App\Repositories\Order\OrderHistoryRepository
     */
    private $order_history_repo;

    /**
     * @var \App\Repositories\Order\AssignNumberRepository
     */
    private $assign_number_repo;

    /**
     * @param  \Illuminate\Database\Connection $db
     * @param  \App\Repositories\Order\OrderRepository $order_repositry
     * @param  \App\Repositories\Order\OrderHistoryRepository $order_history_repositry
     * @param  \App\Repositories\Order\AssignNumberRepository $assign_number_repositry
     * @return void
     */
    public function __construct(
        Connection $db,
        OrderRepository $order_repositry,
        OrderHistoryRepository $order_history_repositry,
        AssignNumberRepository $assign_number_repositry
    ) {
        $this->db = $db;
        $this->order_repo = $order_repositry;
        $this->order_history_repo = $order_history_repositry;
        $this->assign_number_repo = $assign_number_repositry;
    }

    /**
     * 手動で作成された注文情報を条件に応じて検索
     *
     * @param  array $params 検索条件
     * @param  int $page 表示ページ
     * @return \Illuminate\Pagination\LengthAwarePaginator
     * @throws \App\Exceptions\PageOverException
     */
    public function searchManualCreatedOrders(array $params, int $page): LengthAwarePaginator
    {
        $orders = $this->order_repo->searchManualCreatedOrders($params);
        if ($page > $orders->lastPage() && $orders->lastPage() !== 0) {
            throw new PageOverException('target page does not exist.');
        }

        return $orders;
    }

    /**
     * 注文情報の手動登録
     *
     * @param  array $params
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Master\Customer $customer
     * @param  \App\Models\Master\DeliveryDestination $delivery_destination
     * @param  \App\Models\Master\FactoryProduct $factory_product
     * @return \App\Models\Order\Order $order
     */
    public function createOrder(
        array $params,
        Factory $factory,
        Customer $customer,
        DeliveryDestination $delivery_destination,
        FactoryProduct $factory_product
    ) {
        $delivery_date = DeliveryDate::parse($params['delivery_date']);
        $shipping_date = $delivery_date->getShippingDate($delivery_destination, $factory);

        $product_weight = $params['order_quantity'] *
            $factory_product->weight_per_number_of_heads *
            $factory_product->number_of_cases;

        $order = [
            'base_plus_order_number' => $params['base_plus_order_number'],
            'base_plus_order_chapter_number' => $params['base_plus_order_chapter_number'],
            'received_date' => $params['received_date'],
            'delivery_date' => $delivery_date->value(),
            'product_code' => $factory_product->product_code,
            'product_name' => $factory_product->product->product_name,
            'end_user_code' => $params['end_user_code'],
            'order_quantity' => $params['order_quantity'],
            'place_order_unit_code' => $factory_product->unit,
            'order_amount' => $params['order_amount'],
            'order_unit' => $params['order_unit'],
            'order_message' => $params['order_message'],
            'delivery_destination_code' => $delivery_destination->delivery_destination_code,
            'factory_product_sequence_number' => $factory_product->sequence_number,
            'product_weight' => $product_weight,
            'factory_code' => $factory->factory_code,
            'customer_code' => $customer->customer_code,
            'currency_code' => $params['currency_code'],
            'end_user_order_number' => $params['end_user_order_number'],
            'recived_order_unit' => $params['received_order_unit'],
            'customer_recived_order_unit' => $params['customer_received_order_unit'],
            'shipping_date' => $shipping_date->value(),
            'printing_shipping_date' => $shipping_date->getPrintingShippingDate($factory)->value(),
            'creating_type' => CreatingType::MANUAL_CREATED,
            'slip_type' => SlipType::NORMAL_SLIP,
            'slip_status_type' => $customer->getSlipStatusType(),
            'transport_company_code' => $delivery_destination->transport_company_code,
            'collection_time_sequence_number' => $delivery_destination->collection_time_sequence_number
        ];

        return $this->db->transaction(function () use ($order, $factory) {
            $order['order_number'] = $this->assign_number_repo->getAssignedNumber('orders', $factory->symbolic_code);
            $order = $this->order_repo->create($order);

            return $order;
        });
    }

    /**
     * 手動作成した注文情報の修正
     *
     * @param  array $params
     * @param  \App\Models\Order\Order $order
     * @param  \App\Models\Master\DeliveryDestination $delivery_destination
     * @param  \App\Models\Master\FactoryProduct $factory_product
     * @return void
     */
    public function updateOrder(
        array $params,
        Order $order,
        DeliveryDestination $delivery_destination,
        FactoryProduct $factory_product
    ): void {
        $delivery_date = DeliveryDate::parse($params['delivery_date']);
        $shipping_date = $delivery_date->getShippingDate($delivery_destination, $factory_product->factory);

        $product_weight = $params['order_quantity'] *
            $factory_product->weight_per_number_of_heads *
            $factory_product->number_of_cases;

        $params = [
            'base_plus_order_number' => $params['base_plus_order_number'],
            'base_plus_order_chapter_number' => $params['base_plus_order_chapter_number'],
            'delivery_date' => $delivery_date->value(),
            'product_code' => $factory_product->product_code,
            'product_name' => $factory_product->product->product_name,
            'order_quantity' => $params['order_quantity'],
            'place_order_unit_code' => $factory_product->unit,
            'order_amount' => $params['order_amount'],
            'order_unit' => $params['order_unit'],
            'order_message' => $params['order_message'],
            'factory_product_sequence_number' => $factory_product->sequence_number,
            'product_weight' => $product_weight,
            'currency_code' => $params['currency_code'],
            'end_user_order_number' => $params['end_user_order_number'],
            'recived_order_unit' => $params['received_order_unit'],
            'customer_recived_order_unit' => $params['customer_received_order_unit'],
            'shipping_date' => $shipping_date->value(),
            'printing_shipping_date' => $shipping_date->getPrintingShippingDate($factory_product->factory)->value(),
            'process_class' => ProcessClass::CHANGE_PROCESS,
            'updated_at' => $params['updated_at'],
        ];

        $this->db->transaction(function () use ($order, $params) {
            $this->order_history_repo->createByOrder($order);
            $this->order_repo->update($order, $params);
        });
    }

    /**
     * 注文データの削除
     *
     * @param  \App\Models\Order\Order $order
     * @return void
     */
    public function deleteOrder(Order $order): void
    {
        $this->db->transaction(function () use ($order) {
            $order->order_histories->each(function ($oh) {
                $oh->delete();
            });

            $order->delete();
        });
    }
}

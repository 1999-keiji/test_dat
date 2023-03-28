<?php

declare(strict_types=1);

namespace App\Services\Order;

use Illuminate\Auth\AuthManager;
use Illuminate\Database\Connection;
use Illuminate\Pagination\LengthAwarePaginator;
use Cake\Chronos\Chronos;
use Maatwebsite\Excel\Facades\Excel;
use App\Exceptions\DisabledToLinkOrderException;
use App\Exceptions\OptimisticLockException;
use App\Exceptions\PageOverException;
use App\Models\Master\Customer;
use App\Models\Master\DeliveryDestination;
use App\Models\Master\Factory;
use App\Models\Master\FactoryProduct;
use App\Models\Order\Order;
use App\Models\Order\Collections\OrderCollection;
use App\Models\Shipment\ProductAllocation;
use App\Repositories\Master\EndUserRepository;
use App\Repositories\Order\AssignNumberRepository;
use App\Repositories\Order\OrderRepository;
use App\Repositories\Order\OrderHistoryRepository;
use App\Repositories\Order\RelatedOrderRepository;
use App\Repositories\Stock\StockRepository;
use App\ValueObjects\Date\DeliveryDate;
use App\ValueObjects\Date\ShippingDate;
use App\ValueObjects\Enum\CreatingType;
use App\ValueObjects\Enum\ProcessClass;
use App\ValueObjects\Enum\RelatedOrderStatusType;
use App\ValueObjects\Enum\SlipType;
use App\ValueObjects\Enum\SlipStatusType;

class OrderListService
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
     * @var \App\Repositories\Order\OrderRepository
     */
    private $order_repo;

    /**
     * @var \App\Repositories\Order\OrderHistoryRepository
     */
    private $order_history_repo;

    /**
     * @var \App\Repositories\Order\RelatedOrderRepository
     */
    private $related_order_repo;

    /**
     * @var \App\Repositories\Order\AssignNumberRepository
     */
    private $assign_number_repo;

    /**
     * @var \App\Repositories\Stock\StockRepository
     */
    private $stock_repo;

    /**
     * @var \App\Repositories\Master\EndUserRepository
     */
    private $end_user_repo;

    /**
     * @param  \Illuminate\Auth\AuthManager $auth
     * @param  \Illuminate\Database\Connection $db
     * @param  \App\Repositories\Order\OrderRepository $order_repo
     * @param  \App\Repositories\Order\OrderHistoryRepository $order_history_repo
     * @param  \App\Repositories\Order\RelatedOrderRepository $related_order_repo
     * @param  \App\Repositories\Order\AssignNumberRepository $assign_number_repo
     * @param  \App\Repositories\Master\ProductRepository $product_repo
     * @param  \App\Repositories\Stock\StockRepository $stock_repo
     * @param  \App\Repositories\Master\EndUserRepository $end_user_repo
     * @return void
     */
    public function __construct(
        AuthManager $auth,
        Connection $db,
        OrderRepository $order_repo,
        OrderHistoryRepository $order_history_repo,
        RelatedOrderRepository $related_order_repo,
        AssignNumberRepository $assign_number_repo,
        StockRepository $stock_repo,
        EndUserRepository $end_user_repo
    ) {
        $this->auth = $auth;
        $this->db = $db;
        $this->order_repo = $order_repo;
        $this->order_history_repo = $order_history_repo;
        $this->related_order_repo = $related_order_repo;
        $this->assign_number_repo = $assign_number_repo;
        $this->stock_repo = $stock_repo;
        $this->end_user_repo = $end_user_repo;
    }

    /**
     * 注文一覧 検索
     *
     * @param  array $params
     * @param  int $page
     * @param  array $order
     * @return \Illuminate\Pagination\LengthAwarePaginator $orders
     */
    public function searchOrders(array $params, int $page, array $order): LengthAwarePaginator
    {
        $orders = $this->order_repo->searchOrders($params, $order);
        if ($page > $orders->lastPage() && $orders->lastPage() !== 0) {
            throw new PageOverException('target page does not exist.');
        }

        $end_users = $this->end_user_repo->getCurrentApplicatedEndUsers($orders->pluck('end_user_code')->all());
        foreach ($orders as $o) {
            $o->end_user_abbreviation = $end_users->findByEndUserCode($o->end_user_code)->end_user_abbreviation ?? '';
            $o->formatted_order_unit = $o->formatOrderUnit();
            $o->formatted_order_amount = $o->formatOrderAmount();
            $o->had_been_shipped = $o->hadBeenShipped();
        }

        return $orders;
    }

    /**
     * 注文データの変更
     *
     * @param  array $params
     * @param  \App\Models\Order\Order $order
     * @param  \App\Models\Master\FactoryProduct $factory_product
     * @return void
     */
    public function updateOrder(array $params, Order $order, FactoryProduct $factory_product): void
    {
        $shipping_date = ShippingDate::parse($params['shipping_date']);
        $product_weight = $params['order_quantity'] *
            $factory_product->weight_per_number_of_heads *
            $factory_product->number_of_cases;

        $params = [
            'process_class' => ProcessClass::CHANGE_PROCESS,
            'end_user_code' => $params['end_user_code'],
            'delivery_destination_code' => $params['delivery_destination_code'],
            'received_date' => $params['received_date'],
            'delivery_date' => DeliveryDate::parse($params['delivery_date'])->format('Y-m-d'),
            'shipping_date' => $shipping_date->value(),
            'printing_shipping_date' => $shipping_date->getPrintingShippingDate($factory_product->factory)->value(),
            'product_code' => $factory_product->product_code,
            'product_name' => $params['product_name'],
            'supplier_product_name' => $params['supplier_product_name'] ?: '',
            'customer_product_name' => $params['customer_product_name'] ?: '',
            'order_quantity' => (int)($params['order_quantity']),
            'place_order_unit_code' => $params['place_order_unit_code'],
            'order_unit' => $params['order_unit'],
            'order_amount' => $params['order_amount'],
            'currency_code' => $params['currency_code'],
            'statement_delivery_price_display_class' => $params['statement_delivery_price_display_class'],
            'basis_for_recording_sales_class' => $params['basis_for_recording_sales_class'],
            'recived_order_unit' => $params['received_order_unit'] ?: 0,
            'customer_recived_order_unit' => $params['customer_received_order_unit'] ?: 0,
            'small_peace_of_peper_type_code' => $params['small_peace_of_peper_type_code'],
            'transport_company_code' => $params['transport_company_code'] ?? null,
            'collection_time_sequence_number' => $params['collection_time_sequence_number'] ?? null,
            'own_company_code' => $params['own_company_code'] ?: '',
            'organization_name' => $params['organization_name'] ?: '',
            'base_plus_end_user_code' => $params['base_plus_end_user_code'] ?: '',
            'customer_staff_name' => $params['customer_staff_name'] ?: '',
            'purchase_staff_name' => $params['purchase_staff_name'] ?: '',
            'place_order_work_staff_name' => $params['place_order_work_staff_name'] ?: '',
            'seller_name' => $params['seller_name'] ?: '',
            'order_message' => $params['order_message'] ?: '',
            'end_user_order_number' => $params['end_user_order_number'],
            'factory_product_sequence_number' => $factory_product->sequence_number,
            'product_weight' => $product_weight
        ];

        $params['slip_status_type'] = $order->getSlipStatusTypeOnManualUpdated($params);
        $this->db->transaction(function () use ($params, $order) {
            $this->order_history_repo->createByOrder($order);

            $order = $this->order_repo->update($order, $params);
            if ($order->getAllocationQuantity() > $order->getProductQuantityToAllocateFull()) {
                throw new OptimisticLockException('over allocation');
            }
        });
    }

    /**
     * 注文キャンセル
     *
     * @param  \App\Models\Order\Order $order
     * @return void
     */
    public function cancelOrder(Order $order): void
    {
        $this->db->transaction(function () use ($order) {
            $this->order_history_repo->createByOrder($order);

            $order->slip_status_type = SlipStatusType::TEMP_ORDER;
            $order->factory_cancel_flag = true;
            $order->save();
        });
    }

    /**
     * 注文一覧 Excel出力
     *
     * @param array $params
     * @param \App\Models\Master\Factory $factory
     * @param \App\Models\Master\Customer $customer
     */
    public function exportOrders(array $params, Factory $factory, Customer $customer)
    {
        $orders = $this->order_repo->searchOrdersToExport($params);

        $file_name = generate_file_name(config('constant.order.order_list.excel_file_name'));
        Excel::create($file_name, function ($excel) use ($factory, $customer, $orders, $params) {
            $config = config('constant.order.order_list');

            $excel->sheet($config['order_excel_list'], function ($sheet) use ($factory, $customer, $params, $orders) {
                $sheet->loadView('order.order_list.export')->with(compact('factory', 'customer', 'orders', 'params'));
            });

            $excel->sheet($config['output_condition'], function ($sheet) use ($factory, $customer, $params, $orders) {
                $sheet->loadView('order.order_list.export_output_condition')->with(
                    compact('factory', 'customer', 'params')
                );
            });

            $excel->setActiveSheetIndex(0);
        })
        ->export();
    }

    /**
     * API用 赤黒伝票登録
     *
     * @param  array $params
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Master\DeliveryDestination $delivery_destination
     * @return void
     */
    public function saveSlip(array $params, Factory $factory, DeliveryDestination $delivery_destination): void
    {
        $delivery_date = DeliveryDate::parse($params['delivery_date']);
        $shipping_date = $delivery_date->getShippingDate($delivery_destination, $factory);

        $order = [
            'received_date' => Chronos::now()->format('Y-m-d'),
            'delivery_date' => $delivery_date->value(),
            'product_name' => $params['product_name'],
            'end_user_code' => $params['end_user_code'],
            'order_quantity' => $params['order_quantity'],
            'order_amount' => $params['order_amount'],
            'order_unit' => $params['order_unit'],
            'order_message' => $params['order_message'] ?: '',
            'delivery_destination_code' => $delivery_destination->delivery_destination_code,
            'factory_code' => $factory->factory_code,
            'customer_code' => $params['customer_code'],
            'currency_code' => $params['currency_code'],
            'shipping_date' => $shipping_date->value(),
            'printing_shipping_date' => $shipping_date->getPrintingShippingDate($factory)->value(),
            'creating_type' => CreatingType::MANUAL_CREATED,
            'slip_type' => SlipType::CREDIT_SLIP,
            'slip_status_type' => SlipStatusType::FIXED_ORDER,
            'transport_company_code' => $delivery_destination->transport_company_code,
            'collection_time_sequence_number' => $delivery_destination->collection_time_sequence_number,
            'fixed_shipping_by' => $this->auth->id(),
            'fixed_shipping_at' => Chronos::now()
        ];

        $this->db->transaction(function () use ($order, $factory) {
            $order['order_number'] = $this->assign_number_repo->getAssignedNumber('orders', $factory->symbolic_code);
            $this->order_repo->create($order);
        });
    }

    /**
     * 紐づけ可能な確定注文を検索する
     *
     * @param  array $params
     * @return \App\Models\Order\Collections\OrderCollection
     */
    public function searchLinkableFixedOrders(array $params)
    {
        return $this->order_repo->searchLinkableFixedOrders($params)
            ->map(function ($o) {
                $o->formatted_order_unit = $o->formatOrderUnit();
                $o->formatted_order_amount = $o->formatOrderAmount();

                return $o;
            });
    }

    /**
     * 仮注文と確定注文の紐づけ
     *
     * @param  \App\Models\Order\Order $order
     * @param  array $order_number_list
     * @return void
     * @throws \App\Exceptions\OptimisticLockException
     * @throws \App\Exceptions\DisabledToLinkOrderException
     */
    public function linkOrders(Order $order, array $order_number_list): void
    {
        if (count($order_number_list) === 0) {
            return;
        }

        $this->db->transaction(function () use ($order, $order_number_list) {
            if (! $order->isLinkableTemporaryOrder()) {
                $message = 'the temporary order was linked already. order number: %s';
                throw new OptimisticLockException(sprintf($message, $order->order_number));
            }

            $this->order_history_repo->createByOrder($order);
            $this->order_repo->update($order, [
                'slip_status_type' => SlipStatusType::RELATION_TEMP_ORDER,
                'related_order_status_type' => RelatedOrderStatusType::MANUAL_RELATED
            ]);

            $fixed_orders = new OrderCollection();
            foreach ($order_number_list as $order_number) {
                $fixed_order = $this->order_repo->find($order_number);
                if (! $fixed_order->isLinkableFixedOrder()) {
                    $message = 'the fixed order was linked already. order number: %s';
                    throw new OptimisticLockException(sprintf($message, $fixed_order->order_number));
                }
                if ($fixed_order->factory_product_sequence_number !== $order->factory_product_sequence_number) {
                    $message = 'the factory product of the order was changed. order number: %s';
                    throw new OptimisticLockException(sprintf($message, $fixed_order->order_number));
                }
                if ($fixed_order->isAllocated()) {
                    $message = 'the fixed order was allocated. order number: %s';
                    throw new OptimisticLockException(sprintf($message, $fixed_order->order_number));
                }

                $this->order_history_repo->createByOrder($fixed_order);

                $params = ['related_order_status_type' => RelatedOrderStatusType::MANUAL_RELATED];
                if ($order->isAllocated()) {
                    $params['delivery_date'] = $order->delivery_date;
                    $params['shipping_date'] = $order->shipping_date;
                    $params['printing_shipping_date'] = $order->printing_shipping_date;
                }
                if ($order->hadBeenShipped()) {
                    $params['fixed_shipping_by'] = $order->fixed_shipping_by;
                    $params['fixed_shipping_at'] = $order->fixed_shipping_at;
                    $params['transport_company_code'] = $order->transport_company_code;
                    $params['collection_time_sequence_number'] = $order->collection_time_sequence_number;
                }
                if ($order->hadBeenIssuedInvoice()) {
                    $params['invoice_number'] = $order->invoice_number;
                }

                $fixed_order = $this->order_repo->update($fixed_order, $params);
                $this->related_order_repo->create([
                    'temporary_order_number' => $order->order_number,
                    'fixed_order_number' => $fixed_order->order_number
                ]);

                $fixed_orders->push($fixed_order);
            }

            if (! $order->isAllocated()) {
                return;
            }

            if ($order->getAllocationQuantity() > $fixed_orders->toSumOfProductQuantityToAllocateFull()) {
                throw new DisabledToLinkOrderException('over_allocation');
            }
            if ($order->hadBeenShipped() && ($order->order_quantity !== $fixed_orders->sum('order_quantity'))) {
                throw new DisabledToLinkOrderException('shipped_already');
            }

            $product_allocations = $order->product_allocations;
            foreach ($fixed_orders as $o) {
                $product_quantity = $o->getProductQuantityToAllocateFull();
                foreach ($product_allocations as $pa) {
                    if ($pa->allocation_quantity === 0) {
                        continue;
                    }

                    $product_allocation = new ProductAllocation([
                        'factory_code' => $pa->factory_code,
                        'species_code' => $pa->species_code,
                        'harvesting_date' => $pa->harvesting_date->format('Y-m-d'),
                        'order_number' => $o->order_number,
                        'warehouse_code' => $pa->warehouse_code,
                        'last_allocated_by' => $pa->last_allocated_by,
                        'last_allocated_at' => $pa->last_allocated_at
                    ]);

                    $product_allocation->allocation_quantity = ($product_quantity >= $pa->allocation_quantity) ?
                        $pa->allocation_quantity :
                        $product_quantity;

                    $product_allocation->save();

                    $factory_product = $o->factory_product;
                    $this->stock_repo->create([
                        'factory_code' => $product_allocation->factory_code,
                        'warehouse_code' => $product_allocation->warehouse_code,
                        'species_code' => $product_allocation->species_code,
                        'harvesting_date' => $product_allocation->harvesting_date->format('Y-m-d'),
                        'number_of_heads' => $factory_product->number_of_heads,
                        'weight_per_number_of_heads' => $factory_product->weight_per_number_of_heads,
                        'input_group' => $factory_product->input_group,
                        'stock_quantity' => $product_allocation->allocation_quantity,
                        'stock_weight' =>
                            $product_allocation->allocation_quantity * $factory_product->weight_per_number_of_heads,
                        'order_number' => $o->order_number,
                        'delivery_complete_flag' => $order->hadBeenShipped()
                    ]);

                    $product_quantity -= $product_allocation->allocation_quantity;
                    $pa->allocation_quantity -= $product_allocation->allocation_quantity;

                    if ($product_quantity === 0) {
                        break;
                    }
                }
            }

            $product_allocations->each(function ($pa) {
                $pa->delete();
            });

            $order->stocks->each(function ($s) {
                $s->delete();
            });
        });
    }

    /**
     * 注文データ紐付の解除
     *
     * @param  \App\Models\Order\Order $order
     * @return void
     * @throws \App\Exceptions\OptimisticLockException
     */
    public function cancelLinkOrders(Order $order): void
    {
        $this->db->transaction(function () use ($order) {
            if ($order->isLinkableTemporaryOrder()) {
                $message = 'the temporary order is not linked. order number: %s';
                throw new OptimisticLockException(sprintf($message, $order->order_number));
            }

            $this->order_history_repo->createByOrder($order);
            $this->order_repo->update($order, [
                'slip_status_type' => SlipStatusType::TEMP_ORDER,
                'related_order_status_type' => RelatedOrderStatusType::UN_RELATED
            ]);

            $order->related_orders->each(function ($ro) use ($order) {
                $fixed_order = $ro->fixed_order;
                if ($fixed_order->isLinkableFixedOrder()) {
                    $message = 'the fixed order is not linked. order number: %s';
                    throw new OptimisticLockException(sprintf($message, $fixed_order->order_number));
                }

                $this->order_history_repo->createByOrder($fixed_order);
                $this->order_repo->update($fixed_order, [
                    'related_order_status_type' => RelatedOrderStatusType::UN_RELATED
                ]);

                $ro->delete();
            });
        });
    }
}

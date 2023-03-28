<?php

declare(strict_types=1);

namespace App\Services\Order;

use SplFileObject;
use Illuminate\Database\Connection;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\DataLinkException;
use App\Models\Order\Collections\OrderingDetailCollection;
use App\Repositories\Master\CustomerRepository;
use App\Repositories\Master\DeliveryDestinationRepository;
use App\Repositories\Master\DeliveryFactoryProductRepository;
use App\Repositories\Master\EndUserFactoryRepository;
use App\Repositories\Master\FactoryRepository;
use App\Repositories\Order\AssignNumberRepository;
use App\Repositories\Order\OrderRepository;
use App\Repositories\Order\OrderHistoryRepository;
use App\Repositories\Order\OrderingRepository;
use App\Repositories\Order\OrderingDetailRepository;
use App\Repositories\Order\ReceivedOrderRepository;
use App\Repositories\Order\ReceivedOrderDetailRepository;
use App\Repositories\Order\RelatedOrderRepository;
use App\Repositories\Stock\StockRepository;
use App\ValueObjects\Date\DeliveryDate;
use App\ValueObjects\Enum\CreatingType;
use App\ValueObjects\Enum\ProcessClass;
use App\ValueObjects\Enum\RelatedOrderStatusType;
use App\ValueObjects\Enum\SlipStatusType;
use App\ValueObjects\Enum\SlipType;

class VVFBackboneImportService
{
    /**
     * @var \Illuminate\Database\Connection
     */
    private $db;

    /**
     * @var \App\Repositories\Master\FactoryRepository
     */
    private $factory_repo;

    /**
     * @var \App\Repositories\Master\CustomerRepository
     */
    private $customer_repo;

    /**
     * @var \App\Repositories\Master\EndUserFactoryRepository
     */
    private $end_user_factory_repo;

    /**
     * @var \App\Repositories\Master\DeliveryDestinationRepository
     */
    private $delivery_destination_repo;

    /**
     * @var \App\Repositories\Master\DeliveryFactoryProductRepository
     */
    private $delivery_factory_product_repo;

    /**
     * @var \App\Repositories\Order\OrderingRepository
     */
    private $ordering_repo;

    /**
     * @var \App\Repositories\Order\OrderingDetailRepository
     */
    private $ordering_detail_repo;

    /**
     * @var \App\Repositories\Order\ReceivedOrderRepository
     */
    private $received_order_repo;

    /**
     * @var \App\Repositories\Order\ReceivedOrderDetailRepository
     */
    private $received_order_detail_repo;

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
     * @var \App\Repositories\Order\RelatedOrderRepository
     */
    private $related_order_repo;

    /**
     * @var \App\Repositories\Stock\StockRepository
     */
    private $stock_repo;

    /**
     * @param  \Illuminate\Database\Connection $db
     * @param  \App\Repositories\Master\FactoryRepository $factory_repo
     * @param  \App\Repositories\Master\CustomerRepository $customer_repo
     * @param  \App\Repositories\Master\EndUserFactpryRepository $end_user_factory_repo
     * @param  \App\Repositories\Master\DeliveryDestinationRepository $delivery_destination_repo
     * @param  \App\Repositories\Master\DeliveryFactoryProductRepository $delivery_factory_product_repo
     * @param  \App\Repositories\Order\OrderingRepository $ordering_repo
     * @param  \App\Repositories\Order\OrderingDetailRepository $ordering_detail_repo
     * @param  \App\Repositories\Order\ReceivedOrderRepository $received_order_repo
     * @param  \App\Repositories\Order\ReceivedOrderDetailRepository $received_order_detail_repo
     * @param  \App\Repositories\Order\OrderRepository $order_repo
     * @param  \App\Repositories\Order\OrderHistoryRepository $order_history_repo
     * @param  \App\Repositories\Order\AssignNumberRepository $assign_number_repo
     * @param  \App\Repositories\Order\RelatedOrderRepository $related_order_repo
     * @param  \App\Repositories\Stock\StockRepository $stock_repo
     * @return void
     */
    public function __construct(
        Connection $db,
        FactoryRepository $factory_repo,
        CustomerRepository $customer_repo,
        EndUserFactoryRepository $end_user_factory_repo,
        DeliveryDestinationRepository $delivery_destination_repo,
        DeliveryFactoryProductRepository $delivery_factory_product_repo,
        OrderingRepository $ordering_repo,
        OrderingDetailRepository $ordering_detail_repo,
        ReceivedOrderRepository $received_order_repo,
        ReceivedOrderDetailRepository $received_order_detail_repo,
        OrderRepository $order_repo,
        OrderHistoryRepository $order_history_repo,
        AssignNumberRepository $assign_number_repo,
        RelatedOrderRepository $related_order_repo,
        StockRepository $stock_repo
    ) {
        $this->db = $db;
        $this->factory_repo = $factory_repo;
        $this->customer_repo = $customer_repo;
        $this->end_user_factory_repo = $end_user_factory_repo;
        $this->delivery_destination_repo = $delivery_destination_repo;
        $this->delivery_factory_product_repo = $delivery_factory_product_repo;
        $this->ordering_repo = $ordering_repo;
        $this->ordering_detail_repo = $ordering_detail_repo;
        $this->received_order_repo = $received_order_repo;
        $this->received_order_detail_repo = $received_order_detail_repo;
        $this->order_repo = $order_repo;
        $this->order_history_repo = $order_history_repo;
        $this->assign_number_repo = $assign_number_repo;
        $this->related_order_repo = $related_order_repo;
        $this->stock_repo = $stock_repo;
    }

    /**
     * 転送ファイルを取込形式に変換
     *
     * @param  string $path
     * @param  array $skipped_rows
     * @return array
     */
    public function parseTransferedFile(string $path, array $skipped_rows = []): array
    {
        $config = config('settings.data_link.order.orderings');

        $prev_skipped = new OrderingDetailCollection();
        foreach ($skipped_rows as $row) {
            $row = explode("\t", $row);
            $order = [];
            foreach ($config['set_column_index'] as $key => $value) {
                $order[$key] = $row[$value['index']];
            }

            $prev_skipped->push($order);
        }

        $file = new SplFileObject($path);
        $file->setFlags(SplFileObject::READ_AHEAD|SplFileObject::SKIP_EMPTY|SplFileObject::READ_CSV);
        $file->setCsvControl("\t");

        $rows = $skipped = [];
        foreach ($file as $idx => $row) {
            if (count($row) === 1 && empty($row[0])) {
                continue;
            }
            if (count($row) < $config['tsv_file_columns']) {
                continue;
            }

            $order = $rules =[];
            foreach ($config['set_column_index'] as $key => $value) {
                if ((strstr($value['validate'], 'nullable') === false) ||
                    (strstr($value['validate'], 'string') !== false) ||
                    ($row[$value['index']] !== '')) {
                    $order[$key] = $row[$value['index']];
                    $rules[$key] = $value['validate'];
                }
            }

            if ($prev_skipped->isSkippedOrder($order)) {
                $skipped[] = implode("\t", $row);
                continue;
            }
            if (Validator::make($order, $rules)->fails()) {
                $skipped[] = implode("\t", $row);
                continue;
            }

            $rows[] = $order;
        }

        return compact('rows', 'skipped');
    }

    /**
     * 転送ファイルのデータを登録
     *
     * @param  array $rows
     * @return array
     */
    public function importTransferedOrders(array $rows): array
    {
        return $this->db->transaction(function () use ($rows) {
            $skipped = [];
            foreach ($rows as $row) {
                $factory = $this->factory_repo->getFactoryBySupplierCode($row['supplier_flag']);
                $this->end_user_factory_repo->findOrCreateEndUserFactory([
                    'end_user_code' => $row['ordering_details_customer_code'],
                    'factory_code' => $factory->factory_code
                ]);

                $sequence_number = $this->ordering_repo->getMaxSequenceNumber() + 1;

                $this->ordering_repo->create($sequence_number, $row);
                $this->ordering_detail_repo->create($sequence_number, $row);
                $this->received_order_repo->create($sequence_number, $row);
                $this->received_order_detail_repo->create($sequence_number, $row);
            }

            return $skipped;
        });
    }

    /**
     * 連携された受発注データを再構成して注文データとして登録
     *
     * @return array
     */
    public function saveTransferedOrders(): array
    {
        return $this->db->transaction(function () {
            $create_count = $update_count = 0;
            foreach ($this->ordering_detail_repo->getNotSharedOriginalOrders() as $o) {
                $factory = $this->factory_repo->getFactoryBySupplierCode($o->supplier_flag);
                $customer = $this->customer_repo->searchDefaultCustomer();
                $delivery_destination = $this->delivery_destination_repo->find($o->delivery_destination_code);

                $shipping_date = DeliveryDate::parse($o->request_delivery_date)
                    ->getShippingDate($delivery_destination, $factory);

                $delivery_factory_product = $this->delivery_factory_product_repo
                    ->getDeliveryFactoryProductsByDeliveryDestinationAndProduct(
                        $o->delivery_destination_code,
                        $factory->factory_code,
                        $o->product_number
                    )
                    ->first();

                if (is_null($delivery_factory_product)) {
                    try {
                        $delivery_factory_product = $this->delivery_factory_product_repo
                            ->linkDeliveryDestinationToFactoryProductAutomatically(
                                $o->delivery_destination_code,
                                $factory->factory_code,
                                $o->product_number
                            );
                    } catch (DataLinkException $e) {
                        report($e);
                        continue;
                    }
                }

                $product_weight = $o->supplier_place_order_quantity *
                    $delivery_factory_product->weight_per_number_of_heads *
                    $delivery_factory_product->number_of_cases;

                $order = $this->order_repo
                    ->getOrderByBasePlusOrderNumber($o->place_order_number, $o->place_order_chapter_number);
                if (is_null($order)) {
                    $order_number = $this->assign_number_repo
                        ->getAssignedNumber('orders', $factory->symbolic_code, true);

                    $this->order_repo->create([
                        'order_number' => $order_number,
                        'base_plus_order_number' => $o->place_order_number,
                        'base_plus_order_chapter_number' => $o->place_order_chapter_number,
                        'received_date' => $o->place_order_date,
                        'prodcut_class' => $o->product_class,
                        'supplier_product_name' => $o->supplier_product_name,
                        'customer_product_name' => $o->customer_product_name,
                        'product_name' => $o->product_name,
                        'special_spec_code' => $o->special_spec_code,
                        'product_code' => $o->product_number,
                        'maker_code' => $o->maker_code,
                        'delivery_date' => $o->request_delivery_date,
                        'requestor_organization_code' => $o->requestor_code,
                        'organization_name' => $o->organization_name,
                        'end_user_code' => $o->customer_code,
                        'base_plus_end_user_code' => $o->base_plue_end_user_code,
                        'order_quantity' => $o->supplier_place_order_quantity,
                        'place_order_quantity' => $o->place_order_quantity,
                        'place_order_unit_code' => $o->place_order_unit_code,
                        'supplier_place_order_unit' => $o->supplier_place_order_unit,
                        'order_amount' => $o->place_order_amount,
                        'order_unit' => $o->place_order_unit,
                        'order_message' => $o->place_order_message,
                        'supplier_instructions' => $o->supplier_instructions,
                        'buyer_remark' => $o->buyer_remark,
                        'delivery_destination_code' => $o->delivery_destination_code,
                        'base_plus_recived_order_number' => $o->recived_order_number,
                        'base_plus_recived_order_chapter_number' => $o->recived_order_chapter_number,
                        'recived_order_unit' => $o->customer_recived_order_unit,
                        'customer_recived_order_unit' => $o->customer_recived_order_total,
                        'process_class' => $o->process_class,
                        'own_company_code' => $o->own_company_code,
                        'small_peace_of_peper_type_class' => $o->small_peace_of_peper_type_class,
                        'small_peace_of_peper_type_code' => $o->small_peace_of_peper_type_code,
                        'supplier_flag' => $o->supplier_flag,
                        'tax_class' => $o->tax_class,
                        'purchase_staff_code' => $o->purchase_staff_code,
                        'purchase_staff_name' => $o->purchase_staff_name,
                        'currency_code' => $o->currency_code,
                        'place_order_work_staff_code' => $o->place_order_work_staff_code,
                        'place_order_work_staff_name' => $o->place_order_work_staff_name,
                        'end_user_order_number' => $o->customer_order_number,
                        'pickup_type_class' => $o->pickup_type_class,
                        'pickup_type_code' => $o->pickup_type_code,
                        'basis_for_recording_sales_class' => $o->basis_for_recording_sales_class,
                        'statement_delivery_price_display_class' => $o->statement_delivery_price_display_class,
                        'seller_code' => $o->seller_code,
                        'seller_name' => $o->seller_name,
                        'customer_staff_name' => $o->customer_staff_name,
                        'base_plus_delete_flag' => $o->base_plus_delete_flag,
                        'base_plus_user_created_by' => $o->base_plus_user_created_by,
                        'base_plus_program_created_by' => $o->base_plus_program_created_by,
                        'base_plus_created_at' => $o->base_plus_created_at,
                        'base_plus_user_updtaed_by' => $o->base_plus_user_updtaed_by,
                        'base_plus_program_updated_by' => $o->base_plus_program_updated_by,
                        'base_plus_updated_at' => $o->base_plus_updated_at,
                        'factory_code' => $factory->factory_code,
                        'factory_product_sequence_number' => $delivery_factory_product->factory_product_sequence_number,
                        'product_weight' => $product_weight,
                        'customer_code' => $customer->customer_code,
                        'shipping_date' => $shipping_date->format('Y-m-d'),
                        'printing_shipping_date' => $shipping_date->getPrintingShippingDate($factory)->value(),
                        'creating_type' => CreatingType::BASE_PLUS_LINKED,
                        'slip_type' => SlipType::NORMAL_SLIP,
                        'slip_status_type' => SlipStatusType::FIXED_ORDER,
                        'transport_company_code' => $delivery_destination->transport_company_code,
                        'collection_time_sequence_number' => $delivery_destination->collection_time_sequence_number
                    ]);

                    $create_count = $create_count + 1;
                } else {
                    if ($o->supplier_flag !== $order->supplier_flag) {
                        continue;
                    }
                    if ($o->product_number !== $order->product_code) {
                        continue;
                    }
                    if ($delivery_factory_product->factory_product_sequence_number !==
                        $order->factory_product_sequence_number) {
                        continue;
                    }
                    if ($order->isAllocated() && ($o->process_class === ProcessClass::CANCEL_PROCESS)) {
                        continue;
                    }
                    if ($order->getAllocationQuantity() > $order->number_of_cases * $o->supplier_place_order_quantity) {
                        continue;
                    }
                    if ($order->hadBeenShipped() &&
                        ($o->delivery_destination_code !== $order->delivery_destination_code)) {
                        continue;
                    }
                    if ($order->hadBeenShipped() && ($o->supplier_place_order_quantity !== $order->order_quantity)) {
                        continue;
                    }

                    $order = $this->order_repo->find($order->order_number);
                    $this->order_history_repo->createByOrder($order);

                    $params = [
                        'received_date' => $o->place_order_date,
                        'prodcut_class' => $o->product_class,
                        'supplier_product_name' => $o->supplier_product_name,
                        'customer_product_name' => $o->customer_product_name,
                        'product_name' => $o->product_name,
                        'special_spec_code' => $o->special_spec_code,
                        'maker_code' => $o->maker_code,
                        'delivery_date' => $o->request_delivery_date,
                        'requestor_organization_code' => $o->requestor_code,
                        'organization_name' => $o->organization_name,
                        'end_user_code' => $o->customer_code,
                        'base_plus_end_user_code' => $o->base_plue_end_user_code,
                        'order_quantity' => $o->supplier_place_order_quantity,
                        'place_order_quantity' => $o->place_order_quantity,
                        'place_order_unit_code' => $o->place_order_unit_code,
                        'supplier_place_order_unit' => $o->supplier_place_order_unit,
                        'order_amount' => $o->place_order_amount,
                        'order_unit' => $o->place_order_unit,
                        'order_message' => $o->place_order_message,
                        'supplier_instructions' => $o->supplier_instructions,
                        'buyer_remark' => $o->buyer_remark,
                        'delivery_destination_code' => $o->delivery_destination_code,
                        'recived_order_unit' => $o->customer_recived_order_unit,
                        'customer_recived_order_unit' => $o->customer_recived_order_total,
                        'process_class' => $o->process_class,
                        'own_company_code' => $o->own_company_code,
                        'small_peace_of_peper_type_class' => $o->small_peace_of_peper_type_class,
                        'small_peace_of_peper_type_code' => $o->small_peace_of_peper_type_code,
                        'tax_class' => $o->tax_class,
                        'purchase_staff_code' => $o->purchase_staff_code,
                        'purchase_staff_name' => $o->purchase_staff_name,
                        'currency_code' => $o->currency_code,
                        'place_order_work_staff_code' => $o->place_order_work_staff_code,
                        'place_order_work_staff_name' => $o->place_order_work_staff_name,
                        'end_user_order_number' => $o->customer_order_number,
                        'pickup_type_class' => $o->pickup_type_class,
                        'pickup_type_code' => $o->pickup_type_code,
                        'basis_for_recording_sales_class' => $o->basis_for_recording_sales_class,
                        'statement_delivery_price_display_class' => $o->statement_delivery_price_display_class,
                        'seller_code' => $o->seller_code,
                        'seller_name' => $o->seller_name,
                        'customer_staff_name' => $o->customer_staff_name,
                        'base_plus_delete_flag' => $o->base_plus_delete_flag,
                        'base_plus_user_created_by' => $o->base_plus_user_created_by,
                        'base_plus_program_created_by' => $o->base_plus_program_created_by,
                        'base_plus_created_at' => $o->base_plus_created_at,
                        'base_plus_user_updtaed_by' => $o->base_plus_user_updtaed_by,
                        'base_plus_program_updated_by' => $o->base_plus_program_updated_by,
                        'base_plus_updated_at' => $o->base_plus_updated_at,
                        'product_weight' => $product_weight,
                        'slip_status_type' => SlipStatusType::FIXED_ORDER
                    ];

                    if (! $order->hadBeenShipped()) {
                        $params['shipping_date'] = $shipping_date->format('Y-m-d');
                        $params['printing_shipping_date'] =
                            $shipping_date->getPrintingShippingDate($factory)->value();
                        $params['transport_company_code'] = $delivery_destination->transport_company_code;
                        $params['collection_time_sequence_number'] =
                            $delivery_destination->collection_time_sequence_number;
                    }

                    $order = $this->order_repo->update($order, $params);
                    if ($order->isCanceledOrder() && $order->isAllocated()) {
                        $order->product_allocations->each(function ($pa) {
                            $pa->delete();
                        });

                        $order->stocks->each(function ($s) {
                            $stock = $this->stock_repo
                                ->findNotAllocatedStocks(array_only($s->toArray(), [
                                    'factory_code',
                                    'warehouse_code',
                                    'species_code',
                                    'harvesting_date',
                                    'number_of_heads',
                                    'weight_per_number_of_heads',
                                    'input_group',
                                    'stock_status'
                                ]))
                                ->first();

                            $stock->stock_quantity += $s->stock_quantity;
                            $stock->stock_weight += $s->stock_weight;
                            $stock->save();

                            $s->delete();
                        });
                    }

                    $update_count = $update_count + 1;
                }

                $sequence_numbers = $this->ordering_detail_repo->getSequenceNumbersByBasePlusOrderNumber($o);

                $this->ordering_repo->updateCollaboration($sequence_numbers);
                $this->ordering_detail_repo->updateCollaboration($sequence_numbers);
                $this->received_order_repo->updateCollaboration($sequence_numbers);
                $this->received_order_detail_repo->updateCollaboration($sequence_numbers);
            }

            return compact('create_count', 'update_count');
        });
    }

    /**
     * マッチング処理
     *
     * @return array
     */
    public function matching(): array
    {
        $temp_orders = $this->order_repo->getNotRelatedOrders(CreatingType::MANUAL_CREATED, SlipStatusType::TEMP_ORDER);
        $fixed_orders = $this->order_repo
            ->getNotRelatedOrders(CreatingType::BASE_PLUS_LINKED, SlipStatusType::FIXED_ORDER);

        $matched_orders = collect([]);
        $matching = function ($temp_order, $fixed_order) use ($matched_orders) {
            return $temp_order->delivery_date === $fixed_order->delivery_date &&
                $temp_order->delivery_destination_code === $fixed_order->delivery_destination_code &&
                $temp_order->product_code === $fixed_order->product_code &&
                $temp_order->order_quantity === $fixed_order->order_quantity &&
                $temp_order->end_user_code === $fixed_order->end_user_code &&
                $temp_order->order_unit === $fixed_order->order_unit &&
                $temp_order->factory_code === $fixed_order->factory_code &&
                $temp_order->factory_product_sequence_number === $fixed_order->factory_product_sequence_number &&
                (! $temp_order->hadBeenShipped() || ! $fixed_order->hadBeenShipped()) &&
                ! in_array($fixed_order->order_number, $matched_orders->pluck('order_number')->all(), true);
        };

        foreach ($temp_orders as $o) {
            foreach ($fixed_orders as $fixed_order) {
                if ($fixed_order->buyer_remark !== '') {
                    continue;
                }

                if ($matching($o, $fixed_order)) {
                    $fixed_order->temp_order = $o;
                    $matched_orders->push($fixed_order);

                    break;
                }
            }
        }

        $messages = config('constant.order.vvf_backbone_import.message');
        if ($matched_orders->isEmpty()) {
            return ['stop_message' => $messages['stop_message']];
        }

        $error_messages = [];
        $matching_count = 0;

        foreach ($matched_orders as $fixed_order) {
            $total_allocation_quantity = $fixed_order->getAllocationQuantity() +
                $fixed_order->temp_order->getAllocationQuantity();
            $total_product_quantity = $fixed_order->getProductQuantityToAllocateFull() +
                $fixed_order->temp_order->getProductQuantityToAllocateFull();

            if ($total_allocation_quantity > $total_product_quantity) {
                $error_messages[] = sprintf($messages['comparison_error'], $fixed_order->temp_order->order_number);
                continue;
            }

            $matching_count = $this->db->transaction(function () use ($matching_count, $fixed_order) {
                $temp_order = $this->order_repo->find($fixed_order->temp_order->order_number);
                $this->order_history_repo->createByOrder($temp_order);
                $this->order_repo->update($temp_order, [
                    'slip_status_type' => SlipStatusType::RELATION_TEMP_ORDER,
                    'related_order_status_type' => RelatedOrderStatusType::AUTO_RELATED
                ]);

                $fixed_order = $this->order_repo->find($fixed_order->order_number);
                $params = ['related_order_status_type' => RelatedOrderStatusType::AUTO_RELATED];
                if ($temp_order->hadBeenShipped() && ! $fixed_order->hadBeenShipped()) {
                    $params['fixed_shipping_at'] = $temp_order->fixed_shipping_at;
                }
                if ($temp_order->hadBeenIssuedInvoice()) {
                    $params['invoice_number'] = $temp_order->invoice_number;
                }

                $this->order_history_repo->createByOrder($fixed_order);
                $this->order_repo->update($fixed_order, $params);

                $this->related_order_repo->create([
                    'temporary_order_number' => $temp_order->order_number,
                    'fixed_order_number' => $fixed_order->order_number
                ]);

                if ($temp_order->isAllocated()) {
                    // [1] 確定注文に対する引当がない
                    if (! $fixed_order->isAllocated()) {
                        $temp_order->product_allocations->each(function ($pa) use ($fixed_order) {
                            $pa->order_number = $fixed_order->order_number;
                            $pa->save();
                        });

                        $temp_order->stocks->each(function ($s) use ($fixed_order) {
                            $s->order_number = $fixed_order->order_number;
                            $s->save();
                        });
                    }

                    // [2] 確定注文が引当済 or 仮注文も確定注文も部分引当
                    if ($fixed_order->isFullAllocated() ||
                        ($temp_order->isPartAllocated() && $fixed_order->isPartAllocated())) {
                        $temp_order->product_allocations->each(function ($pa) {
                            $pa->delete();
                        });

                        $temp_order->stocks->each(function ($s) {
                            $stock = $this->stock_repo
                                ->findNotAllocatedStocks(array_only($s->toArray(), [
                                    'factory_code',
                                    'warehouse_code',
                                    'species_code',
                                    'harvesting_date',
                                    'number_of_heads',
                                    'weight_per_number_of_heads',
                                    'input_group',
                                    'stock_status'
                                ]))
                                ->first();

                            $stock->stock_quantity += $s->stock_quantity;
                            $stock->stock_weight += $s->stock_weight;
                            $stock->save();

                            $s->delete();
                        });
                    }

                    // [3] 仮注文が引当済かつ確定注文が部分引当
                    if ($temp_order->isFullAllocated() && $fixed_order->isPartAllocated()) {
                        $fixed_order->product_allocations->each(function ($pa) {
                            $pa->delete();
                        });

                        $fixed_order->stocks->each(function ($s) {
                            $stock = $this->stock_repo
                                ->findNotAllocatedStocks(array_only($s->toArray(), [
                                    'factory_code',
                                    'warehouse_code',
                                    'species_code',
                                    'harvesting_date',
                                    'number_of_heads',
                                    'weight_per_number_of_heads',
                                    'input_group',
                                    'stock_status'
                                ]))
                                ->first();

                            $stock->stock_quantity += $s->stock_quantity;
                            $stock->stock_weight += $s->stock_weight;
                            $stock->save();

                            $s->delete();
                        });

                        $temp_order->product_allocations->each(function ($pa) use ($fixed_order) {
                            $pa->order_number = $fixed_order->order_number;
                            $pa->save();
                        });

                        $temp_order->stocks->each(function ($s) use ($fixed_order) {
                            $s->order_number = $fixed_order->order_number;
                            $s->save();
                        });
                    }
                }

                return $matching_count + 1;
            });
        }

        return [
            'success_message' => sprintf($messages['matching_success'], $matching_count),
            'error_message' => $error_messages
        ];
    }
}

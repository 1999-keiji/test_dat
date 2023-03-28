<?php

declare(strict_types=1);

namespace App\Services\Order;

use Illuminate\Database\Connection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Excel;
use App\Models\Master\Customer;
use App\Models\Master\Factory;
use App\Repositories\Master\DeliveryFactoryProductRepository;
use App\Repositories\Order\AssignNumberRepository;
use App\Repositories\Order\OrderRepository;
use App\ValueObjects\Date\DeliveryDate;
use App\ValueObjects\Enum\CreatingType;
use App\ValueObjects\Enum\ProcessClass;
use App\ValueObjects\Enum\SlipType;
use App\ValueObjects\String\CurrencyCode;

class PurchaseOrderExcelImportService
{
    /**
     * @var \Illuminate\Database\Connection
     */
    private $db;

    /**
     * @var \Maatwebsite\Excel\Excel;
     */
    private $excel;

    /**
     * @var \App\Repositories\Master\DeliveryFactoryProductRepository
     */
    private $delivery_factory_product_repo;

    /**
     * @var \App\Repositories\Order\OrderRepository
     */
    private $order_repo;

    /**
     * @var \App\Repositories\Order\AssignNumberRepository
     */
    private $assign_number_repo;

    /**
     * @param  \Illuminate\Database\Connection $db
     * @param  \Maatwebsite\Excel\Excel $excel
     * @param  \App\Repositories\Master\DeliveryFactoryProductRepository $delivery_factory_product_repositry
     * @param  \App\Repositories\Order\OrderRepository $order_repositry
     * @param  \App\Repositories\Order\AssignNumberRepository $assign_number_repositry
     * @return void
     */
    public function __construct(
        Connection $db,
        Excel $excel,
        DeliveryFactoryProductRepository $delivery_factory_product_repositry,
        OrderRepository $order_repositry,
        AssignNumberRepository $assign_number_repositry
    ) {
        $this->db = $db;
        $this->excel = $excel;
        $this->delivery_factory_product_repo = $delivery_factory_product_repositry;
        $this->order_repo = $order_repositry;
        $this->assign_number_repo = $assign_number_repositry;
    }

    /**
     * アップロードされたファイルの確認
     *
     * @param  array $params
     * @return bool
     */
    public function checkUploadedFile(array $params): bool
    {
        $reader = $this->excel->load($params['import_file']->getRealPath());
        $reader->noHeading();

        $sheet_name = config('constant.order.purchase_order_excel_import.sheet_name');
        return ! is_null($reader->getSheetByName($sheet_name));
    }

    /**
     * アップロードされたデータの整理
     *
     * @param  array $params
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Master\Customer $customer
     * @return array
     */
    public function parseUploadedFile(array $params, Factory $factory, Customer $customer): array
    {
        $config = config('constant.order.purchase_order_excel_import');

        $reader = $this->excel->load($params['import_file']->getRealPath());
        $reader->noHeading();
        $sheet = $reader->getSheetByName($config['sheet_name']);

        $orders = $errors = [];
        foreach ($sheet->toArray() as $idx => $row) {
            if ($idx === 0) {
                continue;
            }

            $params = array_merge(
                ['factory_code' => $factory->factory_code, 'customer_code' => $customer->customer_code],
                array_combine($config['import_key_list'], $row)
            );
            if (! $params['order_quantity']) {
                continue;
            }
            if (! $params['currency_code']) {
                $params['currency_code'] = CurrencyCode::getDefaultCurrencyCode();
            }
            if (! $params['delivery_destination_code']) {
                $params['delivery_destination_code'] = $params['delivery_destination_code_2'];
            }

            $delivery_factory_product = $this->delivery_factory_product_repo
                ->getPurchasedDeliveryFactoryProduct($params);
            if (is_null($delivery_factory_product)) {
                $errors[$idx + 1] = [$config['not_linked_delivery_factory_product']];
                continue;
            }

            $product_weight = $params['order_quantity'] *
                $delivery_factory_product->weight_per_number_of_heads *
                $delivery_factory_product->number_of_cases;

            $delivery_destination = $delivery_factory_product->delivery_destination;
            $delivery_date = DeliveryDate::parse(
                preg_replace("/\A([0-9]{4})([0-9]{2})([0-9]{2})\z/", '$1-$2-$3', $params['delivery_date'])
            );

            $shipping_date = $delivery_date->getShippingDate($delivery_destination, $factory);

            $order_unit = $params['order_unit'];
            if ($order_unit === '') {
                $order_unit = $delivery_factory_product->getAppliedUnitPrice($params['currency_code'], $delivery_date);
                if (is_null($order_unit)) {
                    $errors[$idx + 1] = [$config['disabled_to_apply_unit_price']];
                    continue;
                }
            }

            $order = [
                'base_plus_order_number' => $params['base_plus_order_number'],
                'base_plus_order_chapter_number' => $params['base_plus_order_chapter_number'],
                'requestor_organization_code' => $params['requestor_organization_code'],
                'end_user_code' => $params['end_user_code'],
                'supplier_flag' => $params['supplier_flag'],
                'base_plus_end_user_code' => $params['base_plus_end_user_code'],
                'delivery_destination_code' => $params['delivery_destination_code'],
                'maker_code' => $params['maker_code'],
                'product_code' => $delivery_factory_product->product_code,
                'product_name' => $delivery_factory_product->product_name,
                'special_spec_code' => $params['special_spec_code'],
                'order_quantity' => $params['order_quantity'],
                'place_order_unit_code' => $delivery_factory_product->unit,
                'purchase_staff_code' => $params['purchase_staff_code'],
                'place_order_work_staff_code' => $params['place_order_work_staff_code'],
                'supplier_product_name' => $params['supplier_product_name'],
                'order_unit' => $order_unit->value(),
                'order_amount' => $order_unit->value() * $params['order_quantity'],
                'currency_code' => $params['currency_code'],
                'delivery_date' => $delivery_date->format('Y-m-d'),
                'tax_class' => $params['tax_class'],
                'supplier_instructions' => $params['supplier_instructions'],
                'buyer_remark' => $params['buyer_remark'],
                'order_message' => $params['order_message'],
                'base_plus_recived_order_number' => $params['base_plus_recived_order_number'],
                'base_plus_recived_order_chapter_number' => $params['base_plus_recived_order_chapter_number'],
                'seller_code' => $params['seller_code'],
                'customer_product_name' => $params['customer_product_name'],
                'recived_order_unit' => $params['recived_order_unit'] ?: $order_unit->value(),
                'end_user_order_number' => $params['end_user_order_number'],
                'statement_delivery_price_display_class' => $params['statement_delivery_price_display_class'],
                'basis_for_recording_sales_class' => $params['basis_for_recording_sales_class'],
                'customer_staff_name' => $params['customer_staff_name'],
                'customer_code' => $params['customer_code'],
                'factory_code' => $params['factory_code'],
                'received_date' => date('Y-m-d'),
                'shipping_date' => $shipping_date->format('Y-m-d'),
                'printing_shipping_date' => $shipping_date->getPrintingShippingDate($factory)->value(),
                'factory_product_sequence_number' => $delivery_factory_product->factory_product_sequence_number,
                'product_weight' =>  $product_weight,
                'creating_type' => CreatingType::MANUAL_CREATED,
                'slip_type' => SlipType::NORMAL_SLIP,
                'slip_status_type' => $customer->getSlipStatusType(),
                'transport_company_code' => $delivery_destination->transport_company_code,
                'collection_time_sequence_number' => $delivery_destination->collection_time_sequence_number
            ];

            $result = Validator::make($order, $this->getValidationRules($order, $factory));
            if ($result->fails()) {
                $errors[$idx + 1] = $result->errors()->all();
                continue;
            }

            $orders[] = $order;
        }

        return [$orders, $errors];
    }

    /**
     * 注文データの取込
     *
     * @param  array $orders
     * @param  \App\Models\Master\Factory $factory
     * @return void
     */
    public function importUploadedOrders(array $orders, Factory $factory): void
    {
        $this->db->transaction(function () use ($orders, $factory) {
            foreach ($orders as $o) {
                $o['order_number'] = $this->assign_number_repo->getAssignedNumber('orders', $factory->symbolic_code);
                $this->order_repo->create($o);
            }
        });
    }

    /**
     * バリデーション用のルールを取得
     *
     * @param  array $order
     * @param  \App\Models\Master\Factory $factory
     * @return array
     */
    private function getValidationRules(array $order, Factory $factory): array
    {
        return [
            'end_user_code' => [
                'bail',
                'required',
                Rule::exists('end_users')->where(function ($query) use ($order) {
                    $query->where('customer_code', $order['customer_code']);
                }),
                Rule::in($factory->getLinkedEndUserCodeList())
            ],
            'supplier_flag' => [
                'bail',
                'required',
                Rule::in([$factory->supplier_code])
            ],
            'delivery_date' => [
                'bail',
                'required',
                Rule::unique('orders')->where(function ($query) use ($order) {
                    $query->where('customer_code', $order['customer_code'])
                        ->where('end_user_code', $order['end_user_code'])
                        ->where('delivery_destination_code', $order['delivery_destination_code'])
                        ->where('supplier_flag', $order['supplier_flag'])
                        ->where('product_code', $order['product_code'])
                        ->where('creating_type', CreatingType::MANUAL_CREATED)
                        ->where('slip_type', SlipType::NORMAL_SLIP)
                        ->where('process_class', '<>', ProcessClass::CANCEL_PROCESS)
                        ->where('factory_cancel_flag', false);
                })
            ]
        ];
    }
}

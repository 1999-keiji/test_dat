<?php

declare(strict_types=1);

namespace App\Repositories\Order;

use Illuminate\Database\Connection;
use App\Models\Order\OrderingDetail;
use App\Models\Order\Collections\OrderingDetailCollection;

class OrderingDetailRepository
{
    /**
     * @var \Illuminate\Database\Connection
     */
    private $db;

    /**
     * @var \App\Models\Order\OrderingDetail
     */
    private $model;

    /**
     * @param  \Illuminate\Database\Connection $db
     * @param  \App\Models\Order\OrderingDetail $model
     * @return void
     */
    public function __construct(Connection $db, OrderingDetail $model)
    {
        $this->db = $db;
        $this->model = $model;
    }

    /**
     * 登録
     *
     * @param  int $sequence_number
     * @param  array $params
     * @return \App\Models\Order\OrderingDetail
     */
    public function create(int $sequence_number, array $params): OrderingDetail
    {
        return $this->model->create([
            'sequence_number' => $sequence_number,
            'own_company_code' => $params['ordering_own_company_code'],
            'place_order_number' => $params['place_order_number'],
            'place_order_chapter_number' => $params['place_order_chapter_number'],
            'place_order_annulment_reason_code' => $params['place_order_annulment_reason_code'],
            'edl_send_compleate_flag' => $params['edl_send_compleate_flag'],
            'edl_information_class' => $params['edl_information_class'],
            'place_order_date' => $params['place_order_date'],
            'order_contract_receive_date' => $params['order_contract_receive_date'],
            'order_class' => $params['order_class'],
            'product_class' => $params['product_class'],
            'supplier_product_name' => $params['supplier_product_name'],
            'customer_product_name' => $params['ordering_details_customer_product_name'],
            'product_name' => $params['ordering_details_product_name'],
            'special_spec_code' => $params['ordering_details_special_spec_code'],
            'product_number' => $params['ordering_details_product_number'],
            'maker_code' => $params['ordering_details_maker_code'],
            'request_delivery_date' => $params['request_delivery_date'],
            'answer_delivery_date' => $params['answer_delivery_date'],
            'requestor_organization_code' => $params['ordering_details_requestor_organization_code'],
            'organization_name' => $params['ordering_details_organization_name'],
            'customer_code' => $params['ordering_details_customer_code'],
            'customer_place_order_last_code' => $params['customer_place_order_last_code'],
            'base_plue_end_user_code' => $params['base_plue_end_user_code'],
            'supplier_place_order_quantity' => $params['supplier_place_order_quantity'],
            'supplier_purchase_compleate_quantity' => $params['supplier_purchase_compleate_quantity'],
            'supplier_closed_quantity' => $params['supplier_closed_quantity'],
            'place_order_quantity' => $params['place_order_quantity'],
            'arrival_plan_quantity' => $params['arrival_plan_quantity'],
            'arrival_compleate_quantity' => $params['arrival_compleate_quantity'],
            'stock_compleate_quantity' => $params['stock_compleate_quantity'],
            'purchase_compleate_quantity' => $params['purchase_compleate_quantity'],
            'cancellation_quantity' => $params['cancellation_quantity'],
            'cancellation_delivery_compleate_quantity' => $params['cancellation_delivery_compleate_quantity'],
            'place_order_unit_code' => $params['place_order_unit_code'],
            'supplier_place_order_unit' => $params['supplier_place_order_unit'],
            'place_order_amount' => $params['place_order_amount'],
            'place_order_unit' => $params['place_order_unit'],
            'contract_flag' => $params['contract_flag'],
            'place_order_message' => $params['place_order_message'],
            'supplier_instructions' => $params['supplier_instructions'],
            'buyer_remark' => $params['ordering_details_buyer_remark'],
            'delivery_destination_code' => $params['ordering_details_delivery_destination_code'],
            'warehouse_code' => $params['warehouse_code'],
            'orders_sheet_issue_flag' => $params['orders_sheet_issue_flag'],
            'orders_sheet_issue_date' => $params['orders_sheet_issue_date'],
            'construction_develop_number' => $params['construction_develop_number'],
            'estimation_approval_number' => $params['estimation_approval_number'],
            'supplier_recived_order_number' => $params['supplier_recived_order_number'],
            'supplier_original_recived_order_pickup_number' => $params['supplier_original_recived_order_pickup_number'],
            'manufacturing_class' => $params['manufacturing_class'],
            'inspection_spec_sentence' => $params['ordering_details_inspection_spec_sentence'],
            'inspection_circulation' => $params['inspection_circulation'],
            'statement_delivery_date' => $params['statement_delivery_date'],
            'set_product_name' => $params['set_product_name'],
            'set_maker' => $params['set_maker'],
            'set_special_spec_code' => $params['set_special_spec_code'],
            'repair_order_flag' => $params['ordering_details_repair_order_flag'],
            'goods_name' => $params['ordering_details_goods_name'],
            'goods_quantity' => $params['ordering_details_goods_quantity'],
            'compleate_flag' => $params['compleate_flag'],
            'unofficial_recived_order_flag' => $params['unofficial_recived_order_flag'],
            'base_plus_delete_flag' => $params['base_plus_delete_flag'],
            'reserved_text1' => $params['reserved_text1'],
            'reserved_text2' => $params['reserved_text2'],
            'reserved_text3' => $params['reserved_text3'],
            'reserved_text4' => $params['reserved_text4'],
            'reserved_text5' => $params['reserved_text5'],
            'reserved_text6' => $params['reserved_text6'],
            'reserved_text7' => $params['reserved_text7'],
            'reserved_text8' => $params['reserved_text8'],
            'reserved_text9' => $params['reserved_text9'],
            'reserved_text10' => $params['reserved_text10'],
            'reserved_number1' => $params['reserved_number1'],
            'reserved_number2' => $params['reserved_number2'],
            'reserved_number3' => $params['reserved_number3'],
            'reserved_number4' => $params['reserved_number4'],
            'reserved_number5' => $params['reserved_number5'],
            'reserved_number6' => $params['reserved_number6'],
            'reserved_number7' => $params['reserved_number7'],
            'reserved_number8' => $params['reserved_number8'],
            'reserved_number9' => $params['reserved_number9'],
            'reserved_number10' => $params['reserved_number10'],
            'base_plus_created_at' => $params['ordering_details_base_plus_created_at'],
            'base_plus_user_created_by' => $params['ordering_details_base_plus_user_created_by'],
            'base_plus_program_created_by' => $params['ordering_details_base_plus_program_created_by'],
            'base_plus_updated_at' => $params['ordering_details_base_plus_updated_at'],
            'base_plus_user_updated_by' => $params['ordering_details_base_plus_user_updated_by'],
            'base_plus_program_updated_by' => $params['ordering_details_base_plus_program_updated_by']
        ]);
    }

    /**
     * 取込済の状態に更新
     *
     * @param  array $sequence_numbers
     * @return void
     */
    public function updateCollaboration(array $sequence_numbers): void
    {
        $this->model
            ->whereIn('sequence_number', $sequence_numbers)
            ->update(['fsystem_sharing_flag' => true]);
    }

    /**
     * 未連携状態の受発注情報を取得
     *
     * @return \App\Models\Order\Collections\OrderingDetailCollection
     */
    public function getNotSharedOriginalOrders(): OrderingDetailCollection
    {
        return $this->model
            ->select([
                'ordering_details.sequence_number',
                'ordering_details.place_order_number',
                'ordering_details.place_order_chapter_number',
                'ordering_details.place_order_date',
                'ordering_details.product_class',
                'ordering_details.supplier_product_name',
                'ordering_details.customer_product_name',
                'ordering_details.product_name',
                'ordering_details.special_spec_code',
                'ordering_details.product_number',
                'ordering_details.maker_code',
                $this->db->raw(
                    "DATE_FORMAT(ordering_details.request_delivery_date, '%Y-%m-%d') AS request_delivery_date"
                ),
                'ordering_details.requestor_code',
                'ordering_details.organization_name',
                'ordering_details.customer_code',
                'ordering_details.base_plue_end_user_code',
                'ordering_details.supplier_place_order_quantity',
                'ordering_details.place_order_quantity',
                'ordering_details.place_order_unit_code',
                'ordering_details.supplier_place_order_unit',
                'ordering_details.place_order_amount',
                'ordering_details.place_order_unit',
                'ordering_details.place_order_message',
                'ordering_details.supplier_instructions',
                'ordering_details.buyer_remark',
                'ordering_details.delivery_destination_code',
                'ordering_details.base_plus_delete_flag',
                'ordering_details.base_plus_user_created_by',
                'ordering_details.base_plus_program_created_by',
                'ordering_details.base_plus_created_at',
                'ordering_details.base_plus_user_updated_by',
                'ordering_details.base_plus_program_updated_by',
                'ordering_details.base_plus_updated_at',
                'received_order_details.customer_recived_order_unit',
                'received_order_details.customer_recived_order_total',
                'received_order_details.recived_order_number',
                'received_order_details.recived_order_chapter_number',
                'orderings.process_class',
                'orderings.own_company_code',
                'orderings.small_peace_of_peper_type_class',
                'orderings.small_peace_of_peper_type_code',
                'orderings.supplier_flag',
                'orderings.tax_class',
                'orderings.purchase_staff_code',
                'orderings.purchase_staff_name',
                'orderings.currency_code',
                'orderings.place_order_work_staff_code',
                'orderings.place_order_work_staff_name',
                'received_orders.customer_order_number',
                'received_orders.pickup_type_class',
                'received_orders.pickup_type_code',
                'received_orders.basis_for_recording_sales_class',
                'received_orders.statement_delivery_price_display_class',
                'received_orders.seller_code',
                'received_orders.seller_name',
                'received_orders.customer_staff_name',
            ])
            ->join('orderings', function ($join) {
                $join->on('ordering_details.sequence_number', '=', 'orderings.sequence_number')
                    ->where('orderings.fsystem_sharing_flag', false);
            })
            ->join('received_orders', function ($join) {
                $join->on('ordering_details.sequence_number', '=', 'received_orders.sequence_number')
                    ->where('received_orders.fsystem_sharing_flag', false);
            })
            ->join('received_order_details', function ($join) {
                $join->on('ordering_details.sequence_number', '=', 'received_order_details.sequence_number')
                    ->where('received_order_details.fsystem_sharing_flag', false);
            })
            ->where('ordering_details.fsystem_sharing_flag', false)
            ->orderBy('ordering_details.sequence_number', 'ASC')
            ->get();
    }

    /**
     * BASE+注文番号を利用して連番を取得
     *
     * @param  \App\Models\Order\OrderingDetail $order
     * @return array
     */
    public function getSequenceNumbersByBasePlusOrderNumber(OrderingDetail $order): array
    {
        return $this->model->select('sequence_number')
            ->where('sequence_number', '<=', $order->sequence_number)
            ->where('place_order_number', $order->place_order_number)
            ->where('place_order_chapter_number', $order->place_order_chapter_number)
            ->get()
            ->pluck('sequence_number')
            ->all();
    }
}

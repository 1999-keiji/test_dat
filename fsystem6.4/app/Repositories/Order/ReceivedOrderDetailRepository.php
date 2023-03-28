<?php

declare(strict_types=1);

namespace App\Repositories\Order;

use App\Models\Order\ReceivedOrderDetail;

class ReceivedOrderDetailRepository
{
    /**
     * @var \App\Models\Order\ReceivedOrderDetail
     */
    private $model;

    /**
     * @param  \App\Models\Order\ReceivedOrderDetail $model
     * @return void
     */
    public function __construct(ReceivedOrderDetail $model)
    {
        $this->model = $model;
    }

    /**
     * 登録
     *
     * @param  int $sequence_number
     * @param  array $params
     * @return \App\Models\Order\ReceivedOrderDetail
     */
    public function create(int $sequence_number, array $params): ReceivedOrderDetail
    {
        return $this->model->create([
            'sequence_number' => $sequence_number,
            'recived_order_number' => $params['recived_order_number'],
            'recived_order_chapter_number' => $params['recived_order_chapter_number'],
            'recived_order_annulment_reason_code' => $params['recived_order_annulment_reason_code'],
            'prodcut_class' => $params['prodcut_class'],
            'customer_product_name' => $params['received_order_details_customer_product_name'],
            'product_name' => $params['received_order_details_product_name'],
            'special_spec_code' => $params['received_order_details_special_spec_code'],
            'maker_code' => $params['received_order_details_maker_code'],
            'product_number' => $params['received_order_details_product_number'],
            'requestor_organization_code' => $params['received_order_details_requestor_organization_code'],
            'organization_name' => $params['received_order_details_organization_name'],
            'inspection_spec_sentence' => $params['received_order_details_inspection_spec_sentence'],
            'attached_item' => $params['attached_item'],
            'detail_payment_installments_flag' => $params['detail_payment_installments_flag'],
            'customer_recived_order_quantity' => $params['customer_recived_order_quantity'],
            'customer_sales_compleate_quantity' => $params['customer_sales_compleate_quantity'],
            'customer_closed_quantity' => $params['customer_closed_quantity'],
            'recived_order_quantity' => $params['recived_order_quantity'],
            'recived_order_shipment_compleate_quantity' => $params['recived_order_shipment_compleate_quantity'],
            'recived_order_sales_compleate_quantity' => $params['recived_order_sales_compleate_quantity'],
            'recived_order_unit' => $params['recived_order_unit'],
            'stock_unit' => $params['stock_unit'],
            'customer_recived_order_unit' => $params['customer_recived_order_unit'],
            'recived_order_unit_amount' => $params['recived_order_unit_amount'],
            'customer_recived_order_total' => $params['customer_recived_order_total'],
            'invoice_display_unit' => $params['invoice_display_unit'],
            'invoice_display_total' => $params['invoice_display_total'],
            'saller_instructions' => $params['saller_instructions'],
            'recived_order_person_remark' => $params['recived_order_person_remark'],
            'buyer_remark' => $params['received_order_details_buyer_remark'],
            'buyer_barcode_information' => $params['buyer_barcode_information'],
            'research_development_product_number' => $params['research_development_product_number'],
            'repair_order_flag' => $params['received_order_details_repair_order_flag'],
            'goods_name' => $params['received_order_details_goods_name'],
            'goods_quantity' => $params['received_order_details_goods_quantity'],
            'compleat_flag' => $params['compleat_flag'],
            'base_plus_delete_flag' => $params['received_order_details_base_plus_delete_flag'],
            'base_plus_created_at' => $params['received_order_details_base_plus_created_at'] ?? null,
            'base_plus_user_created_by' => $params['received_order_details_base_plus_user_created_by'],
            'base_plus_program_created_by' => $params['received_order_details_base_plus_program_created_by'],
            'base_plus_updated_at' => $params['received_order_details_base_plus_updated_at'] ?? null,
            'base_plus_user_updated_by' => $params['received_order_details_base_plus_user_updated_by'],
            'base_plus_program_updated_by' => $params['received_order_details_base_plus_program_updated_by']
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
}

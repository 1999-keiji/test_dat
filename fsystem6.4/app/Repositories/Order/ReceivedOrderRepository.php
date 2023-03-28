<?php

declare(strict_types=1);

namespace App\Repositories\Order;

use App\Models\Order\ReceivedOrder;

class ReceivedOrderRepository
{
    /**
     * @var \App\Models\Order\ReceivedOrder
     */
    private $model;

    /**
     * @param  \App\Models\Order\ReceivedOrder $model
     * @return void
     */
    public function __construct(ReceivedOrder $model)
    {
        $this->model = $model;
    }

    /**
     * 登録
     *
     * @param  int $sequence_number
     * @param  array $params
     * @return \App\Models\Order\ReceivedOrder
     */
    public function create(int $sequence_number, array $params): ReceivedOrder
    {
        return $this->model->create([
            'sequence_number' => $sequence_number,
            'own_company_code' => $params['received_orders_own_company_code'],
            'recived_order_number' => $params['recived_order_number'],
            'customer_order_number' => $params['customer_order_number'],
            'end_user_order_number' => $params['end_user_order_number'],
            'pickup_type_class' => $params['pickup_type_class'],
            'pickup_type_code' => $params['pickup_type_code'],
            'lc_trade_flag' => $params['lc_trade_flag'],
            'lc_number' => $params['lc_number'],
            'basis_for_recording_sales_class' => $params['basis_for_recording_sales_class'],
            'customer_code' => $params['received_orders_customer_code'],
            'delivery_destination_code' => $params['received_orders_delivery_destination_code'],
            'lease_flag' => $params['lease_flag'],
            'destination_code' => $params['destination_code'],
            'statement_delivery_price_display_class' => $params['statement_delivery_price_display_class'],
            'statement_delivery_class' => $params['statement_delivery_class'],
            'invoice_issue_flag' => $params['invoice_issue_flag'],
            'oversea_flag' => $params['oversea_flag'],
            'seller_code' => $params['seller_code'],
            'seller_name' => $params['seller_name'],
            'customer_staff_name' => $params['customer_staff_name'],
            'suite_name' => $params['suite_name'],
            'suite_class' => $params['suite_class'],
            'suite_statement_delivery_remark' => $params['suite_statement_delivery_remark'],
            'tax_class' => $params['received_orders_tax_class'],
            'currency_code' => $params['received_orders_currency_code'],
            'payment_installments_flag' => $params['payment_installments_flag'],
            'delivery_compleate_flag' => $params['delivery_compleate_flag'],
            'shipment_stop_flag' => $params['shipment_stop_flag'],
            'free_reason_code' => $params['free_reason_code'],
            'maintain_period_flag' => $params['received_orders_maintain_period_flag'],
            'recived_order_date' => $params['recived_order_date'],
            'compleat_flag' => $params['received_orders_compleat_flag'],
            'destinations_segment' => $params['destinations_segment'],
            'end_user_code' => $params['end_user_code'],
            'base_plus_delete_flag' => $params['received_orders_base_plus_delete_flag']
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

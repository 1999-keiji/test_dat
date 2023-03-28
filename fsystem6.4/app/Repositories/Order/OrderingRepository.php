<?php

declare(strict_types=1);

namespace App\Repositories\Order;

use App\Models\Order\Ordering;

class OrderingRepository
{
    /**
     * @var \App\Models\Order\Ordering
     */
    private $model;

    /**
     * @param  \App\Models\Order\Ordering $model
     * @return void
     */
    public function __construct(Ordering $model)
    {
        $this->model = $model;
    }

    /**
     * 連番の最大値を取得
     *
     * @return int
     */
    public function getMaxSequenceNumber(): int
    {
        return $this->model->max('sequence_number') ?: 0;
    }

    /**
     * 登録
     *
     * @param  array $params
     * @return \App\Models\Order\Ordering
     */
    public function create(int $sequence_number, array $params): Ordering
    {
        return $this->model->create([
            'sequence_number' => $sequence_number,
            'process_class' => $params['process_class'],
            'own_company_code' => $params['ordering_own_company_code'],
            'place_order_number' => $params['place_order_number'],
            'small_peace_of_peper_type_class' => $params['small_peace_of_peper_type_class'],
            'small_peace_of_peper_type_code'  => $params['small_peace_of_peper_type_code'],
            'supplier_flag' => $params['supplier_flag'],
            'tax_class' => $params['ordering_tax_class'],
            'purchase_staff_code' => $params['purchase_staff_code'],
            'purchase_staff_name' => $params['purchase_staff_name'],
            'currency_code' => $params['ordering_currency_code'],
            'oversea_pay_terms_class' => $params['oversea_pay_terms_class'],
            'trade_terms_class' => $params['trade_terms_class'],
            'loading_port_code' => $params['loading_port_code'],
            'loading_port_name' => $params['loading_port_name'],
            'trade_means_remark' => $params['trade_means_remark'],
            'maintain_period_flag' => $params['ordering_maintain_period_flag'],
            'supplier_staff_name' => $params['supplier_staff_name'],
            'place_order_work_staff_code' => $params['place_order_work_staff_code'],
            'place_order_work_staff_name' => $params['place_order_work_staff_name'],
            'compleat_flag' => $params['ordering_compleat_flag'],
            'base_plus_delete_flag' => $params['ordering_base_plus_delete_flag']
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

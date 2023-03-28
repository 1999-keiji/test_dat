<?php

declare(strict_types=1);

namespace App\Repositories\Shipment;

use App\Models\Shipment\InvoiceReceiptInfomationLog;

class InvoiceReceiptInfomationLogRepository
{
    /**
     * @var \App\Models\Shipment\InvoiceReceiptInfomationLog
     */
    private $model;

    /**
     * @param  \App\Models\Shipment\InvoiceReceiptInfomationLog $model
     * @return void
     */
    public function __construct(InvoiceReceiptInfomationLog $model)
    {
        $this->model = $model;
    }

    /**
     * 納品受領書出力ログ登録
     *
     * @param  array $order_numbers
     * @return void
     */
    public function create(array $order_numbers): void
    {
        foreach ($order_numbers as $order_number) {
            $invoice_receipt_infomation_log = $this->model
                ->select('sequence_number')
                ->where('order_number', $order_number)
                ->orderBy('sequence_number', 'DESC')
                ->first();

            $this->model->create([
                'order_number' => $order_number,
                'sequence_number' => $invoice_receipt_infomation_log ?
                    $invoice_receipt_infomation_log->sequence_number + 1 : 1
            ]);
        }
    }
}

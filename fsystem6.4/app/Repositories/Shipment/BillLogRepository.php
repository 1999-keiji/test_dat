<?php

declare(strict_types=1);

namespace App\Repositories\Shipment;

use App\Models\Shipment\BillLog;

class BillLogRepository
{
    /**
     * @var \App\Models\Shipment\BillLog
     */
    private $model;

    /**
     * @param  \App\Models\Shipment\BillLog
     * @return void
     */
    public function __construct(BillLog $model)
    {
        $this->model = $model;
    }

    /**
     * 請求書出力ログ登録
     *
     * @param  array $order_numbers
     * @return void
     */
    public function create(array $order_numbers): void
    {
        foreach ($order_numbers as $order_number) {
            $bill_log = $this->model
                ->select('sequence_number')
                ->where('order_number', $order_number)
                ->orderBy('sequence_number', 'DESC')
                ->first();

            $this->model->create([
                'order_number' => $order_number,
                'sequence_number' => $bill_log ? ($bill_log->sequence_number + 1) : 1
            ]);
        }
    }
}

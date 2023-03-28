<?php

declare(strict_types=1);

namespace App\Repositories\Shipment;

use App\Models\Shipment\ShipmentInfomationLog;

class ShipmentInfomationLogRepository
{
    /**
     * @var \App\Models\Shipment\ShipmentInfomationLog
     */
    private $model;

    /**
     * @param  \App\Models\Shipment\ShipmentInfomationLog $model
     * @return void
     */
    public function __construct(ShipmentInfomationLog $model)
    {
        $this->model = $model;
    }

    /**
     * 出荷案内書出力ログ登録
     *
     * @param  array $order_numbers
     * @return void
     */
    public function create(array $order_numbers): void
    {
        foreach ($order_numbers as $order_number) {
            $shipment_infomation_log = $this->model
                ->select('sequence_number')
                ->where('order_number', $order_number)
                ->orderBy('sequence_number', 'DESC')
                ->first();

            $this->model->create([
                'order_number' => $order_number,
                'sequence_number' => $shipment_infomation_log ? ($shipment_infomation_log->sequence_number + 1) : 1
            ]);
        }
    }
}

<?php

declare(strict_types=1);

namespace App\Repositories\Shipment;

use App\Models\Shipment\CollectionRequestLogs;

class CollectionRequestLogRepository
{
    /**
     * @var \App\Models\Order\CollectionRequestLogs
     */
    private $model;

    /**
     * @param \App\Models\Shipment\CollectionRequestLogs $model
     * @return void
     */
    public function __construct(CollectionRequestLogs $model)
    {
        $this->model = $model;
    }

    /**
     * 集荷依頼書出力ログ登録
     *
     * @param  array $order_numbers
     * @return void
     */
    public function create(array $order_numbers): void
    {
        foreach ($order_numbers as $order_number) {
            $collection_request_log = $this->model
                ->select('sequence_number')
                ->where('order_number', $order_number)
                ->orderBy('sequence_number', 'DESC')
                ->first();

            $this->model->create([
                'order_number' => $order_number,
                'sequence_number' => $collection_request_log ? ($collection_request_log->sequence_number + 1) : 1
            ]);
        }
    }
}

<?php

declare(strict_types=1);

namespace App\Repositories\Order;

use Cake\Chronos\Chronos;
use App\Models\Order\Order;
use App\Models\Order\OrderHistory;

class OrderHistoryRepository
{
    /**
     * @var \App\Models\Order\OrderHistory
     */
    private $model;

    /**
     * @param  \App\Models\Order\OrderHistory $model
     * @return void
     */
    public function __construct(OrderHistory $model)
    {
        $this->model = $model;
    }

    /**
     * 注文情報を複製して注文履歴を作成
     *
     * @param  \App\Models\Order\Order $order
     * @return void
     */
    public function createByOrder(Order $order): void
    {
        $now = Chronos::now();
        $params = $order->getAttributes() + ['registration_date' => $now->format('Y-m-d H:i:s')];

        $latest = $this->model
            ->select(['registration_date'])
            ->where('order_number', $order->order_number)
            ->orderBy('registration_date', 'DESC')
            ->first();

        if (! is_null($latest)) {
            $registration_date = Chronos::parse($latest->registration_date);
            if ($registration_date->timestamp >= $now->timestamp) {
                $params['registration_date'] = $registration_date->addSecond()->format('Y-m-d H:i:s');
            }
        }

        $this->model->create($params);
    }
}

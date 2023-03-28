<?php

declare(strict_types=1);

namespace App\Repositories\Order;

use App\Models\Order\RelatedOrder;

class RelatedOrderRepository
{
    /**
     * @var \App\Models\Order\RelatedOrder
     */
    private $model;

    /**
     * @param  \App\Models\Order\RelatedOrder $model
     * @return void
     */
    public function __construct(RelatedOrder $model)
    {
        $this->model = $model;
    }

    /**
     * 注文紐付登録
     *
     * @param  array $params
     * @return \App\Models\Order\RelatedOrder
     */
    public function create(array $params): RelatedOrder
    {
        return $this->model->create($params);
    }
}

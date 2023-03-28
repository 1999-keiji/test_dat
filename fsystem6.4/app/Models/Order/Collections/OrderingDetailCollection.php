<?php

declare(strict_types=1);

namespace App\Models\Order\Collections;

use Illuminate\Database\Eloquent\Collection;

class OrderingDetailCollection extends Collection
{
    /**
     * 過去に取込がスキップされた受発注情報かどうか判定する
     *
     * @param  array $order
     * @return bool
     */
    public function isSkippedOrder(array $order): bool
    {
        return $this->where('place_order_number', $order['place_order_number'])
            ->where('place_order_chapter_number', $order['place_order_chapter_number'])
            ->isNotEmpty();
    }
}

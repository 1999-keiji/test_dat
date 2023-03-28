<?php

declare(strict_types=1);

namespace App\Models\Shipment\Collections;

use Illuminate\Database\Eloquent\Collection;

class InvoiceCollection extends Collection
{
    /**
     * 工場で絞り込み
     *
     * @param  string $factory_code
     * @return \App\Models\Shipment\Collections\InvoiceCollection
     */
    public function filterByFactory(string $factory_code): InvoiceCollection
    {
        return $this->where('factory_code', $factory_code);
    }

    /**
     * 締め処理済のものを抽出
     *
     * @return \App\Models\Shipment\Collections\InvoiceCollection
     */
    public function filterFixed(): InvoiceCollection
    {
        return $this->filter(function ($i) {
            return $i->has_fixed;
        });
    }

    /**
     * 納入年月の降順に並び替え
     *
     * @return \App\Models\Shipment\Collections\InvoiceCollection
     */
    public function sortByDeliveryMonthDesc(): InvoiceCollection
    {
        return $this->sortByDesc('delivery_month');
    }
}

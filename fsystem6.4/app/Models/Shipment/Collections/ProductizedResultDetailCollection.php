<?php

declare(strict_types=1);

namespace App\Models\Shipment\Collections;

use Illuminate\Database\Eloquent\Collection;

class ProductizedResultDetailCollection extends Collection
{
    /**
     * 製品規格を条件にデータを取得
     *
     * @param  array $packaging_style
     * @return \App\Models\Shipment\Collections\ProductizedResultDetailCollection
     */
    public function filterByPackagingStyle(array $packaging_style): ProductizedResultDetailCollection
    {
        return $this->where('number_of_heads', $packaging_style['number_of_heads'])
            ->where('weight_per_number_of_heads', $packaging_style['weight_per_number_of_heads'])
            ->where('input_group', $packaging_style['input_group']);
    }
}

<?php

declare(strict_types=1);

namespace App\Models\Stock\Collections;

use stdClass;
use Illuminate\Database\Eloquent\Collection;

class CarryOverStockCollection extends Collection
{
    /**
     * 工場を条件に抽出
     *
     * @param  \stdClass $factory
     * @return \App\Models\Stock\Collections\CarryOverStockCollection
     */
    public function filterByFactory(stdClass $factory): CarryOverStockCollection
    {
        return $this->where('factory_code', $factory->factory_code);
    }
}

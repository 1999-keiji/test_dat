<?php

declare(strict_types=1);

namespace App\Models\Master\Collections;

use Illuminate\Database\Eloquent\Collection;

class CustomerCollection extends Collection
{
    /**
     * ラベル、値のキーをもつJSON文字列にコンバート
     *
     * @return string
     */
    public function toJsonOptions(): string
    {
        return $this
            ->map(function ($s) {
                return [
                    'label' => $s->customer_abbreviation,
                    'value' => $s->customer_code
                ];
            })
            ->toJson();
    }
}

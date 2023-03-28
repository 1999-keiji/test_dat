<?php

declare(strict_types=1);

namespace App\Models\Master\Collections;

use Illuminate\Database\Eloquent\Collection;

class SpeciesCollection extends Collection
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
                    'label' => $s->species_name,
                    'value' => $s->species_code
                ];
            })
            ->toJson();
    }
    /**
     * API用にレスポンスパラメータの形式にコンバート
     *
     * @return array
     */
    public function toResponseForSearchingApi(): array
    {
        return $this
            ->map(function ($fp) {
                return $fp->toArray();
            })
            ->all();
    }
}

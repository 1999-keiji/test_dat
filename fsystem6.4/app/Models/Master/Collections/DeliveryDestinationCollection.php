<?php

declare(strict_types=1);

namespace App\Models\Master\Collections;

use Illuminate\Database\Eloquent\Collection;

class DeliveryDestinationCollection extends Collection
{
    /**
     * API用にレスポンスパラメータの形式にコンバート
     *
     * @return array
     */
    public function toResponseForSearchingApi(): array
    {
        return $this
            ->map(function ($dd) {
                return [
                    'code' => $dd->delivery_destination_code,
                    'name' => $dd->delivery_destination_abbreviation,
                    'address' => $dd->address,
                    'phone_number' => $dd->phone_number,
                    'end_user' => [
                        'code' => $dd->end_user->end_user_code->value(),
                        'name' => $dd->end_user->end_user_abbreviation
                    ]
                ];
            })
            ->all();
    }
}

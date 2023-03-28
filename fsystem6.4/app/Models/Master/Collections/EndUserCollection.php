<?php

declare(strict_types=1);

namespace App\Models\Master\Collections;

use Illuminate\Database\Eloquent\Collection;
use App\Models\Master\EndUser;

class EndUserCollection extends Collection
{
    /**
     * API用にレスポンスパラメータの形式にコンバート
     *
     * @return array
     */
    public function toResponseForSearchingApi(): array
    {
        return $this
            ->map(function ($eu) {
                return [
                    'code' => $eu->end_user_code->value(),
                    'name' => $eu->end_user_abbreviation,
                    'address' => $eu->address,
                    'phone_number' => $eu->phone_number
                ];
            })
            ->all();
    }

    /**
     * エンドユーザコードで抽出
     *
     * @param  string $end_user_code
     * @return \App\Models\Master\EndUser
     */
    public function findByEndUserCode(string $end_user_code): ?EndUser
    {
        return $this->where('end_user_code', $end_user_code)->first();
    }
}

<?php

declare(strict_types=1);

namespace App\Models\Master\Collections;

use Illuminate\Database\Eloquent\Collection;
use App\Models\Master\EndUser;

class FactoryCollection extends Collection
{
    /**
     * 指定されたエンドユーザに未紐づけの工場マスタを取得
     *
     * @param  \App\Models\Master\EndUser $end_user
     * @return \App\Models\Master\Collections\FactoryCollection
     */
    public function getNotLinkedEndUserFactories(EndUser $end_user): FactoryCollection
    {
        $factory_code_list = $end_user->end_user_factories->pluck('factory_code')->all();
        return $this->reject(function ($f) use ($factory_code_list) {
            return in_array($f->factory_code, $factory_code_list, true);
        });
    }

     /**
     * ラベル、値のキーをもつJSON文字列にコンバート
     *
     * @return string
     */
    public function toJsonOptions(): string
    {
        return $this
            ->map(function ($f) {
                return [
                    'label' => $f->factory_abbreviation,
                    'value' => $f->factory_code
                ];
            })
            ->toJson();
    }
}

<?php

namespace App\Models\Master\Collections;

use Illuminate\Database\Eloquent\Collection;

class UserFactoryCollection extends Collection
{
    /**
     * 工場マスタの情報を抽出してFactoryCollectionでラップし直す
     *
     * @return \App\Models\Master\Collections\FactoryCollection
     */
    public function toFactoryCollection(): FactoryCollection
    {
        $factories = $this->map(function ($uf) {
            return $uf->factory;
        });

        return new FactoryCollection($factories->all());
    }
}

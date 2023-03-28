<?php

declare(strict_types=1);

namespace App\Models\Master;

use App\Models\Model;
use App\Models\Master\Collections\MenuCollection;

class Menu extends Model
{
    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array $models
     * @return \App\Models\Master\Collections\MenuCollection
     */
    public function newCollection(array $models = []): MenuCollection
    {
        return new MenuCollection($models);
    }
}

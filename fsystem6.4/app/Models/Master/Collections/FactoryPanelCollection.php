<?php

declare(strict_types=1);

namespace App\Models\Master\Collections;

use Illuminate\Database\Eloquent\Collection;

class FactoryPanelCollection extends Collection
{
    /**
     * 穴数の逆順にして返却
     *
     * @return \App\Models\Master\Collections\FactoryPanelCollection
     */
    public function sortByNumberOfHolesDesc()
    {
        return $this->sortByDesc('number_of_holes');
    }
}

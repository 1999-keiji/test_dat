<?php

declare(strict_types=1);

namespace App\Models\Plan;

use App\Models\Model;
use App\Models\Plan\Collections\ArrangementDetailStateCollection;

class ArrangementDetailState extends Model
{
    /**
     * @var int
     */
    public const NUMBER_OF_PANEL_STATE_COLUMN = 100;

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array $models
     * @return \App\Models\Plan\Collections\ArrangementDetailStateCollection
     */
    public function newCollection(array $models = []): ArrangementDetailStateCollection
    {
        return new ArrangementDetailStateCollection($models);
    }
}

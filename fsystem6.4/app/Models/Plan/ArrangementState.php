<?php

declare(strict_types=1);

namespace App\Models\Plan;

use App\Models\Model;
use App\Models\Plan\Collections\ArrangementStateCollection;

class ArrangementState extends Model
{
    /**
     * @var int
     */
    public const NUMBER_OF_ROWS_COLUMN = 30;

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array $models
     * @return \App\Models\Plan\Collections\ArrangementStateCollection
     */
    public function newCollection(array $models = []): ArrangementStateCollection
    {
        return new ArrangementStateCollection($models);
    }
}

<?php

declare(strict_types=1);

namespace App\Traits;

use App\Observers\UpdatedDatetimeObserver;

trait UpdatedDatetimeObservable
{
    /**
     * @return void
     */
    public static function bootUpdatedDatetimeObservable()
    {
        self::observe(UpdatedDatetimeObserver::class);
    }
}

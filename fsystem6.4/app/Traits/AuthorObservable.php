<?php

declare(strict_types=1);

namespace App\Traits;

use App\Observers\AuthorObserver;

trait AuthorObservable
{
    /**
     * @return void
     */
    public static function bootAuthorObservable()
    {
        self::observe(AuthorObserver::class);
    }
}

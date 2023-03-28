<?php

declare(strict_types=1);

namespace App\Extension\Logger;

class AccessLogger extends BaseLogger
{
    /**
     * @return void
     */
    public function __construct()
    {
        parent::__construct(storage_path().config('settings.logs.access_log'));
    }
}

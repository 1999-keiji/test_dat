<?php

declare(strict_types=1);

namespace App\Extension\Logger;

class ApplicationLogger extends BaseLogger
{
    /**
     * @return void
     */
    public function __construct()
    {
        parent::__construct(storage_path().config('settings.logs.application_log'));
    }
}

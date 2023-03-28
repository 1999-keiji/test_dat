<?php

declare(strict_types=1);

namespace App\Extension\Logger;

class QueryLogger extends BaseLogger
{
    /**
     * @return void
     */
    public function __construct()
    {
        parent::__construct(storage_path().config('settings.logs.query_log.query_log'));
    }
}

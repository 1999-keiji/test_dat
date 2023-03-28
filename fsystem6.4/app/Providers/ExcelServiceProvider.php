<?php

namespace App\Providers;

use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\ExcelServiceProvider as BaseExcelServiceProvider;
use App\Extension\LaravelExcelWriter;

class ExcelServiceProvider extends BaseExcelServiceProvider
{
    /**
     * Bind writers
     * @return void
     */
    protected function bindWriters()
    {
        // Bind the excel writer
        $this->app->singleton('excel.writer', function ($app)
        {
            return new LaravelExcelWriter(
                $app->make(Response::class),
                $app['files'],
                $app['excel.identifier']
            );
        });
    }
}

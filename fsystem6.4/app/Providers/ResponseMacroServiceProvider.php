<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Routing\ResponseFactory;

class ResponseMacroServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(ResponseFactory $factory)
    {
        $factory->macro('exportPdf', function ($pdf, $file_name) use ($factory) {
            $file_name = rawurlencode($file_name);
            return $factory->make($pdf, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => "attachement; filename=\"{$file_name}.pdf\""
            ]);
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}

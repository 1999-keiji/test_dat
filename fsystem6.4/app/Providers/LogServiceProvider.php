<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Extension\Logger\ApplicationLogger;
use App\Extension\Logger\AccessLogger;
use App\Extension\Logger\QueryLogger;

class LogServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->singleton('App\Extension\Logger\ApplicationLogger', function ($app) {
            return new ApplicationLogger();
        });
        $this->app->singleton('App\Extension\Logger\AccessLogger', function ($app) {
            return new AccessLogger();
        });
    }
}

<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use App\Models\Master\User;
use App\Extension\Logger\QueryLogger;

class AppServiceProvider extends ServiceProvider
{
    /**
     * @var App\Extension\Logger\QueryLogger
     */
    private $query_logger;

    /**
     * @return void
     */
    public function __construct()
    {
        $this->query_logger = new QueryLogger();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::if('canAccess', function (User $user, $path) {
            return $user->canAccess($path);
        });

        Blade::if('canSave', function (User $user, $path) {
            return $user->canSave($path);
        });

        $this->queryLog();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Output SQL log.
     */
    private function queryLog()
    {
        DB::listen(function ($query) {
            $sql = $query->sql;
            if (! in_array(substr($sql, 0, strpos($sql, ' ')), config('settings.logs.query_log.exception_pattern'))) {
                for ($i = 0; $i < count($query->bindings); $i++) {
                    $sql = preg_replace("/\?/", $query->bindings[$i], $sql, 1);
                }

                $this->query_logger->info($sql);
            }
        });
    }
}

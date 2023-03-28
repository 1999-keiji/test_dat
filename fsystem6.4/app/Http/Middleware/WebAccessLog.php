<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Extension\Logger\AccessLogger;

class WebAccessLog
{
    /**
     * @var App\Extension\Logger\AccessLogger
     */
    private $access_logger;

    /**
     * @param  \App\Extension\Logger\AccessLogger $access_logger
     * @return void
     */
    public function __construct(AccessLogger $access_logger)
    {
        $this->access_logger = $access_logger;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $log_id = substr(base_convert(md5(uniqid()), 16, 36), 0, 10);
        $this->access_logger->info('web：start.', [
            'id' => $log_id,
            'user' => (is_null($request->user()) ? '' : $request->user()->user_code),
            'ip' => $request->ip(),
            'action' => Route::currentRouteAction(),
            'session' => $request->session()->getId(),
            'param' => $request->all()
        ]);
        $response = $next($request);
        $this->access_logger->info('web：end.', ['id' => $log_id]);
        return $response;
    }
}

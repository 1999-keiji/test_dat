<?php

namespace App\Http\Middleware;

use Closure;
use App\Exceptions\StocktakingException;
use App\Services\Master\FactoryService;

class RedirectIfStocktaking
{
    /**
     * @var \App\Services\Master\FactoryService
     */
    private $factory_service;

    /**
     * @param  \App\Services\Master\FactoryService $factory_service
     * @return void
     */
    public function __construct(FactoryService $factory_service)
    {
        $this->factory_service = $factory_service;
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
        $factory = $request->route('factory');
        if (is_null($factory) && $request->route('stock')) {
            $factory = $request->route('stock')->factory;
        }
        if (is_null($factory) && $request->route('order')) {
            $factory = $request->route('order')->factory_product->factory;
        }
        if (is_null($factory)) {
            $factory = $this->factory_service->find($request->factory_code);
        }

        if ($factory->stock_manipulation_control->stock_control_flag ?? false) {
            $message = 'disabled to save stock data. factory_code: %s';
            throw new StocktakingException(sprintf($message, $factory->factory_code));
        }

        return $next($request);
    }
}

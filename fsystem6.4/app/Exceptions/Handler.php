<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        DisposedStockException::class,
        MovedStockException::class,
        MultipleWarehouseStockException::class,
        OptimisticLockException::class,
        PageOverException::class,
        StocktakingException::class
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof AuthenticationException) {
            return $request->expectsJson()
                ? response()->json(['message' => 'Unauthenticated.'], 401)
                : redirect()->guest(route('auth.login'))->with([
                    'alert' => trans('auth.expired')
                ]);
        }

        if ($exception instanceof TokenMismatchException) {
            return $request->expectsJson()
                ? response()->json(['message' => 'Unauthenticated.'], 401)
                : redirect()->guest(route('index'));
        }

        if ($exception instanceof AuthorizationException) {
            return $request->expectsJson()
                ? response()->json(['message' => 'Unauthorized.'], 403)
                : redirect()->route('index')->with([
                    'alert' => trans('auth.forbidden')
                ]);
        }

        if ($exception instanceof MethodNotAllowedHttpException) {
            return $request->expectsJson()
                ? response()->json(['message' => 'Not allowed.'], 403)
                : redirect()->route('index')->with([
                    'alert' => trans('auth.forbidden')
                ]);
        }

        if ($exception instanceof StocktakingException) {
            return redirect()->back()->with(['alert' => config('operation.stock.stocktaking.save.block')]);
        }

        return parent::render($request, $exception);
    }
}

<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            if (config('app.env') === 'local') {
                Route::prefix('dev')
                    ->name('dev.')
                    ->group(base_path('routes/dev.php'));
            }
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            App\Http\Middleware\AddContext::class,
            App\Http\Middleware\SetLocale::class,
            App\Http\Middleware\HandleSites::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            'banks', 'cars',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        /*$exceptions->render(function ($x) {
            return response()->json('aa');
        });*/
        //
    })->create();

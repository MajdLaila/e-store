<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Register custom middleware
        $middleware->alias([
            'rate.limit' => \App\Http\Middleware\RateLimitMiddleware::class,
        ]);
        
        // Apply rate limiting to API routes
        $middleware->api(prepend: [
            \App\Http\Middleware\RateLimitMiddleware::class . ':api,60,1'
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Register custom exception handler for API routes
        $exceptions->render(function (Throwable $e, $request) {
            if ($request->is('api/*')) {
                return app(\App\Exceptions\Handler::class)->render($request, $e);
            }
        });
    })->create();

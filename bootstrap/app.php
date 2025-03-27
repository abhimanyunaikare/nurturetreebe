<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use App\Http\Middleware\RoleMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )->withMiddleware(function (Middleware $middleware) {
        // Global Middleware
        $middleware->use([
            \Illuminate\Http\Middleware\HandleCors::class,
        ]);

        // Register Middleware Aliases
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'auth:sanctum' => EnsureFrontendRequestsAreStateful::class, // Sanctum Middleware
        ]);

        // ✅ Register Telescope Middleware
        $middleware->group('web', [
            \Laravel\Telescope\Http\Middleware\Authorize::class, // Allow access to Telescope
        ]);
        
        // API Middleware Group
        $middleware->group('api', [
            EnsureFrontendRequestsAreStateful::class, // Sanctum for SPA authentication
            ThrottleRequests::class . ':api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);
    })    
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();

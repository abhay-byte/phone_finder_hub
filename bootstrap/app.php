<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\EnsureAuthenticated;
use App\Http\Middleware\EnsureSuperAdmin;
use App\Http\Middleware\RedirectIfAuthenticated;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');

        // Exclude logout from CSRF — HTMX SPA navigation causes stale tokens.
        // Logging out is harmless and cannot be exploited via CSRF.
        $middleware->validateCsrfTokens(except: [
            'logout',
        ]);

        // Register custom middleware aliases
        $middleware->alias([
            'auth'        => EnsureAuthenticated::class,
            'guest'       => RedirectIfAuthenticated::class,
            'super_admin' => EnsureSuperAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

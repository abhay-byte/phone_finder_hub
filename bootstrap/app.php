<?php

use App\Http\Middleware\EnsureAuthenticated;
use App\Http\Middleware\EnsureAuthorOrSuperAdmin;
use App\Http\Middleware\EnsureSuperAdmin;
use App\Http\Middleware\RedirectIfAuthenticated;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

/*
 * Render Deployment: Firebase credentials are provided as a base64-encoded
 * secret because multi-line JSON does not paste reliably into Render's
 * environment variable fields. We decode it here before Laravel boots so
 * config/firebase.php can reference the file path.
 */
if ($base64 = $_ENV['FIREBASE_CREDENTIALS_BASE64'] ?? $_SERVER['FIREBASE_CREDENTIALS_BASE64'] ?? null) {
    $credentialsPath = dirname(__DIR__).'/storage/app/firebase-credentials.json';

    if (! file_exists($credentialsPath) || filesize($credentialsPath) === 0) {
        file_put_contents($credentialsPath, base64_decode($base64));
    }

    $_ENV['FIREBASE_CREDENTIALS'] = $credentialsPath;
    $_SERVER['FIREBASE_CREDENTIALS'] = $credentialsPath;
    putenv("FIREBASE_CREDENTIALS={$credentialsPath}");
}

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
            'auth' => EnsureAuthenticated::class,
            'guest' => RedirectIfAuthenticated::class,
            'super_admin' => EnsureSuperAdmin::class,
            'author_admin' => EnsureAuthorOrSuperAdmin::class,
            'role' => \App\Http\Middleware\EnsureRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

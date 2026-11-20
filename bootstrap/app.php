<?php

use App\Http\Middleware\EnsureRole;
use App\Http\Middleware\IsAdmin;
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
        $middleware->alias(['admin' => IsAdmin::class, 'role' => EnsureRole::class]);
        // The old bot endpoint was exempt from CSRF; re-enable only with a signed callback integration.
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Framework defaults.
    })->create();

<?php

namespace App\Http\Middleware;

use App\Services\DemoAccess;
use Closure;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class DemoOrAuthenticate
{
    public function __construct(private readonly DemoAccess $demo)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        if (config('demo.enabled')) {
            $this->demo->enter($request);
            return $next($request);
        }

        // Preserve Laravel's normal authentication and redirect behavior
        // without ever invoking it for a public demo visitor.
        return app(Authenticate::class)->handle($request, $next);
    }
}

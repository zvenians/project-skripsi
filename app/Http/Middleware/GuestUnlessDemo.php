<?php

namespace App\Http\Middleware;

use App\Http\Middleware\RedirectIfAuthenticated;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class GuestUnlessDemo
{
    public function handle(Request $request, Closure $next): Response
    {
        if (config('demo.enabled')) {
            return $next($request);
        }

        return app(RedirectIfAuthenticated::class)->handle($request, $next);
    }
}

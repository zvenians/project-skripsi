<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class DisableRegistrationInDemo
{
    public function handle(Request $request, Closure $next): Response
    {
        if (config('demo.enabled')) {
            return $request->isMethod('GET') ? redirect('/login') : abort(404);
        }

        return $next($request);
    }
}

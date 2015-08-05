<?php

namespace App\Http\Middleware;

use Closure;

class AdminBeforeMiddleware
{
    public function handle($request, Closure $next)
    {
        // Perform action
        return $next($request);
    }
}

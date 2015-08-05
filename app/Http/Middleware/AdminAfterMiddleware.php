<?php

namespace App\Http\Middleware;

use Closure;

class AdminAfterMiddleware
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        // Perform action
        // Session record controller & action in route.php:91
        //return view("admin.$controller.$action");
        return $response;
    }
}

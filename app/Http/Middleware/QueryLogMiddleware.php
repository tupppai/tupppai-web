<?php namespace App\Http\Middleware;

use DB, Closure;

class QueryLogMiddleware
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        logger(DB::getQueryLog());

        return $response;
    }
}

<?php namespace App\Http\Middleware;

use DB, Closure;

class QueueLogMiddleware
{
    public function handle($request, Closure $next)
    {
        $query      = app()->request->query();
        if(!empty($_POST)) {
            $query = array_merge($_POST, $query);
        }
        $query = array_merge($_COOKIE, $query);

        logger($query);
        
        DB::connection()->enableQueryLog();
        $response = $next($request);
        return $response;
    }
}

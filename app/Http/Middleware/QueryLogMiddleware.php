<?php namespace App\Http\Middleware;

use DB, Closure;

class QueryLogMiddleware
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        //logger(DB::getQueryLog(), 'sql');
        $_uid       = session('uid');
        $data       = DB::getQueryLog();

        $prefix     = 'sql';
        $prefix     = $prefix?$prefix.'_': '';
        $host       = app()->request->getHost();
        $ip         = app()->request->ip();
        $method     = app()->request->method();
        $path       = app()->request->path();
        $ajax       = app()->request->ajax();

        $hostname   = $prefix.hostmaps($host);
        \Event::fire(new \App\Events\QueryLogEvent(
            $hostname,
            "[$method][$ajax][$ip][$path][$_uid]",
            $data
        ));

        return $response;
    }
}

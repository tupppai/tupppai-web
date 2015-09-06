<?php namespace App\Http\Middleware;

use DB, Closure, Event;
use App\Events\QueueLogEvent;

class QueueLogMiddleware
{
    public function handle($request, Closure $next)
    {
        $_uid       = session('uid');
        
        $host       = app()->request->getHost();
        $ip         = app()->request->ip();
        $method     = app()->request->method();
        $path       = app()->request->path();
        $ajax       = app()->request->ajax();

        $query      = app()->request->query();
        if(!empty($_POST)) {
            $query = array_merge($_POST, $query);
        }
        $query = array_merge($_COOKIE, $query);

        $hostname   = hostmaps($host);
        Event::fire(new QueueLogEvent($hostname, "[$method][$ajax][$ip][$path][$_uid]", $query));
        
        DB::connection()->enableQueryLog();
        $response = $next($request);
        return $response;
    }
}

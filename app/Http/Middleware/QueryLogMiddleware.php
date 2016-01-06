<?php namespace App\Http\Middleware;

use DB, Closure, Queue, Event;
use App\Jobs\QueueLog;
use App\Events\QueryLogEvent;

class QueryLogMiddleware
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        $_uid       = session('uid');

        #todo 写一个common function 合并
        $host       = app()->request->getHost();
        $path       = app()->request->path();
        $ip         = app()->request->ip();
        $hostname   = hostmaps($host);

        $queries    = DB::getQueryLog();
        Event::fire(new QueryLogEvent($hostname, "[$ip][$path][$_uid]", $queries));
        return $response;
    }
}

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
        $controller = session('controller');
        $action     = session('action');
        //return view("admin.$controller.$action");
        $content = view("admin.$controller.$action")->render();
        $layout  = view("admin.index")->render();

        $response->setContent($layout.$content);
        return $response;
    }
}

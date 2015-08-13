<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
 */
//Home Controller
$app->get('/', function() use ($app) {
    return $app->welcome();
});
//Admin Login Controller
$app->get('login', 'Admin\LoginController@indexAction');

/**
 * 设置默认路由方式
 */
$host       = $app->request->getHost();
$segments   = $app->request->segments();

if( $host && !empty($segments) ){
    $ip         = $app->request->ip();
    $query      = $app->request->query();
    $method     = $app->request->method();
    $path       = $app->request->path();
    $namespace  = ucfirst(hostmaps($host))."\\";

    Log::info("[$method][$namespace][$ip][$path]", $query);

    $name       = $namespace.ucfirst($segments[0]);
    $action     = $segments[1];

    if( isset($segments[2]) ) {
        $segments[2] = '{id}';
        $path = "/".implode("/", $segments);
    }

    $app->addRoute(
        $method, 
        $path, 
        "{$name}Controller@{$action}Action"
    );
}



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
 * 设置默认路由方式,日志
 */
$host       = $app->request->getHost();
$ip         = $app->request->ip();
$query      = $app->request->query();
$method     = $app->request->method();
$path       = $app->request->path();
$hostname   = hostmaps($host);

Log::info("[$method][$hostname][$ip][$path]", $query);

switch($hostname) {
case 'admin':
    $app->group([
            'namespace' => 'App\Http\Controllers',
            'middleware' => ['auth','before','after']
        ], function ($app) {
            router($app);
        }
    );
    break;
case 'android':
case 'main':
    $app->group([
            'namespace' => 'App\Http\Controllers',
            /*'middleware' => ['auth','before','after']*/
        ], function ($app) {
            router($app);
        }
    );
    break;
}

function router($app){
    $host       = $app->request->getHost();
    $method     = $app->request->method();
    $path       = $app->request->path();
    $segments   = $app->request->segments();
    $hostname   = hostmaps($host);

    $namespace  = ucfirst($hostname)."\\";
    $name       = $namespace.ucfirst(controller());
    $action     = action();

    if( isset($segments[2])  ) {
        $segments[2] = '{id}';
        $path = "/".implode("/", $segments);
    }
    $app->addRoute(
        $method, 
        $path, 
        "{$name}Controller@{$action}Action"
    );
}

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
//$app->get('/v1/user/login', 'UserController@login');
$app->get('/', function() use ($app) {
    return $app->welcome();
});

/**
 * Admin Login Controller
 */
$app->get('login', 'Admin\LoginController@indexAction');

/**
 * Android 的接口到这个目录
 */
$app->group([
        'prefix' => 'v1',
        'namespace' => 'App\Http\Controllers\Android'
        /*, 'middleware' => 'auth'*/
    ], function ($app) {
        if( !isset($_SERVER['REDIRECT_URL']) ) {
            return false;
        }

        $url = str_replace('v1', '', $_SERVER['REDIRECT_URL']);
        $url = trim($url,'/');
        $controller = 'index';
        $action = 'index';
        $uri    = explode('/', $url);
        $count  = count($uri);

        if( $count == 1 and $uri[0] != '' ) {
            $controller = $uri[0];
        }
        if( $count > 1 ) {
            $controller = $uri[0];
            $action     = $uri[1];
        }
        $name = ucfirst($controller);

        if( $count <= 2 ) {
            $app->addRoute('GET', "/$controller/$action", "{$name}Controller@{$action}Action");
            $app->addRoute('POST', "/$controller/$action", "{$name}Controller@{$action}Action");
        }
        else  {
            $app->addRoute('GET', "/$controller/$action/{id}", "{$name}Controller@{$action}Action");
            $app->addRoute('POST', "/$controller/$action/{id}", "{$name}Controller@{$action}Action");
        }
    }
);


/**
 * Admin 的页面
 */
$app->group([
        'namespace' => 'App\Http\Controllers\Admin',
        'middleware' => ['auth','before','after']
    ], function ($app) {
        if( !isset($_SERVER['REDIRECT_URL']) ) {
            return false;
        }

        $url = str_replace('v1', '', $_SERVER['REDIRECT_URL']);
        $url = trim($url,'/');
        $controller = 'index';
        $action = 'index';
        $uri    = explode('/', $url);
        $count  = count($uri);

        if( $count == 1 and $uri[0] != '' ) {
            $controller = $uri[0];
        }
        if( $count > 1 ) {
            $controller = $uri[0];
            $action     = $uri[1];
        }
        $name = ucfirst($controller);

        //record visit session one for last visit, another for view render
        //session(['controller'=>$controller,'action'=>$action]);

        if( $count <= 2 ) {
            $app->addRoute('GET', "/$controller/$action", "{$name}Controller@{$action}Action");
            $app->addRoute('POST', "/$controller/$action", "{$name}Controller@{$action}Action");
        }
        else  {
            $app->addRoute('GET', "/$controller/$action/{id}", "{$name}Controller@{$action}Action");
            $app->addRoute('POST', "/$controller/$action/{id}", "{$name}Controller@{$action}Action");
        }
    }
);

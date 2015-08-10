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
        'prefix' => get_prefix('android'),
        'namespace' => get_namespace('android')
        /*, 'middleware' => 'auth'*/
    ], function ($app) {
        if( !isset($_SERVER['REDIRECT_URL']) ) {
            return false;
        }

        set_router($_SERVER['REDIRECT_URL']);
    }
);

//use Log;
/**
 * Admin 的页面
 */
$app->group([
        'namespace' => get_namespace('admin'),
        'middleware' => ['auth','before','after']
    ], function ($app) {
        if( !isset($_SERVER['REDIRECT_URL']) ) {
            return false;
        }

        set_router($_SERVER['REDIRECT_URL']);
    }
);

/**
 * Main 的页面
 */
$app->group([
        'prefix' => get_prefix('main'),
        'namespace' => get_namespace('main')
    ], function ($app) {
        if( !isset($_SERVER['REDIRECT_URL']) ) {
            return false;
        }

        set_router($_SERVER['REDIRECT_URL']);
    }
);

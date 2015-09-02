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

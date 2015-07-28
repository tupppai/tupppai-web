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
use \App\Services\User as sUser;

$app->get('/', function() use ($app) {

    sUser::getUserByUid(1);
    //$user = new mUser;
    //return $user->get_user_by_uids(array(1), 0, 10);
    //return \App\Models\User::all();
    //return $app->welcome();
});

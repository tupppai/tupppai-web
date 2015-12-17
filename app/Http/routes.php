<?php

//Home Controller
$app->get('/', function() use ($app) {
    return 'hello';
});

# 模拟CI配置默认路由方式,日志
$host       = $app->request->getHost();
$hostname   = hostmaps($host);

switch($hostname) {
case 'admin':
    //Admin Login Controller
    $app->get('login', 'Admin\LoginController@indexAction');
    $app->post('login', 'Admin\LoginController@checkAction');
    $app->routeMiddleware([
            'auth' => 'App\Http\Middleware\AdminAuthMiddleware',
            'before' => 'App\Http\Middleware\AdminBeforeMiddleware',
            'after' => 'App\Http\Middleware\AdminAfterMiddleware',
            'log' => 'App\Http\Middleware\QueueLogMiddleware',
            'query' => 'App\Http\Middleware\QueryLogMiddleware'
        ])->group([
            'namespace' => 'App\Http\Controllers',
            'middleware' => ['auth','before','after','log', 'query']
        ], function ($app) {
            router($app);
        }
    );
    break;
case 'api':
    $app->routeMiddleware([
        'log' => 'App\Http\Middleware\QueueLogMiddleware',
        'query' => 'App\Http\Middleware\QueryLogMiddleware'
        ])->group([
            'namespace' => 'App\Http\Controllers',
            'middleware' => ['log', 'query']
        ], function ($app) {
            $app->get('/', function() { return 'hello'; });
            router($app);
        }
    );
    $app->get('/index', function() { return 'hello, welcome join us.'; });
    $app->get('/', function() { return 'hello, welcome join us.'; });
    break;
case 'main':
default:
    $app->routeMiddleware([
        'log' => 'App\Http\Middleware\QueueLogMiddleware',
        'query' => 'App\Http\Middleware\QueryLogMiddleware'
        ])->group([
            'namespace' => 'App\Http\Controllers\Main',
            'middleware' => ['log', 'query']
        ], function ($app) {
            //router($app);
            #thread
            $app->get('populars', 'ThreadController@popular');
            $app->get('categories', 'ThreadController@categories');
            $app->get('timeline', 'ThreadController@timeline');
            $app->get('channels', 'ThreadController@channels');
            $app->get('activities', 'ThreadController@activities');
            #ask
            $app->get('asks', 'AskController@index');
            $app->post('asks/save', 'AskController@save');
            $app->get('asks/{id}', 'AskController@view');
            #reply
            $app->get('replies', 'ReplyController@index');
            $app->post('replies/save', 'ReplyController@save');
            $app->get('replies/ask/{id}', 'ReplyController@ask');
            $app->get('replies/{id}', 'ReplyController@view');
            #comment
            $app->get('comments', 'CommentController@index');
            $app->post('comments/save', 'CommentController@save');
            $app->get('comments/{id}', 'CommentController@view');
            #like
            $app->put('like', 'LikeController@save');
            #inprogress
            $app->get('inprogresses', 'InprogressController@index');
            $app->post('inprogresses/del', 'InprogressController@del');
            $app->get('inprogresses/{id}', 'InprogressController@view');
            #download
            $app->get('download', 'ImageController@download');
            $app->get('record', 'ImageController@record');
            $app->get('upload', 'ImageController@upload');
            $app->post('upload', 'ImageController@upload');
            # users
            $app->get('users', 'UserController@index');
            $app->get('users/{id}', 'UserController@view');
            #user landing
            $app->get('user/code', 'UserController@code');
            $app->get('user/auth', 'UserController@auth');
            # user
            $app->get('user/status', 'UserController@status');
            $app->get('user/logout', 'UserController@logout');
            $app->post('user/login', 'UserController@login');
            $app->post('user/follow', 'UserController@follow');
            $app->post('user/register', 'UserController@register');
            $app->post('user/save', 'UserController@save');
            $app->post('user/forget', 'UserController@forget');
            $app->post('user/updatePassword', 'UserController@updatePassword');
            $app->get('user/uped', 'UserController@uped');
            $app->get('user/collections', 'UserController@collections');
            #message
            $app->get('messages', 'UserController@message');
            #banners
            $app->get('banners', 'BannerController@index');
            #fans
            $app->get('fans', 'UserController@fans');
            #follow
            $app->get('follows', 'UserController@follows');
            #search
            $app->get('search', 'SearchController@index');
            $app->get('search/users', 'SearchController@users');
            $app->get('search/topics', 'SearchController@topics');
            $app->get('search/threads', 'SearchController@threads');
        }
    );
    break;
}

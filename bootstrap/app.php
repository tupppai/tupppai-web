<?php

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/common.php';

Dotenv::load(__DIR__.'/../');

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
*/

$app = new App\Application(
	realpath(__DIR__.'/../')
);

$app->withFacades();

$app->withEloquent();

/*
|--------------------------------------------------------------------------
| Register Container Bindings
|--------------------------------------------------------------------------
|
| Now we will register a few bindings in the service container. We will
| register the exception handler and the console kernel. You may add
| your own bindings here if you like or you can make another file.
|
*/

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
|
| Next, we will register the middleware with the application. These can
| be global middleware that run before and after each request into a
| route or middleware that'll be assigned to some specific routes.
|
*/

$app->middleware([
    #Illuminate\Cookie\Middleware\EncryptCookies::class,
    Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
    Illuminate\Session\Middleware\StartSession::class,
    Illuminate\View\Middleware\ShareErrorsFromSession::class,
    #Laravel\Lumen\Http\Middleware\VerifyCsrfToken::class,
]);

$app->routeMiddleware([
    #'auth' => 'App\Http\Middleware\AdminAuthMiddleware',
]);

/*
|--------------------------------------------------------------------------
| Register Service Providers
|--------------------------------------------------------------------------
|
| Here we will register all of the application's service providers which
| are used to bind services into the container. Service providers are
| totally optional, so you are not required to uncomment this line.
|
*/

/*
 $app->register(App\Providers\AppServiceProvider::class);
 */
// library services register
$app->register(App\Providers\EventServiceProvider::class);
$app->register(App\Providers\LibraryServiceProvider::class);
$app->register(App\Providers\SmsManagerServiceProvider::class);
$app->register(App\Providers\SmsServiceProvider::class);

/*
|--------------------------------------------------------------------------
| Load The Application Routes
|--------------------------------------------------------------------------
|
| Next we will include the routes file so that they can all be added to
| the application. This will provide all of the URLs the application
| can respond to, as well as the controllers that may handle them.
|
 */

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
case 'android':
    $app->routeMiddleware([
        'log' => 'App\Http\Middleware\QueueLogMiddleware',
        'query' => 'App\Http\Middleware\QueryLogMiddleware'
        ])->group([
            'namespace' => 'App\Http\Controllers',
            'middleware' => ['log', 'query']
        ], function ($app) {
            router($app);
        }
    );
    break;
case 'main':
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
            $app->get('timeline', 'ThreadController@timeline');
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
            #comment
            $app->get('like', 'LikeController@save');
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
            # user
            $app->get('user/status', 'UserController@status');
            $app->get('user/login', 'UserController@login');
            $app->get('user/logout', 'UserController@logout');
            $app->post('user/follow', 'UserController@follow');
            $app->post('user/register', 'UserController@register');
            $app->post('user/save', 'UserController@save');
            #message
            $app->get('messages', 'UserController@message');
            #banners
            $app->get('banners', 'BannerController@index');
            #fans
            $app->get('fans', 'UserController@fans');
            #follow
            $app->get('follows', 'UserController@follows');
        }
    );
    break;
}

# 个性配置路由功能
$app->group(['namespace' => 'App\Http\Controllers'], function ($app) {
	require __DIR__.'/../app/Http/routes.php';
});

//load global configs(@skys215)
$app->configure('global');

return $app;

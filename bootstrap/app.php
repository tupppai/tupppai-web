<?php

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../app/common.php';

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
 $app->register(App\Providers\EventServiceProvider::class);
 */
// library services register
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
$ip         = $app->request->ip();
$query      = $app->request->query();
$method     = $app->request->method();
$path       = $app->request->path();
$ajax       = $app->request->ajax();
$hostname   = hostmaps($host);

if(!empty($_POST)) {
    $query = array_merge($_POST, $query);
}
Log::info("[$method][$ajax][$hostname][$ip][$path]", $query);

switch($hostname) {
case 'admin':
    $app->routeMiddleware([
        'auth' => 'App\Http\Middleware\AdminAuthMiddleware',
        'before' => 'App\Http\Middleware\AdminBeforeMiddleware',
        'after' => 'App\Http\Middleware\AdminAfterMiddleware'
        ])->group([
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
        ], function ($app) {
            router($app);
        }
    );
    break;
}

# 个性配置路由功能
$app->group(['namespace' => 'App\Http\Controllers'], function ($app) {
	require __DIR__.'/../app/Http/routes.php';
});

return $app;

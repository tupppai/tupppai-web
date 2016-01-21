<?php
use \LucaDegasperi\OAuth2Server\Facades\Authorizer;
//Home Controller
$app->get('/carbon', ['middleware' => ['oauth'],function() use ($app) {
    $jobs = new \App\Jobs\CheckUserPayReply(2364,8659);
    $jobs->handle();
}]);
$app->get('/callback',function(){
    // 用来客户端向认证服务器申请令牌的HTTP请求的页面，便于发送post请求
    if(\Illuminate\Support\Facades\Input::has('code')){
        return view('callback');
    }
});
//oauth
$app->get('oauth/authorize', ['as'=>'oauth.authorize.post','middleware' => ['check-authorization-params'], function() {
    // 这会让页面跳转到一个授权页面，提供给用户进行操作
    $authParams = Authorizer::getAuthCodeRequestParams();

    $formParams = array_except($authParams,'client');

    $formParams['client_id'] = $authParams['client']->getId();

    $formParams['scope'] = implode(config('oauth2.scope_delimiter'), array_map(function ($scope) {
        return $scope->getId();
    }, $authParams['scopes']));

    return view('oauth.authorization-form', ['params' => $formParams, 'client' => $authParams['client']]);
}]);

$app->post('oauth/authorize', ['as' => 'oauth.authorize.post','middleware' => [ 'check-authorization-params'], function() {
    // 用户通过授权，客户端向认证服务器申请令牌的HTTP请求
    $params = Authorizer::getAuthCodeRequestParams();
    $params['user_id'] = 1;
    $redirectUri = '/';

    // If the user has allowed the client to access its data, redirect back to the client with an auth code.
    if (Request::has('approve')) {
        $redirectUri = Authorizer::issueAuthCode('user', $params['user_id'], $params);
    }

    // If the user has denied the client to access its data, redirect back to the client with an error message.
    if (Request::has('deny')) {
        $redirectUri = Authorizer::authCodeRequestDeniedRedirectUri();
    }

    return redirect($redirectUri);
}]);

$app->post('oauth/access_token', function() {
    // 认证服务器发送的HTTP回复
    header('Content-Type:application/json; charset=utf-8');
    return (Authorizer::issueAccessToken());
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
            $app->get('timeline', 'ThreadController@timeline');
            $app->get('populars', 'ThreadController@popular');
            $app->get('categories', 'CategoryController@index');
            $app->get('channels', 'CategoryController@channels');
            $app->get('activities', 'CategoryController@activities');
            $app->get('activities/{id}', 'CategoryController@show');
            #ask
            $app->get('asks', 'AskController@index');
            $app->post('asks/save', 'AskController@save');
            $app->get('asks/{id}', 'AskController@view');
            #reply
            $app->get('replies', 'ReplyController@index');
            $app->post('replies/save', 'ReplyController@save');
            $app->get('replies/ask/{id}', 'ReplyController@ask');
            $app->get('replies/reply/{id}', 'ReplyController@reply');
            $app->get('replies/{id}', 'ReplyController@view');
            #comment
            $app->get('comments', 'CommentController@index');
            $app->post('comments/save', 'CommentController@save');
            $app->get('comments/{id}', 'CommentController@view');
            #like
            $app->put('like', 'LikeController@save');
            $app->get('love', 'LikeController@love');
            $app->put('love', 'LikeController@love');
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
            #tag
            $app->get('tags', 'TagController@index');
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
            #ping++
            $app->post('ping/pay', 'PingController@pay');
        }
    );
    break;
}

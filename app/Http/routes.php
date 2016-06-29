<?php

# 模拟CI配置默认路由方式,日志
$host     = $app->request->getHost();
$hostname = hostmaps($host);
function robot($hostname) {
	if ($hostname == 'main' && !env('APP_DEBUG')) {
		$robotFileName = 'robots-pc.txt';
	} else {
		$robotFileName = 'robots-other.txt';
	}
	$robotPath = base_path() . '/public/' . $robotFileName;
	ob_clean();
	return file_get_contents($robotPath);
};

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
    $app->get('/robots.txt', function() use ($hostname){
        return robot( $hostname );
    });
    break;
case 'api':
    $app->routeMiddleware([
        // 'sign' => 'App\Http\Middleware\AppSignVerifyMiddleware',
        'log' => 'App\Http\Middleware\QueueLogMiddleware',
        'query' => 'App\Http\Middleware\QueryLogMiddleware'
        ])->group([
            'namespace' => 'App\Http\Controllers',
            'middleware' => [/*'sign',*/ 'log', 'query']
        ], function ($app) {
            $app->get('/', function() { return 'hello'; });
            router($app);
        }
    );
    $app->get('/robots.txt', function() use ($hostname){
        return robot( $hostname );
    });
    $app->get('/index', function() { return 'hello, welcome join us.'; });
    $app->get('/', function() { return 'hello, welcome join us.'; });
    break;
case 'main':
    $app->routeMiddleware([
        'log' => 'App\Http\Middleware\QueueLogMiddleware',
        'query' => 'App\Http\Middleware\QueryLogMiddleware'
        ])->group([
            'namespace' => 'App\Http\Controllers\Main',
            'middleware' => ['log', 'query']
        ],
        function ($app) {
            //router($app);
            #thread
            $app->get('timeline', 'ThreadController@timeline');
            $app->get('populars', 'ThreadController@popular');
            $app->get('categories', 'CategoryController@index');
            $app->get('categories/list', 'CategoryController@lists');
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
            $app->get('user/usedTags', 'UserController@tags');
            #tag
            $app->get('tags/check', 'TagController@check');
            $app->get('tags/userhistory', 'TagController@UserHistoryForTag');
            $app->get('tags/show/{id}', 'TagController@show');
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
            $app->post('pay', 'MoneyController@pay');
            // 微信接入
            $app->get('wechat', 'AuthController@wx');
            // 获取微信js签名
            $app->post('sign', 'AuthController@sign');
            //通过media_id获取资源
            $app->get('/mediasource', 'MediaController@getMedia');

			$app->get('wxactgod/index', 'WXActGodController@index');
			$app->post('wxactgod/upload', 'WXActGodController@multi');
			$app->get('wxactgod/avatars', 'WXActGodController@avatars');
			$app->get('wxactgod/rand', 'WXActGodController@rand');
			//通过media_id获取图片并上传至七牛
			$app->get('getMedia', 'MediaController@getMediaToUploadId');
			//兼职
			$app->get('task/{type}', 'TaskController@index');
			$app->get('task/record/{id}', 'TaskController@record');
			$app->post('task/upload/{id}', 'TaskController@upload');
			$app->post('task/refuse/{id}', 'TaskController@refuse');

			}
		);
		$app->routeMiddleware([
			'log'   => 'App\Http\Middleware\QueueLogMiddleware',
			'query' => 'App\Http\Middleware\QueryLogMiddleware',
		])->group([
			'namespace'  => 'App\Http\Controllers\Main2',
			'middleware' => ['log', 'query'],
			'prefix'     => 'v2',
		], function ($app) {
			//微信登陆
			$app->get('wechat', 'AuthController@wx');
			//首页
			$app->get('populars', 'ThreadController@popular');

			$app->post('asks/save', 'AskController@save');
			$app->get('asks/{id}', 'AskController@view');
			$app->get('timeline', 'ThreadController@timeline');
			//个人中心 求P
			$app->get('asks', 'AskController@index');
			//个人中心 进行中
			#inprogress
			$app->get('inprogresses', 'InprogressController@index');
			$app->post('inprogresses/del', 'InprogressController@del');
			$app->get('inprogresses/{id}', 'InprogressController@view');
			//个人中心 作品   详情页
			#reply
			$app->get('replies', 'ReplyController@index');
			$app->post('replies/save', 'ReplyController@save');
			$app->get('replies/ask/{id}', 'ReplyController@ask');
            $app->get('replies/reply/{id}', 'ReplyController@reply');
			$app->get('replies/tag/{id}', 'ReplyController@tag');
			$app->get('replies/{id}', 'ReplyController@view');
			#thread
			$app->get('thread/{type}/{id}', 'ThreadController@view');
			#comment
			$app->get('comments', 'CommentController@index');
			$app->post('comments/save', 'CommentController@save');
			$app->get('comments/{id}', 'CommentController@view');
			#like
			$app->put('like', 'LikeController@save');
			$app->get('love', 'LikeController@love');
			$app->put('love', 'LikeController@love');
			#user
			$app->get('user', 'UserController@status');
			$app->get('users/{id}', 'UserController@view');
			#upload
			$app->post('upload', 'ImageController@upload');

            #tag
            $app->get( 'tags', 'TagController@getTags');
            $app->get( 'bannerAndTags', 'ThreadController@getBannerAndTags');

            $app->get( 'recommendUser', 'UserController@recommendUser' );
            #reward
            $app->post('thread/reward', 'ThreadController@reward');
		}
		);
		$app->get('/robots.txt', function () use ($hostname) {
			return robot($hostname);
		});
		break;
	default:
		$app->get('/', function () {
			return abort(404);
		});
}

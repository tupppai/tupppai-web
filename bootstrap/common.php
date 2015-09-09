<?php
define('__DS__',  DIRECTORY_SEPARATOR);

/** 友盟相关 **/
//友盟APPKEY  MASTER SECRET
define('UMENG_IOS_APPKEY', '55b1ecdbe0f55a1de9001164');
define('UMENG_IOS_MASTER_SECRET','mbb7qz4rged5kj7j5zclsrqp5a4kkbi8');

define('UMENG_ANDROID_APPKEY', '5534c256e0f55aa48c002909');
define('UMENG_ANDROID_MASTER_SECRET','0s1phi0ghw5wbmik38khols1xbsjwzan');

//友盟SECRET
define('UMENG_SECRET','c8f974673fbd1188aa00218f7d3cbac5');


//玄武短信发送平台 用户名和密码
define('XW_USERNAME', 'szyww@szyww');
define('XW_PASSWORD', 'xw4024');

//微信AppKEY
define('WX_APPID', 'wx86ff6f67a2b9b4b8');
define('WX_APPSECRET', 'c2da31fda3acf1c09c40ee25772b6ca5');

define('VERIFY_MSG', '您好！您在求PS大神的验证码为：::code::。');

define('APP_NAME', '求PS大神');

/**
 * 统一 json 返回格式
 *
 * @param  string $ret  结果。一般 1 表示成功
 * @param  string $info 返回信息。一般出错信息放在这里
 * @param  array  $data 要返回的数据
 * @return string
 */
function json_format($ret = 0, $code = 0, $data=array(), $info='')
{
    #todo: info i18n
    //header("Content-type: application/json");
    return array(
        'ret'   => $ret,
        'code'  => $code,
        'info'  => $info,
        'data'  => $data,
        'token' => Session::getId(),
        'debug' => intval(true),
    );
}

/**
 * 抛出异常，中断操作
 **/
function error($codeName = 0, $info = '', $data = array())
{
    $code = \App\Exceptions\ExceptionCode::getErrCode($codeName);
    if ( !$info ) {
        $info = \App\Exceptions\ExceptionCode::getErrInfo($codeName);
    }
    //todo log error info
    $ret = json_format(0, $code, $data, $info);
    $str = json_encode($ret);

    throw new \App\Exceptions\ServiceException($str);
}

/**
 * 登陆超时
 */
function expire($info = '', $data = array()) {
    $code = \App\Exceptions\ExceptionCode::getErrCode('LOGIN_EXPIRED');
    if ( !$info ) {
        $info = \App\Exceptions\ExceptionCode::getErrInfo('LOGIN_EXPIRED');
    }
    $ret = json_format(2, $code, $data, $info);
    $str = json_encode($ret);

    throw new \App\Exceptions\ServiceException($str);
}

/**
 * 调试工具pr()
 * @param array/object
 */
function pr($arr, $flag = true)
{
    echo "<pre>";
    if(is_object($arr) && property_exists($arr, 'toArray')){
        echo "Object;";
        $data = $arr->toArray();
    }
    else {
        $data = $arr;
    }
    print_r($data);
    if($flag) exit();
}

/**
 * 导入modal的文件
 */
function modal($file, $host = "admin"){
    $modal_path = realpath('.') . __DS__ . '..' . __DS__ . 'resources' . __DS__ . 'modals' . __DS__ . $host;

    include($modal_path.$file.".modal");
}

/**
 * 获取客户端 IP 地址
 *
 * @return string
 */
function get_client_ip()
{
    $ipaddress = '';

    if (!empty($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if (!empty($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if (!empty($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if (!empty($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if (!empty($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';

    return $ipaddress;
}

/**
 * 获取request对象的对应值
 */
function _req($key = '', $default = '')
{
    return isset($_REQUEST[$key]) ? $_REQUEST[$key]: $default;
}

/**
 * 获取当前登陆的用户信息
 */
function _uid($key = 'uid')
{
    $_uid = 0;
    if( !\Session::get('uid') ){
        return $_uid;
        #return error('USER_NOT_EXIST');
    }
    $_uid = \Session::get('uid');
    if( $_uid && $key != 'uid' ){
        $user = \App\Models\User::find($_uid);
        return $user->{$key};
    }
    return $_uid;
}

function watermark2($url, $text="求ps大神\nqiupsdashen.com", $font='', $fontsize='', $fill='white', $dissolve='', $gravity='SouthWest', $dx='', $dy='') {
    $url .= "?watermark/2/text/".base64_encode($text)."";
    if($font)
        $url .= "/font/".base64_encode($font)."";
    if($fontsize)
        $url .= "/fontsize/".$fontsize."";
    if($fill)
        $url .= "/fill/".base64_encode($fill)."";
    if($dissolve)
        $url .= "/dissolve/".$dissolve."";
    if($gravity)
        $url .= "/gravity/".$gravity."";
    if($dx)
        $url .= "/dx/".$dx."";
    if($dy)
        $url .= "/dy/".$dy."";
    return $url;
}

function get_sex_name($sex){
    return $sex==0?'女':'男';
}

if (!function_exists('hostmaps')) {
    #todo: 迁移到config.php
    function hostmaps($host) {
        $hostmaps = array(
            env('ANDROID_HOST') => 'android',
            env('ADMIN_HOST')   => 'admin',
            env('MAIN_HOST')    => 'main'
        );

        return isset($hostmaps[$host])?$hostmaps[$host]: null;
    }
}

if (!function_exists('controller')) {
    function controller($name='index') { 
        $segments   = app()->request->segments();
        if(isset($segments[0])) {
            return $segments[0];
        }
        return $name;
    }
}

if (!function_exists('action')) {
    function action($name='index') {
        $segments   = app()->request->segments();
        if(isset($segments[1])) {
            return $segments[1];
        }
        return $name;
    }
}

if (!function_exists('params')) {
    function params() {
        $segments   = app()->request->segments();
        if(isset($segments[2])) {
            return $segments[2];
        }
        return null;
    }
}

if (!function_exists('router')) {
    function router($app){
        $host       = $app->request->getHost();
        $method     = $app->request->method();
        $path       = $app->request->path();
        $segments   = $app->request->segments();
        $hostname   = hostmaps($host);

        $namespace  = ucfirst($hostname)."\\";
        $controller = controller();
        $action     = action();

        $name       = $namespace.ucfirst($controller);

        #todo: 优化路由
        if( is_array($segments) && isset($segments[2])  ) {
            $segments[2] = '{id}';
            $path = "/".implode("/", $segments);
        }
        $app->addRoute(
            $method, 
            $path, 
            "{$name}Controller@{$action}Action"
        );
    }
}

function encode_location( $province, $city, $location ){
    return $location = $province.'|'.$city.'|'.$location;
}

function decode_location( $location ){
    $lo =array('city','province','location');

    $l = explode('|', $location);
    $l = array_pad( $l, count( $lo ), '');

    return array_combine($lo, $l);
}

/**
 * 匹配手机号码格式
 * @param  [string] $phone [手机号码]
 * @return [int]    1||0   [1:匹配成功]
 */
function match_phone_format($phone)
{
    if (strlen($phone)==11) {
        return preg_match("/1[3|5|7|8|][0-9]{9}/", $phone);
    } else {
        return 0;
    }
}

function match_username_format($username)
{
    return preg_match('/^[a-zA-Z][a-zA-Z0-9]{5,15}$/', $username);
}



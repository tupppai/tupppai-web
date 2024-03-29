<?php

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

function message($info, $data) {
    $str = json_format(1, 0, $data, $info);

    throw new \App\Exceptions\ServiceException($str);
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

    $ret['query'] = app()->request->query();
    logger($ret, 'error');
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

    $data['wx_appid'] = env('MP_APPID');
    $ret = json_format(2, $code, $data, $info);
    $str = json_encode($ret);

    logger($ret, 'error');
    throw new \App\Exceptions\ServiceException($str);
}

/**
 * 记录系统日志
 */
function logger($data = array(), $prefix = null ) {
    $_uid       = session('uid');

    $prefix     = $prefix?$prefix.'_': '';
    $host       = app()->request->getHost();
    $ip         = app()->request->ip();
    $method     = app()->request->method();
    $path       = app()->request->path();
    $ajax       = app()->request->ajax();

    $hostname   = $prefix.hostmaps($host);
    \Event::fire(new \App\Events\QueueLogEvent(
        $hostname,
        "[$method][$ajax][$ip][$path][$_uid]",
        $data
    ));
}

/**
 * 支持自动解析 Event -> handle
 */
function fire($listen, $arguments = [])
{
    return \App\Handles\Handle::fire($listen, $arguments);
}

/**
 * 支持自动解析 Event -> handle
 */
function listen($listen, $arguments = [])
{
    $syncEvent = new \App\Events\HandleSyncEvent($listen, $arguments);
    return \App\Handles\Handle::listen($syncEvent);
}

/**
 * 签名对比
 */
function sign($args, $verify){
    ksort($args);
    $args = array_map(function($n){
        if(is_array($n)){
            sort($n);
            return strtolower(json_encode($n,JSON_NUMERIC_CHECK));
        }
        return strtolower($n);
    },$args);
    $args = implode('',$args);
    $sign = config('global.SIGN');
    $toDay = (\Carbon\Carbon::today()->day);
    if($verify == strtolower(md5(strtolower(md5($args.$sign.$toDay))))){
        return true;
    }
    return false;
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
function _uid($key = 'uid', $wx = false)
{
    $uid = session('uid');
    return $uid?$uid:0;
}

function watermark2($url, $text="图派\ntupppai.com", $font='', $fontsize='', $fill='white', $dissolve='', $gravity='SouthWest', $dx='', $dy='') {
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
        //获取二级域名
        $domains = explode(".",$host);
        $subdomain = $domains[0];
        if( count($domains) <= 2 ){
            $subdomain = 'www';
        }

        $hostmaps = array(
            env('API_DOMAIN')     => 'api',
            env('ADMIN_DOMAIN')   => 'admin',
            env('PC_DOMAIN', 'www') => 'main'
        );

        return isset($hostmaps[$subdomain])?$hostmaps[$subdomain]: null;
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
        //todo: use query()
        //$segments   = app()->request->query();
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

        $CONTROLLER_NAMESPACE = 'App\\Http\\Controllers\\';
        #todo: 优化路由
        if( is_array($segments) && isset($segments[2])  ) {
            $segments[2] = '{id}';
            $path = "/".implode("/", $segments);
        }

        if( !class_exists($CONTROLLER_NAMESPACE.$name.'Controller') ){
            return false;
            // response($name.'Controller doesn\'t exist.', 404);
        }
        if( !method_exists($CONTROLLER_NAMESPACE.$name.'Controller',$action.'Action') ){
            return false;
            //response($name.'Controller doesn\'t have method \''.$action.'Action\'', 404);
        }
        $app->addRoute(
            $method,
            $path,
            "{$name}Controller@{$action}Action"
        );
    }
}

function camel_to_lower($str){
    return strtolower(preg_replace('/((?<=[a-z])(?=[A-Z]))/', '_', $str));
}

function encode_location( $province, $city, $location ){
    return $location = $city.'|'.$province.'|'.$location;
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
        return preg_match("/1[35789][0-9]{9}/", $phone);
    } else {
        return 0;
    }
}

function match_username_format($username)
{
    return preg_match('/^[a-zA-Z][a-zA-Z0-9]{5,15}$/', $username);
}

function crlf2br( $string ){
    $newLineArray = array('\r\n','\n\r','\n','\r');
    $newString = str_replace($newLineArray,'',nl2br($string));
    return $newString;
}

/**
 * 通过Emojione拓展将pc和客户端的emoji表情转换为 :shortname:的格式进行存储
 * 若不匹配，则转换为[emoji]字符
 *
 * @param [string] $content
 * @author brandwang
 */
function emoji_to_shortname($content) {
    $content = \Emojione\Emojione::toShort($content);
    // 未被拓展匹配的转换为[emoji]字符
    $content = preg_replace("/[\xF0-\xF7][\x80-\xBF]{3}/", "[emoji]", $content);

    return $content;
}

/**
 * 主要用于向客户端返回内容
 * 通过Emojione拓展将数据库中的shortname格式转换为unicode返回给客户端
 * @param [string] $content
 */
function shortname_to_unicode($content) {
    $content = \Emojione\Emojione::shortnameToUnicode($content);

    return $content;
}
/*
 * 计算一个指定范围-随机浮点数
 * */
function randomFloat($min = 0, $max = 1) {
    return round($min + mt_rand() / mt_getrandmax() * ($max - $min),2);
}

/*
 * 钱币格式化
 * @param [string] $money
 * */
function money_convert($money, $type = '', $locale = 'zh_CN') {
    $money /= config('global.MULTIPLIER');
    if ('money' == $type) {
        setlocale(LC_MONETARY, $locale);
        $money = money_format('%n', $money);
    }
    return $money;
}

/**
 * http请求
 */
function http_get($url) {

    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_HEADER,0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    $res = curl_exec($ch);
    curl_close($ch);

    return json_decode($res, true);
}

/**
 * http_post请求
 */
function http_post($url, $post = '', $cookie = '', $returnCookie = 0) {
    $this->format_post_data($post);
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)');
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
    curl_setopt($curl, CURLOPT_REFERER, "http://XXX");
    if ($post) {
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post));
    }
    if ($cookie) {
        curl_setopt($curl, CURLOPT_COOKIE, $cookie);
    }
    curl_setopt($curl, CURLOPT_HEADER, $returnCookie);
    curl_setopt($curl, CURLOPT_TIMEOUT, 10);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec($curl);
    if (curl_errno($curl)) {
        return curl_error($curl);
    }
    curl_close($curl);
    if ($returnCookie) {
        list($header, $body) = explode("\r\n\r\n", $data, 2);
        preg_match_all("/Set\\-Cookie:([^;]*);/", $header, $matches);
        $info['cookie'] = substr($matches[1][0], 1);
        $info['content'] = $body;
        return $info;
    } else {
        $data = json_decode($data, true);
        $data = !empty($data) ? $data : null;
        return $data;
    }
}

    function GrabImage($url, $ext = null) {
        if ($url == '') {
            return false;
        }
        if(!$ext){
            $ext = strrchr($url, '.');
            if ($ext != '.gif' && $ext != '.jpg' && $ext != '.png') {
                $headers = get_headers($url,1);
                $type   = explode('/', $headers['Content-Type']);
                $ext = isset($type[1]) ? $type[1] : false;
                if($ext != 'gif' && $ext != 'jpeg' && $ext != 'png') {
                    return false;
                }
                $ext = '.'.$ext;
            }
        }else{
            $ext = '.'.$ext;
        }
        $filename = storage_path('uploads/') .date('dMYHis') . $ext;
        ob_start();
        $result = false;
        $i = 1;
        while (!$result) {
            if ($i > 2) {
                break;
            }
            $result = @readfile($url);
            $i++;
        }
        $img = ob_get_contents();
        ob_end_clean();
        $fp2 = fopen($filename, 'a+');
        chmod($filename, 0777);
        fwrite($fp2, $img);
        fclose($fp2);

        return $filename;
    }

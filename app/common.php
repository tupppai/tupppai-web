<?php
define('__DS__',  DIRECTORY_SEPARATOR);

/** 友盟相关 **/
//友盟APPKEY  MASTER SECRET
define('UMENG_IOS_APPKEY', '5545afea67e58eb5d7001cd3');
define('UMENG_IOS_MASTER_SECRET','sdbmz0djxqf0jaiupfdtxmf2y0wggcsl');

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
 * 得到以 Y-m-d H:i:s 形式的时间
 *
 * @return string
 */
function now_str()
{
    return date('Y-m-d H:i:s');
}

function set_date($time)
{
    return date('Y-m-d H:i:s', $time);
}

function time_ymd($time){
    return date('Y-m-d');
}

function str_time($str)
{
    return strtotime($str);
}

function get_day($time){
    return floor($time / (24 * 60 * 60));
}

function get_hour($time){
    return sprintf("%.2f", $time / (60 * 60));
}

function get_time($time){
    $today  = date("Ymd");
    $now    = strtotime($today);
    $work   = $now + $time;

    return date("H:i:s", $work);
}

function get_money($time, $rate, $type = 'hour') {
    switch($type){
    case 'hour':
        return floatval(get_hour($time)) * floatval($rate);
    }
}

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
    return json_encode(array(
        'ret'   => $ret,
        'code'  => $code,
        'info'  => $info,
        'data'  => $data,
        'token' => Session::getId(),
        'debug' => intval(true),
    ));
}
/**
 * 抛出异常，中断操作
 **/
function error($codeName = 0, $info = '', $data = array())
{
    $code = \App\Services\ServiceBase::getErrCode($codeName);
    if ( !$info ) {
        $info = \App\Services\ServiceBase::getErrInfo($codeName);
    }
    //todo log error info
    $ret = json_format(0, $code, $data, $info);
    throw new \App\Exceptions\ServiceException($ret);
}

/**
 * 产生随机字串，可用来自动生成密码
 * 默认长度6位 字母和数字混合 支持中文
 *
 * from thinkphp
 *
 * @param string $len 长度
 * @param string $type 字串类型
 * 0 字母 1 数字 其它 混合
 * @param string $addChars 额外字符
 * @return string
 */
function rand_string($len=6, $type='', $addChars='') {
    $str ='';
    switch($type) {
        case 0:
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' . $addChars;
            break;
        case 1:
            $chars = str_repeat('0123456789', 3);
            break;
        case 2:
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' . $addChars;
            break;
        case 3:
            $chars = 'abcdefghijklmnopqrstuvwxyz' . $addChars;
            break;
        case 4:
            $chars = "们以我到他会作时要动国产的一是工就年阶义发成部民可出能方进在了不和有大这主中人上为来分生对于学下级地个用同行面说种过命度革而多子后自社加小机也经力线本电高量长党得实家定深法表着水理化争现所二起政三好十战无农使性前等反体合斗路图把结第里正新开论之物从当两些还天资事队批点育重其思与间内去因件日利相由压员气业代全组数果期导平各基或月毛然如应形想制心样干都向变关问比展那它最及外没看治提五解系林者米群头意只明四道马认次文通但条较克又公孔领军流入接席位情运器并飞原油放立题质指建区验活众很教决特此常石强极土少已根共直团统式转别造切九你取西持总料连任志观调七么山程百报更见必真保热委手改管处己将修支识病象几先老光专什六型具示复安带每东增则完风回南广劳轮科北打积车计给节做务被整联步类集号列温装即毫知轴研单色坚据速防史拉世设达尔场织历花受求传口断况采精金界品判参层止边清至万确究书术状厂须离再目海交权且儿青才证低越际八试规斯近注办布门铁需走议县兵固除般引齿千胜细影济白格效置推空配刀叶率述今选养德话查差半敌始片施响收华觉备名红续均药标记难存测士身紧液派准斤角降维板许破述技消底床田势端感往神便贺村构照容非搞亚磨族火段算适讲按值美态黄易彪服早班麦削信排台声该击素张密害侯草何树肥继右属市严径螺检左页抗苏显苦英快称坏移约巴材省黑武培著河帝仅针怎植京助升王眼她抓含苗副杂普谈围食射源例致酸旧却充足短划剂宣环落首尺波承粉践府鱼随考刻靠够满夫失包住促枝局菌杆周护岩师举曲春元超负砂封换太模贫减阳扬江析亩木言球朝医校古呢稻宋听唯输滑站另卫字鼓刚写刘微略范供阿块某功套友限项余倒卷创律雨让骨远帮初皮播优占死毒圈伟季训控激找叫云互跟裂粮粒母练塞钢顶策双留误础吸阻故寸盾晚丝女散焊功株亲院冷彻弹错散商视艺灭版烈零室轻血倍缺厘泵察绝富城冲喷壤简否柱李望盘磁雄似困巩益洲脱投送奴侧润盖挥距触星松送获兴独官混纪依未突架宽冬章湿偏纹吃执阀矿寨责熟稳夺硬价努翻奇甲预职评读背协损棉侵灰虽矛厚罗泥辟告卵箱掌氧恩爱停曾溶营终纲孟钱待尽俄缩沙退陈讨奋械载胞幼哪剥迫旋征槽倒握担仍呀鲜吧卡粗介钻逐弱脚怕盐末阴丰雾冠丙街莱贝辐肠付吉渗瑞惊顿挤秒悬姆烂森糖圣凹陶词迟蚕亿矩康遵牧遭幅园腔订香肉弟屋敏恢忘编印蜂急拿扩伤飞露核缘游振操央伍域甚迅辉异序免纸夜乡久隶缸夹念兰".
"映沟乙吗儒杀汽磷艰晶插埃燃欢铁补咱芽永瓦倾阵碳演威附牙芽永瓦斜灌欧献顺猪洋腐请透司危括脉宜笑若尾束壮暴企菜穗楚汉愈绿拖牛份染既秋遍锻玉夏疗尖殖井费州访吹荣铜沿替滚客召旱悟刺脑措贯藏敢令隙炉壳硫煤迎铸粘探临薄旬善福纵择礼愿伏残雷延烟句纯渐耕跑泽慢栽鲁赤繁境潮横掉锥希池败船假亮谓托伙哲怀割摆贡呈劲财仪沉炼麻罪祖息车穿货销齐鼠抽画饲龙库守筑房歌寒喜哥洗蚀废纳腹乎录镜妇恶脂庄擦险赞钟摇典柄辩竹谷卖乱虚桥奥伯赶垂途额壁网截野遗静谋弄挂课镇妄盛耐援扎虑键归符庆聚绕摩忙舞遇索顾胶羊湖钉仁音迹碎伸灯避泛亡答勇频皇柳哈揭甘诺概宪浓岛袭谁洪谢炮浇斑讯懂灵蛋闭孩释乳巨徒私银伊景坦累匀霉杜乐勒隔弯绩招绍胡呼痛峰零柴簧午跳居尚丁秦稍追梁折耗碱殊岗挖氏刃剧堆赫荷胸衡勤膜篇登驻案刊秧缓凸役剪川雪链渔啦脸户洛孢勃盟买杨宗焦赛旗滤硅炭股坐蒸凝竟陷枪黎救冒暗洞犯筒您宋弧爆谬涂味津臂障褐陆啊健尊豆拔莫抵桑坡缝警挑污冰柬嘴啥饭塑寄赵喊垫丹渡耳刨虎笔稀昆浪萨茶滴浅拥穴覆伦娘吨浸袖珠雌妈紫戏塔锤震岁貌洁剖牢锋疑霸闪埔猛诉刷狠忽灾闹乔唐漏闻沈熔氯荒茎男凡抢像浆旁玻亦忠唱蒙予纷捕锁尤乘乌智淡允叛畜俘摸锈扫毕璃宝芯爷鉴秘净蒋钙肩腾枯抛轨堂拌爸循诱祝励肯酒绳穷塘燥泡袋朗喂铝软渠颗惯贸粪综墙趋彼届墨碍启逆卸航衣孙龄岭骗休借".$addChars;
            break;
        default :
            // 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
            $chars = 'ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789' . $addChars;
            break;
    }
    if ($len>10) {//位数过长重复字符串一定次数
        $chars = ($type==1 ? str_repeat($chars, $len) : str_repeat($chars, 5));
    }
    if ($type!=4) {
        $chars   =   str_shuffle($chars);
        $str     =   substr($chars, 0, $len);
    } else {
        // 中文随机字
        for ($i=0; $i<$len; $i++){
            $str .= mb_substr($chars, floor(mt_rand(0, mb_strlen($chars,'utf-8')-1)), 1, 'utf-8');
        }
    }

    return $str;
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
 * 把时间转换为 xxx 小时前 类似的友好时间
 *
 * @param string $time_int 时间
 * @return string
 */
function time_in_ago($time_int)
{
    $show_time  = $time_int;
    $now_time   = time();

    $dur = $now_time - $show_time;
    if($dur < 0){
        return date("Y-m-d H:i", $time_int);
    }else{
        if($dur < 60){
            return $dur.'秒前';
        }else{
            if($dur < 3600){
                return floor($dur/60).'分钟前';
            }else{
                if($dur < 86400){
                    return floor($dur/3600).'小时前';
                }else{
                    if($dur < 259200){//3天内
                        return floor($dur/86400).'天前';
                    }else{
                        return date("Y-m-d H:i", $time_int);
                        //return $time_str;
                    }
                }
            }
        }
    }
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
    if(\Session::get('uid')){
        $_uid = \Session::get('uid');
    }
    if($key != 'uid'){
        $user = \Psgod\Models\User::find($_uid);
        return $user->{$key};
    }
    return $_uid;
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

function match_email_format($email)
{
    return preg_match('/^[a-z0-9]+([._\\-]*[a-z0-9])*@([a-z0-9]+[-a-z0-9]*[a-z0-9]+.){1,63}[a-z0-9]+$/', $email);
}

function watermark1($url, $dissolve=70, $gravity='SouthWest', $dx=10, $dy=10) {
    $width = get_image_width($url);
    if($width<=320) {
        $image = 'http://7u2spr.com1.z0.glb.clouddn.com/20150430-1524265541d8aadb956.png';
        //$image = 'http://7u2spr.com1.z0.glb.clouddn.com/20150430-1557525541e080d9259.png';
    } else if($width<=640) {
        $image = 'http://7u2spr.com1.z0.glb.clouddn.com/20150430-1523275541d86f9294d.png';
        //$image = '';
    } else if($width<=1280) {
        $image = 'http://7u2spr.com1.z0.glb.clouddn.com/20150430-1520395541d7c724d4c.png';
        //$image = '';
    } else if($width<=1920) {
        $image = 'http://7u2spr.com1.z0.glb.clouddn.com/20150430-1518185541d73adaaed.png';
        //$image = '';
    } else {
        $image = 'http://7u2spr.com1.z0.glb.clouddn.com/20150430-1503095541d3ad7aef8.png';
        //$image = '';
    }

    $url .= '?watermark/1/image/'.base64_encode($image).'';
    if($dissolve)
        $url .= '/dissolve/'.$dissolve;
    if($gravity)
        $url .= '/gravity/'.$gravity;
    if($dx)
        $url .= '/dx/'.$dx.'';
    if($dy)
        $url .= '/dy/'.$dy.'';
    return $url;
}

function name_add_mark($name, $mark='mark') {
    $index = strpos($name, '.');
    if($index) {
        return substr_replace($name, $mark, $index, 0);
    }
}

function save_image_wm($url, $namemark){
    $bucket = 'pstest';
    $sk = 'xDdcSRN2s0hGw3djcBKnrOMCHN8jWEQgjBCxbisr';
    $ak = 'eifvG4u-0Wp9KZgsev_9MyBiBRXHcOFaeSOXJ19f';

    $saveas = base64_encode($bucket.':'.$namemark);
    $url .= '|saveas/'.$saveas;

    $sign = hash_hmac('sha1', str_replace('http://', '', $url), $sk, true);
    $sign = $ak.':'.urlsafe_base64_encode($sign);
    $url .= '/sign/'.$sign;

    $arr = json_decode(file_get_contents($url), true);
    if(in_array('error', $arr))
        return false;
    else
        return $arr;
}

function urlsafe_base64_encode($data) {
   $data = base64_encode($data);
   $data = str_replace(array('+','/'),array('-','_'),$data);
   return $data;
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

function get_ext_from_url($url) {
    $f = strrpos($url, '.');            // 开头
    $e = strrpos($url, '?');            // 结尾
    $e?$e:$e = strlen($url);            // 找不到就赋值为url的结尾
    return substr($url, $f, $e-$f);
}

function get_sex_name($sex){
    return $sex==0?'女':'男';
}

function match_url_format($url){
    return preg_match('/^http[s]?:\/\/'.
        '(([0-9]{1,3}\.){3}[0-9]{1,3}'. // IP形式的URL- 199.194.52.184
        '|'. // 允许IP和DOMAIN（域名）
        '([0-9a-z_!~*\'()-]+\.)*'. // 域名- www.
        '([0-9a-z][0-9a-z-]{0,61})?[0-9a-z]\.'. // 二级域名
        '[a-z]{2,13})'.  // first level domain- .com or .museum
        '(:[0-9]{1,4})?'.  // 端口- :80
        '((\/\?)|'.  // a slash isn't required if there is no file name
        '(\/[0-9a-zA-Z_!~\'\(\)\[\]\.;\?:@&=\+\$,%#-\/^\*\|]*)?)$/',
    $url) == 1;
}

function url_cut_tail($url) {
    $index = stripos($url, '?');
    if($index)
        return substr($url,0,$index);
    return $url;
}

if (!function_exists('config_path')) {
    /**
     * Get the configuration path.
     *
     * @param  string  $path
     * @return string
     */
    function config_path($path = '')
    {
        return app()->make('path.config').($path ? DIRECTORY_SEPARATOR.$path : $path);
    }
}

function get_prefix($type) {
    return array(
        'android'=>'v1',
        'main'=>'main',
        'admin'=>''
    )[$type];
}

function get_namespace($type) {
    return array(
        'android'=>'\App\Http\Controllers\Android',
        'admin'=>'\App\Http\Controllers\Admin',
        'main'=>'\App\Http\Controllers\Main'
    )[$type];
}

if (!function_exists('set_router')) {
    /**
     * Set the router
     */
    function set_router($url, $type = null) {
        $url = explode('?', $url)[0];
        $url = str_replace('v1', '', $url);
        $url = str_replace('main', '', $url);
        $url = trim($url,'/');

        $controller = 'index';
        $action = 'index';
        $uri    = explode('/', $url);
        $count  = count($uri);
        $namespace = '';
        $prefix    = '';

        if($type) {
            $namespace  = get_namespace($type).'\\';
            $prefix     = '/'.get_prefix($type);
        }

        if( $count == 1 and $uri[0] != '' ) {
            $controller = $uri[0];
        }
        if( $count > 1 ) {
            $controller = $uri[0];
            $action     = $uri[1];
        }
        $name = $namespace.ucfirst($controller);

        if( $count <= 2 ) {
            app()->addRoute('GET', "$prefix/$controller/$action", "{$name}Controller@{$action}Action");
            app()->addRoute('POST', "$prefix/$controller/$action", "{$name}Controller@{$action}Action");
        }
        else  {
            app()->addRoute('GET', "$prefix/$controller/$action/{id}", "{$name}Controller@{$action}Action");
            app()->addRoute('POST', "$prefix/$controller/$action/{id}", "{$name}Controller@{$action}Action");
        }
    }
}

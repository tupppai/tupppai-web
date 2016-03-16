<?php namespace App\Http\Controllers\Main;

use App\Models\App;
use App\Models\ActionLog;

use App\Services\User as sUser;
use App\Services\UserLanding as sUserLanding;
use Redirect,Input,Session,Log;

class AuthController extends ControllerBase {

    public $_allow = array('*');

    const EXPIRE_IN = 7200;

    public function wx()
    {
        $appid  = env('MP_APPID');  
        $secret = env('MP_APPSECRET');
        $code   = $this->get('code', 'string');  
        $hash   = $this->get('hash', 'string');
        if (!$code) {
            return error('KEY_NOT_EXIST');
        }

        $time   = time();

        // 1. 获取session中的token
        $openid         = session('open_id');
        $token          = session('token');
        $token_expire   = session('token_expire');

        // 2. 若session超时则需要从微信服务器中获取
        if($token_expire < $time || !$openid) {
            $token_url  = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$secret.'&code='.$code.'&grant_type=authorization_code';
            $token_obj  = http_get($token_url);
            
            if (!$token_obj) {
                return error('KEY_NOT_EXIST');
            }           
            if (!isset($token_obj['access_token'])) {
                Log::info('access_token', array($token_obj));
            }

            $token      = $token_obj['access_token'];  
            $openid     = $token_obj['openid'];  

            if ($token && $openid) {
                session(['open_id' => $openid, 'token'=>$token, 'token_expire'=>$time + self::EXPIRE_IN]);
            }
        }
        
        // 3. 根据openid和access_token查询用户信息  
        $user_url   = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$token.'&openid='.$openid.'&lang=zh_CN';  
        $data       = http_get($user_url);
        if (!$data) {
    	    return error('KEY_NOT_EXIST'); 
        }

        $type = 'weixin_mp';
        
        $user_landing = sUserLanding::getUserByOpenid($openid, $type);
        if($user_landing && sUser::getUserByUid($user_landing->uid)) {
            session( [ 'uid' => $user_landing->uid ] );
            return redirect('/'.$hash);
            //return $this->output();
        }

        $avatar   = $data['headimgurl'];
        $mobile   = '';
        $password = '';
        /*v1.0.5 允许不传昵称 默认为手机号码_随机字符串*/
        $nickname = $data['nickname'];
        $username = '用户_'.hash('crc32b',$mobile.mt_rand());
        $location = $data['country'];
        $city     = '';
        $province = '';
        $sex      = $data['sex']==1?1:0;
        $unionid  = $data['unionid'];

        $user = sUser::addUser(
            $type,
            $username,
            $password,
            $nickname,
            $mobile,
            $location,
            $avatar,
            $sex,
            $openid
        );
        $landing = sUserLanding::bindUser($user->uid, $openid, $nickname ,$type, $unionid);

        if($user->id) {
            session( [ 'uid' => $user->uid ] );
        }     

        return redirect('/'.$hash);
    }


    public function sign() {
        $appid  = env('MP_APPID');  
        $secret = env('MP_APPSECRET');  
        $time = time();

        // 1. 从session中获取jsapi 的token
        $ticket         = session('ticket');
        $ticket_expire  = session('ticket_expire');

        // 2. 如果超时则重新获取
        if($ticket_expire < $time) {
            $token_url  = 'https://api.weixin.qq.com/cgi-bin/token?appid='.$appid.'&secret='.$secret.'&grant_type=client_credential';
            $token_obj  = http_get($token_url);
	
            if (!$token_obj) {
                return error('KEY_NOT_EXIST');
            }
            $token      = $token_obj['access_token'];  
        
            $jsapi_url  = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$token";

            $data       = http_get($jsapi_url);
            if ($data) {
                session(['ticket' => $data['ticket'], 'ticket_expire'=>$time]);
            }
        }

        // 3. 通过url获得签名
        $url = $this->post('url', 'string');
        
        $timestamp  = time();
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $nonceStr = "";
        $length = 16;
        for ($i = 0; $i < $length; $i++) {
            $nonceStr .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }

        $jsapiTicket = session('ticket');
        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

        $signature = sha1($string);

        $signPackage = array(
          "appId"     => $appid,
          "nonceStr"  => $nonceStr,
          "timestamp" => $timestamp,
          "url"       => $url,
          "signature" => $signature,
          "rawString" => $string
        );

        Log::info('signature', $signPackage);
        return $this->output($signPackage);
    }
}

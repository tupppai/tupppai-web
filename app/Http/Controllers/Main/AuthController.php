<?php namespace App\Http\Controllers\Main;

use App\Models\App;
use App\Models\ActionLog;

use App\Services\User as sUser;
use App\Services\UserLanding as sUserLanding;
use App\Services\WxActGod;
use Redirect,Input,Session,Log;
use App\Facades\EasyWeChat;

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

        $openid         = session('open_id');

        $app = EasyWeChat::getFacadeRoot();
        $userinfo = $app->user->get( $openid );
        $unionid = $userinfo['unionid'];

        $type = 'weixin_mp';

        $data = sUserLanding::getUserByOpenid($openid, $type);
        if($user_landing && sUser::getUserByUid($user_landing->uid)) {
            session( [ 'uid' => $user_landing->uid ] );
            $redirect = '/services/index.html';
            //$redirect = $this->actGod() ? $this->actGod() : $redirect;
            // return redirect($redirect);
            //return $this->output();
        }

        if( $type == mUserLanding::TYPE_WEIXIN && $unionid ){
            $user_landing = sUserLanding::getUserLandingByUnionId( $unionid );
            if( $user_landing ){
                $user = sUserLanding::loginUser( $user_landing->type, $openid );
                sUserLanding::addNewUserLanding( $user_landing->uid, $openid, $user_landing->nickname, $type, $unionid );
                //save unionid to openid
                sUserLanding::updateUserUnionIdByOpenId( $openid, $unionid );
                return true;
            }
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


		$redirect = '/services/index.html';
		//$redirect = $this->actGod() ? $this->actGod() : $redirect;
		return redirect($redirect);
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
        $url = $_POST['url'];

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
          "rawString" => $string,

          "ticket" => $jsapiTicket
        );

        Log::info('signature', $signPackage);
        return $this->output($signPackage);
    }
//
//	public function actGod()
//	{
//		$result = WxActGod::actGod();
//		$redirect = '';
//		if( $result['code'] == -1 ){
//			$redirect = '/boys/uploadagain/uploadagain?result='.$result['data']['result'].'&request='.$result['data']['request'];
//		}else if( $result['code'] == 1 ){
//			$redirect = '/boys/uploadsuccess/uploadsuccess?total_amount='.$result['data']['total_amount'].'&left_amount='.$result['data']['left_amount'];
//		}else if( $result['code'] == 2 ){
//			$redirect = '/obtainsuccess/obtainsuccess?image='.$result['data']['image'];
//		}
//		return $redirect;
//	}
}

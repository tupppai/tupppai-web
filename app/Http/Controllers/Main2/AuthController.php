<?php namespace App\Http\Controllers\Main2;

use App\Services\User as sUser;
use App\Services\UserLanding as sUserLanding;
use Redirect,Input,Session,Log;

class AuthController extends ControllerBase {

    public $_allow = '*';

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
                return error('TOKEN_NOT_EXIST');
            }
            if (!isset($token_obj['access_token'])) {
                Log::info('access_token', array($token_obj));
                return error('WX_ERROR',$token_obj['errmsg']);
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
    	    return error('AUTH_NOT_EXIST');
        }
        $type = 'weixin_mp';
        $user_landing = sUserLanding::getUserByOpenid($openid, $type);
        if($user_landing && sUser::getUserByUid($user_landing->uid)) {
            //已注册
            session( [ 'uid' => $user_landing->uid ] );
            if( !$user_landing->unionid ){
                sUserLanding::updateUserUnionIdByOpenId($openid, $data['unionid']);
            }
        }else {
            //未注册
            $avatar = $data['headimgurl'];
            $mobile = '';
            $password = '';
            /*v1.0.5 允许不传昵称 默认为手机号码_随机字符串*/
            $nickname = $data['nickname']. hash('crc32b', $data['nickname'] . mt_rand());
            $username = '用户_' . hash('crc32b', $mobile . mt_rand());
            $location = $data['country'];
            $city = '';
            $province = '';
            $sex = $data['sex'] == 1 ? 1 : 0;
            $unionid = $data['unionid'];

            $user = sUser::addUser($type, $username, $password, $nickname, $mobile, $location, $avatar, $sex, $openid);
            if (empty($user)) {
                log::info('注册失败');
            }
            $landing = sUserLanding::bindUser($user->uid, $openid, $nickname, $type, $unionid);

            if (empty($landing)) {
                log::info('landing for user 失败');
            }
            if ($user->id) {
                session(['uid' => $user->uid]);
            }
        }
        return redirect('/services/index.html');
    }

}

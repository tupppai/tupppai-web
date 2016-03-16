<?php namespace App\Http\Controllers\Main;

use App\Models\App;
use App\Models\ActionLog;

use App\Services\User as sUser;
use App\Services\UserLanding as sUserLanding;
use Redirect, Input, Session, Log;

class MediaController extends ControllerBase
{

    public function getMedia()
    {
        $appid = env('MP_APPID');
        $secret = env('MP_APPSECRET');
        $media_id = $this->get('$media_id', 'string');
        if (!$code) {
            return error('KEY_NOT_EXIST');
        }

        $time = time();

        // 1. 获取session中的token
        $openid = session('open_id');
        $token = session('token');
        $token_expire = session('token_expire');

        // 2. 若sessuion超时则需要从微信服务器中获取
        if ($token_expire < $time || !$openid) {
//            $token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $appid . '&secret=' . $secret . '&code=' . $code . '&grant_type=authorization_code';
            $token_url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$secret}";
            $token_obj = http_get($token_url);

            if (!$token_obj) {
                return error('KEY_NOT_EXIST');
            }
            if (!isset($token_obj['access_token'])) {
                Log::info('access_token', [$token_obj]);
            }

            $token = $token_obj['access_token'];
            $expires_in = $token_obj['expires_in'];

            if ($token) {
//                session(['open_id' => $openid, 'token' => $token, 'token_expire' => $time + self::EXPIRE_IN]);
                session(['token' => $token, 'token_expire' => $expires_in]);
            }
        }

        // 3. 根据media_id和access_token查询用户信息
        $user_url = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token={$token}&media_id={$media_id}";
        $data = http_get($user_url);
        if (!$data) {
            return error('NOT_MEDIA');
        }
        $img = GrabImage('xxxx');
        $save_name = CloudCDN::generate_filename_by_file($img);
        CloudCDN::upload($img, $save_name);

    }

}

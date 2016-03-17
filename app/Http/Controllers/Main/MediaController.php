<?php namespace App\Http\Controllers\Main;


use App\Facades\CloudCDN;
use Redirect, Input, Session, Log;

class MediaController extends ControllerBase
{

	public function getMedia()
	{
		$appid    = env('MP_APPID');
		$secret   = env('MP_APPSECRET');
		$qiniu    = env('QINIU_DOMAIN');
		$media_id = $this->get('media_id', 'string');
		if (!$media_id) {
			return error('KEY_NOT_EXIST');
		}

		$time = time();

		// 1. 获取session中的token
		//$openid = session('open_id');
		$token = session('access_token');
		$token_expire = session('access_token_expire');

		// 2. 若sessuion超时则需要从微信服务器中获取
		// if ($token_expire < $time || !$openid) {
		if ($token_expire < $time) {
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
				session(['access_token' => $token, 'access_token_expire' => $expires_in]);
			}
		}

		// 3. 根据media_id和access_token查询用户信息
		$image_url = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token={$token}&media_id={$media_id}";
		$img = GrabImage($image_url);
		if(empty($img)){
			error('DOWNLOAD_NOT_EXIST');
		}
		$save_name = CloudCDN::generate_filename_by_file($img);
		$save_name = CloudCDN::upload($img, $save_name);
		if(!$save_name){
			error('UPLOAD_NOT_EXIST');
		}
		$save_name = $qiniu.'/'.$save_name;
		return $this->output([
			'images_url' => $save_name
		]);

	}

}

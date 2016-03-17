<?php
	namespace App\Services;

	use App\Facades\CloudCDN;
	use App\Services\Upload as sUpload;
	use Redirect, Input, Session, Log;

	class Wx extends ServiceBase
	{
		public static function getUploadId($media_id)
		{
			$access_token 	= self::getAccessToken();
			$img_path 		= self::getMedia($media_id, $access_token);
			$upload_id 		= self::ImageSaveQiniu($img_path);

			return $upload_id;

		}

		public static function getAccessToken()
		{
			$appid 			= env('MP_APPID');
			$secret			= env('MP_APPSECRET');
			$time 			= time();
			// 1. 获取session中的token
			$token 			= session('access_token');
			$token_expire 	= session('access_token_expire');
			// 2. 若sessuion超时则需要从微信服务器中获取
			if ($token_expire < $time) {
				$token_url 	= "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$secret}";
				$token_obj 	= http_get($token_url);
				if (!$token_obj) {
					return false;
				}
				if (!isset($token_obj['access_token'])) {
					Log::info('access_token', [$token_obj]);
				}
				$token = $token_obj['access_token'];
				$expires_in = $token_obj['expires_in'];
				if ($token) {
					session(['access_token' => $token, 'access_token_expire' => $expires_in]);
				}
			}
			return $token;
		}

		/**
		 * 保存图片到七牛并插入uploads表返回upload_id
		 * @param $img_path
		 * @return upload_id
		 */
		public static function ImageSaveQiniu($img_path)
		{
			$save_name = CloudCDN::generate_filename_by_file($img_path);
			$save_name = CloudCDN::upload($img_path, $save_name);
			if (!$save_name) {
				return false;
			}
			$size = getimagesize($img_path);
			$ratio = $size[1] / $size[0];
			$scale = 1;
			$size = $size[1] * $size[0];
			$upload = sUpload::addNewUpload($save_name, $save_name, $save_name, $ratio, $scale, $size);
			if (empty($upload)) {
				return false;
			}

			return $upload->id;
		}

		/**
		 * 根据media保存图片到本地
		 * @param $media_id
		 * @param $token
		 * @return image_path or false
		 */
		public static function getMedia($media_id, $token)
		{
			$image_url = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token={$token}&media_id={$media_id}";
			$img_path = GrabImage($image_url);
			if (empty($img_path)) {
				return false;
			}

			return $img_path;
		}
	}

<?php namespace App\Services;


use App\Facades\CloudCDN;
use App\Models\Upload as mUpload;
use App\Models\User as mUser;

class UploadDown extends ServiceBase
{

	use \App\Traits\UploadImage;

	public function GrabImage($url, $filename = '')
	{
		if ($url == ''):return false;endif;
		if ($filename == '') {
			$ext = strrchr($url, '.');
			$filename = storage_path('upload/') . date('dMYHis') . '.jpg';
		}
		ob_start();
		$result = false;
		$i = 1;
		while (!$result) {
			if($i > 2) break;
			$result = @readfile($url);
			$i++;
		}
		$img = ob_get_contents();
		ob_end_clean();
		$size = strlen($img);
		$fp2 = fopen($filename, 'a+');
		chmod($filename, 0777);
		fwrite($fp2, $img);
		fclose($fp2);

		return $filename;
	}

	public function uploadsDown()
	{
		$page = 1;
		$uploads = new mUpload();
		$uploads_count = $uploads->where('id', '>', 16627)->where('filename', 'file.jpg')->count();
		$limit = ceil($uploads_count / 100);
		for ($page; $page <= 100; $page++) {
			$uploads = (new mUpload())->where('id', '>', 16627)->forPage($page, $limit)->get();
			foreach ($uploads as $upload) {
				if (strpos($upload->savename, 'http') === false) {
					continue;
				}
				$img = $this->GrabImage($upload->savename);
				$save_name = CloudCDN::generate_filename_by_file($img);
				$qiniu_image_url = CloudCDN::upload($img, $save_name);
				//var_dump($save_name);
				//var_dump($qiniu_image_url);
				//dd(CloudCDN::file_url( $qiniu_image_url ));
				$upload->savename = $save_name;
				$upload->pathname = $save_name;
				$upload->save();
				echo 'upload_id: ' . $upload->id;
				echo '<br>';
			}
		}
	}

	public function uploadUserAvatar()
	{
		$page = 1;
		$user = new mUser();
		$users_count = $user->where('uid', '>', 3017)->count();
		$limit = ceil($users_count / 200);
		for ($page; $page <= 200; $page++) {
			$users = (new mUser())->where('uid', '>', 3017)->forPage($page, $limit)->get();
			foreach ($users as $user) {
				if (strpos($user->avatar, 'http') === false) {
					continue;
				}
				$img = $this->GrabImage($user->avatar);
				$save_name = CloudCDN::generate_filename_by_file($img);
				$qiniu_image_url = CloudCDN::upload($img, $save_name);
				//var_dump($save_name);
				//var_dump($qiniu_image_url);
				//dd(CloudCDN::file_url( $qiniu_image_url ));
				$user->avatar = $save_name;
				$user->save();
				echo 'user_id: ' . $user->uid;
				echo '<br>';
			}
		}
	}


}

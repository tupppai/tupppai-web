<?php namespace App\Services;


use App\Facades\CloudCDN;
use App\Models\Upload as mUpload;
use App\Models\User as mUser;

class UploadDown extends ServiceBase
{

	use \App\Traits\UploadImage;

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
				$img = GrabImage($upload->savename);
				$save_name = CloudCDN::generate_filename_by_file($img);
				CloudCDN::upload($img, $save_name);
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
				$img = GrabImage($user->avatar);
				$save_name = CloudCDN::generate_filename_by_file($img);
				CloudCDN::upload($img, $save_name);
				$user->avatar = $save_name;
				$user->save();
				echo 'user_id: ' . $user->uid;
				echo '<br>';
			}
		}
	}


}

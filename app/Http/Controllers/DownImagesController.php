<?php namespace App\Http\Controllers;

use App\Facades\CloudCDN;
use App\Models\Upload as mUpload;

class DownImagesController extends Controller
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
		readfile($url);
		$img = ob_get_contents();
		ob_end_clean();
		$size = strlen($img);
		$fp2 = fopen($filename, 'a+');
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
			$uploads = $uploads->where('id', '>', 16627)->forPage($page, $limit)->get();
			foreach ($uploads as $upload) {
				$img = $this->GrabImage($upload->savename);
				$save_name  = CloudCDN::generate_filename_by_file($img);
				$qiniu_image_url = CloudCDN::upload($img, $save_name);
				//var_dump($save_name);
				//var_dump($qiniu_image_url);
				//dd(CloudCDN::file_url( $qiniu_image_url ));
				$upload->savename = $save_name;
				$upload->pathname = $save_name;
				$upload->save();
				dd($upload->id);
			}
		}
	}
}

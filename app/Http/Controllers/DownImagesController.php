<?php namespace App\Http\Controllers;

use App\Facades\CloudCDN;
use App\Models\Upload as mUpload;
use App\Services\UploadDown;
use Illuminate\Support\Facades\Queue;

class DownImagesController extends Controller
{

	public function uploadsDown()
	{
		Queue::later(5,new \App\Jobs\UploadDown());
	}
	public function uploadsDownAvatar()
	{
//		$upload_down = new \App\Services\UploadDown();
//		$upload_down->uploadUserAvatar();
//		exit;
		Queue::later(5,new \App\Jobs\UploadDownAvatar());
	}
}

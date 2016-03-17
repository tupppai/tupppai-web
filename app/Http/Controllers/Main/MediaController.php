<?php namespace App\Http\Controllers\Main;


use App\Facades\CloudCDN;
use App\Services\Upload as sUpload;
use App\Services\Wx;
use Redirect, Input, Session, Log;

class MediaController extends ControllerBase
{

	public function getMediaToUploadId()
	{
		$media_id = $this->get('media_id', 'string');
		if (!$media_id) {
			return error('KEY_NOT_EXIST');
		}
		$upload_id = Wx::getUploadId($media_id);
		return $this->output([
			'upload_id' => $upload_id
		]);

	}

}

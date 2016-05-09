<?php
namespace App\Http\Controllers\Main2;

use App\Services\Wx as sWX;
class ImageController extends ControllerBase
{
	public function upload()
	{

        $media_id = $this->post('media_id', 'string', 0);
        $upload_ids = sWX::getUploadId( $media_id );
        if( is_null($upload_ids)){
            return error('WRONG_ARGUMENTS', 'token获取失败');
        }
        if( $upload_ids === 0 ){
            return error('WRONG_ARGUMENTS', '获取图片失败');
        }
        if( $upload_ids === -1 ){
            return error('WRONG_ARGUMENTS', '保存图片失败');
        }
        $upload = sUpload::getUploadById($upload_ids);
        if(!$upload) {
            return error('EMPTY_UPLOAD_ID');
        }
        return $this->output([
            'result' => 'ok',
            'savename' => $upload->savename,
            'pathname' => $upload->pathname
        ]);
	}
}
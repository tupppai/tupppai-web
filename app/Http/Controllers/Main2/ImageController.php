<?php
namespace App\Http\Controllers\Main2;

use App\Services\Wx as sWX;
use App\Services\Upload as sUpload;
use Log;
class ImageController extends ControllerBase
{
	public function upload()
	{

        $media_id = $this->post('media_id', 'string', 0);
        $upload_ids = sWX::wxUploadId( $media_id );
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
            'upload_id' => $upload->id,
            'savename' => $upload->savename,
            'pathname' => $upload->pathname
        ]);
	}
    public function test()
    {
        $upload_ids = sWX::wxUploadId( 'lDp6DUsB6cH1Dl7C3TGwrvlzKg9ZsWvJceqABi_emOZaAISv5tCf-LF5seJXyyhy' );
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
            'upload_id' => $upload->id,
            'savename' => $upload->savename,
            'pathname' => $upload->pathname
        ]);
    }
}
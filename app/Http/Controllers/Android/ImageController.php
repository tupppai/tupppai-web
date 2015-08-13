<?php namespace App\Http\Controllers\Android;

use App\Models\Upload as mUpload,
    App\Models\User as mUser,
    App\Models\UserLanding as mUserLanding,
    App\Models\Download as mDownload;

use App\Facades\Sms, App\Facades\CloudCDN;
use App\Jobs\Push, Queue;

class ImageController extends ControllerBase
{
    public $_allow = array(
        'upload'
    );

    /**
     * [downloadAction 记录下载]
     * @param type 求助or回复
     * @param target 目标id
     * @return [json]
     */
    public function downloadAction() {
        $type       = $this->get('type');
        $target_id  = $this->get('target');
        $width      = $this->get('width', 'int', 480);
        $uid        = $this->_uid;

        if( !in_array($type, array('ask', 'reply') )){
            return error('WRONG_ARGUMENTS');
        }
        
        $url = '';
        if($type=='ask') {
            $model  = sAsk::getAskById($target_id);
            $type   = mDownload::TYPE_ASK;
        }
        else if($type=='reply') {
            $model  = sAsk::getAskById($target_id);
            $type   = mDownload::TYPE_ASK; 
        }

        if( !$model ) {
            return error('UPLOAD_NOT_EXIST');
        }

        $upload     = sUpload::getUploadById($model->upload_id);
        if( !$upload ){
            return error('UPLOAD_NOT_EXIST');
        }
        $url        = CloudCDN::file_url($upload->savename);

        if( !sDownload::hasDownloaded($uid, $type, $target_id) ){
            $dl = sDownload::addNewDownload($uid, $type, $target_id, $url, 0);
        }

        return $this->output( array(
            'type'=>$type,
            'target_id'=>$target_id,
            'url'=>$url
        ));
    }

    public function testAction() {
        echo '
            <form action="upload" method="post"
            enctype="multipart/form-data">
            <label for="file">Filename:</label>
            <input type="file" name="file2" id="file" /> 
            <br />
            <input type="submit" name="submit" value="Submit" />
            </form>
            ';
    }

    public function uploadAction() {
        $data = $this->_upload_cloudCDN();

        return $this->output($data);
    }

    use \App\Traits\UploadImage; 
}

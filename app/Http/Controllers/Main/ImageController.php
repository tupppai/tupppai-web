<?php namespace App\Http\Controllers\Main;

use App\Models\Upload as mUpload,
    App\Models\User as mUser,
    App\Models\Label as mLabel,
    App\Models\UserLanding as mUserLanding,
    App\Models\Download as mDownload;

use App\Services\Ask as sAsk;
use App\Services\Upload as sUpload;
use App\Services\Download as sDownload;

use App\Facades\CloudCDN;

class ImageController extends ControllerBase
{
    public function record() {
        $this->isLogin();

        $type       = $this->get('type');
        $target_id  = $this->get('target');
        $width      = $this->get('width', 'int', 480);
        $uid        = $this->_uid;

        $url = array();
        if($type == mLabel::TYPE_ASK) {
            $model  = sAsk::getAskById($target_id);
            $type   = mDownload::TYPE_ASK;
            $uploads= sUpload::getUploadByIds(explode(',', $model->upload_ids));
            #todo: 打包下载
            foreach($uploads as $upload) {
                $url[]   = CloudCDN::file_url($uploads[0]->savename);
            }
        }
        else if($type == mLabel::TYPE_REPLY) {
            $model  = sAsk::getAskById($target_id);
            $type   = mDownload::TYPE_ASK; 
            $upload = sUpload::getUploadById($model->upload_id);
            $url[]  = CloudCDN::file_url($upload->savename);
        }

        if( !sDownload::hasDownloaded($uid, $type, $target_id) ){
            sDownload::saveDownloadRecord($uid, $type, $target_id, $url[0]);
        }

        return $this->output( array(
            'type'=>$type,
            'target_id'=>$target_id,
            'url'=>$url
        ));
    }
    
    public function download(){
        $url    = $this->get("url");

        // todo: 后续将名字替换成label里面的内容
        $filename = 'psgod-'.date('Ymd').'.jpg';
        // todo: 去除水印
        $contents = file_get_contents($url);
        // 输入文件标签
        Header("Content-type: application/octet-stream");
        Header("Accept-Ranges: bytes");
        Header("Accept-Length: ".strlen($contents));
        Header("Content-Disposition: attachment; filename=" . $filename);
        // 输出文件内容
        echo $contents;
    }

    public function upload() {
        $data = $this->_upload_cloudCDN();

        return $this->output($data);
    }

    use \App\Traits\UploadImage; 
}

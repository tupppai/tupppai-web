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
use Request, Input, Validator;

class ImageController extends ControllerBase
{
    public function record() {
        $this->isLogin();

        $type       = $this->get('type');
        $target_id  = $this->get('target');
        $category_id= $this->get('category_id', 'int', 0);
        $width      = $this->get('width', 'int', 480);
        $uid        = $this->_uid;

        if(!$type) {
            return error('ASK_NOT_EXIST');
        }

        $url = sDownload::getFile( $type, $target_id );

        if( !sDownload::hasDownloaded($uid, $type, $target_id) ){
            sDownload::saveDownloadRecord($uid, $type, $target_id, $url[0], $category_id);
        }

        return $this->output_json( array(
            'type'=>$type,
            'target_id'=>$target_id,
            'url'=>$url
        ));
    }

    public function download(){
        $url    = $this->get("url");
        if(!$url) {
            return error('ERROR_URL_FORMAT');
        }

        // todo: 后续将名字替换成label里面的内容
        $filename = '图派-'.date('Ymd').'.jpg';
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

    public function upload()
    {
        if (empty($files = Request::file())) {
            return error('FILE_NOT_VALID');
        }
        $rules = array();
        //mimes:jpeg,bmp,png and for max size max:10000
        // doing the validation
        $validator = Validator::make($files, $rules);

        if ($validator->fails()) {
            return error('FILE_NOT_VALID');
        }

        $width  = $this->get("width");
        $ratio  = $this->post("ratio", "float", 0);
        $scale  = $this->post("scale", "float", 0);

        foreach($files as $file) {
            //$size = $file->getSize();
            $filename = $file->getPathName();
            if( !$filename ){
                continue;
            }
            $size = getimagesize( $filename );
            $ratio= $size[1]/$size[0];
            $scale= 1;
            $size = $size[1]*$size[0];

            $save_name  = CloudCDN::generate_filename_by_file($file->getClientOriginalName());

            $ret = CloudCDN::upload($file->getPathName(), $save_name);
            if(!$ret) {
                #todo: log error
            }
            $this->_save_file($file, $save_name);

            $upload = sUpload::addNewUpload(
                $file->getClientOriginalName(),
                $save_name,
                $ret,
                $ratio,
                $scale,
                $size
            );

            $ret = array(
                'url'=>CloudCDN::file_url( $ret ),
                'id'=>$upload->id,
                'name'=>$file->getClientOriginalName(),
                'ratio'=>$ratio,
                'scale'=>$scale
            );
            return $this->output( $ret );
        }
    }

    use \App\Traits\UploadImage;
}

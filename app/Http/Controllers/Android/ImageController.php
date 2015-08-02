<?php namespace App\Http\Controllers\Android;

use App\Models\Upload;

class ImageController extends ControllerBase
{
    
    public $_allow = array(
        'upload'
    );
     
    public function downloadAction() {
        $upload_id  = $this->get("upload_id", "int", 1);
        $width      = $this->get("width", "int", 320);

        if (!$upload_id) {
            return error('UPLOAD_NOT_EXIST');
        }

        $upload = Upload::findFirst($upload_id);
        if (!$upload) {
            return error('UPLOAD_NOT_EXIST');
        }
        //todo: 记录用户下载图片的数据
        $data = array();
        $data['url']    = \CloudCDN::file_url($upload->savename);          
        $data['ratiom']  = $upload->ratio;
        $data['width']  = intval($width);
        $data['height'] = intval($width*$data['ratio']);

        return $this->output($data);
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
        $this->_upload_cloudCDN();
    }

    use \App\Traits\UploadImage; 
}

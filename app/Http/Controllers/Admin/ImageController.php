<?php namespace App\Http\Controllers\Admin;

use App\Models\Upload;

class ImageController extends ControllerBase
{
    use \App\Traits\UploadImage;   // 混入文件上传 trait

    public function indexAction() {

        return $this->output();
    }
}

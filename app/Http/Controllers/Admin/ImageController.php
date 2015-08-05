<?php namespace App\Http\Controllers\Admin;

use App\Models\Upload;

class ImageController extends ControllerBase
{
    use \App\Traits\ImageUpload;   // 混入文件上传 trait    

    public function indexAction() {
        
    }
}

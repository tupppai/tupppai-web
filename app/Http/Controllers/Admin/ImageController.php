<?php namespace App\Http\Controllers\Admin;

use App\Models\Upload;
use Request, Input, Validator;
use App\Facades\CloudCDN;

use App\Services\Upload as sUpload;

class ImageController extends ControllerBase
{
    use \App\Traits\UploadImage;   // 混入文件上传 trait

    public function indexAction() {

        return $this->output();
    }

    public function updateAction() {
        
        $uploads = Upload::where('pathname', '0')->get();
        foreach($uploads as $upload) {
            $savename = $upload->savename;
            $date = substr($savename, 0, 6);
            $ret = CloudCDN::upload("/data/images/$date/$savename", $savename);


            $upload->pathname = $savename;
            $upload->save();
        }
    }
    
    public function addAction()
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
            
            $size = getimagesize($file->getPathName());
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
            return $this->output_json( $ret );
        }
    }
}

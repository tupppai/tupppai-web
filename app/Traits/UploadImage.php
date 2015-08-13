<?php namespace App\Traits;

use Request, Input, Validator;
use App\Facades\CloudCDN;

use App\Services\Upload as sUpload;

/**
 * 文件上传
 */
trait UploadImage
{
    /**
     * 上传文件到七牛
     * 
     * @return array
     */
    protected function _upload_cloudCDN()
    {
        if (empty($files = Request::file())) {
            return error('FILE_NOT_EXIST');
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
            $size = $file->getSize();

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
    
            return array( 
                'url'=>$ret, 
                'id'=>$upload->id, 
                'name'=>$file->getClientOriginalName(),
                'ratio'=>$ratio,
                'scale'=>$scale
            );
        }
    }

    private function _save_file($file, $save_name){
        $upload_dir = env('IMAGE_UPLOAD_DIR');
        $upload_dir .= date('Ym') . __DS__;

        //需要创建目录
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        #move_uploaded_file($file->getPathName(), $upload_dir.$save_name);
        $file->move($upload_dir, $save_name);
    }
    
    /**
     * 检测文件上传错误码
     */
    protected function _upload_error(){
        if(empty($_FILES)){
            return "请选择上传文件";
        }
        switch($_FILES['Filedata']['error']) {   
            case 1:    
                return "文件大小超出了服务器的空间大小";
            case 2:    
                return "要上传的文件大小超出浏览器限制";
            case 3:    
                return "文件仅部分被上传";
            case 4:    
                return "没有找到要上传的文件";
            case 5:    
                return "服务器临时文件夹丢失";
            case 6:    
                return "文件写入到临时文件夹出错";
            default:
                return "";
        }
    }


}

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
     * 上传图片到七牛并且返回 json string
     *
     * @return json string
     */
    public function uploadAction()
    {
        $ret = $this->_upload_cloudCDN();
        return $this->output_json( $ret );
    }

//todo::laravelize
    /**
     * 上传图片到服务器本地并且预览
     *
     * @return json string
     */
    public function previewAction()
    {
        $config     = read_config("image");
        $upload_dir = $config->upload_dir . date("Ym")."/";
        $preview_dir= $config->preview_dir . date("Ym")."/";
        $allow_ext  = (array)$config->allow_ext;

        if ($this->request->hasFiles() == true) {
            foreach ($this->request->getUploadedFiles() as $file) {
                if(!DEV){
                    $ext = $file->getExtension();
                    if(!in_array($ext, $allow_ext)){
                        return ajax_return(0, '上传失败，文件类型错误');
                    }
                }

                //get file name
                $save_name = $this->cloudCDN->generate_filename_by_file($file->getName());
                $size   = $this->_save_file($file, $upload_dir, $save_name);

                $upload = \Psgod\Models\Upload::newUpload($file->getName(), $save_name, $preview_dir, $size);
                if ($upload) {
                    ajax_return(1, 'okay', array(
                        'url'=>$preview_dir . $save_name,
                        'id'=>$upload->id,
                        'name'=>$file->getName(),
                        'ratio'=>$size['ratio']
                    ));
                } else {
                    ajax_return(0, '上传成功但保存失败', array('url'=>$preview_dir . $save_name));
                }
            }
        } else {
            ajax_return(0, $this->_upload_error());
        }
    }
//todo::laravelize
    /**
     * 切割图片
     *
     * @return json string
     */
    public function cropAction()
    {
        $this->noview();

        /**
         * 切割图片
         */
        $bounds = $this->post("bounds", "float");
        $scale  = $this->post("scale", "float");
        $upload_id  = $this->post("upload_id", "int");

        $jpeg_quality = 90;

        $config = read_config("image");
        $public_dir     = $config->public_dir;
        $preview_dir    = $config->preview_dir;

        $upload = \Psgod\Models\Upload::findFirst("id=" . $upload_id);
        $src = $public_dir.$upload->pathname.$upload->savename;

        $size = getimagesize($src);
        $type = $size['mime'];
        // 比例参数
        $k = $size[0]/$bounds[0];
        $dst_w  = $scale['w']*$k;
        $dst_h  = $scale['h']*$k;

        if($dst_w != 0 && $dst_h != 0){
            $src_x  = $scale['x']*$k;
            $src_y  = $scale['y']*$k;
            $src_w  = $size[0];
            $src_h  = $size[1];

            switch($type){
            case "image/png":
                $img_r = imagecreatefrompng($src);
                break;
            case "image/jpg":
            case "image/jpeg":
                $img_r = imagecreatefromjpeg($src);
                break;
            case "image/gif":
                $img_r = imagecreatefromgif($src);
                break;
            }
            $dst_r = ImageCreateTrueColor($dst_w, $dst_h );

            imagecopyresampled($dst_r, $img_r, 0, 0,
                $src_x, $src_y, $dst_w, $dst_h, $dst_w, $dst_h);

            imagejpeg($dst_r, $src, $jpeg_quality);
        }
        //$save_name = $this->cloudCDN->generate_filename_by_file($upload->filename);
        $save_name = $upload->savename;
        $ret = $this->cloudCDN->upload($src, $save_name);
        if ($ret) {
            if($dst_w == 0){
                //$upload->ratio = $dst_h/$dst_w;
                $upload->ratio = $size[1]/$size[0];
            }
            else if($dst_w != 0){
                $upload->ratio = $dst_h/$dst_w;
            }

            $upload->update_time = time();
            $upload->type        = 'qiniu';
            $upload->save();
            ajax_return(1, 'okay', array(
                'url'=>get_cloudcdn_url($ret),
                'id'=>$upload->id,
                'name'=>$upload->filename,
                'ratio'=>$upload->ratio
            ));
        } else {
            ajax_return(0, '文件上传到CDN出错');
        }
    }



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
            $localPath = $this->_save_file($file, $save_name);

            if( !$ratio ){
                $imageinfo = getimagesize($localPath);
                $ratio = $imageinfo[1] / $imageinfo[0]; //  width/height
            }

            $upload = sUpload::addNewUpload(
                $file->getClientOriginalName(),
                $save_name,
                $ret,
                $ratio,
                $scale,
                $size
            );

            return array(
                'url'=>CloudCDN::file_url( $ret ),
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
        return $upload_dir.$save_name;
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


    public static function _dl_file( $url, $file_path ){
        file_put_contents($file_path, fopen($url, 'r'));
    }
    public static function grayscale_and_upload_image( $url ){
        list($width, $height, $image_type) = getimagesize($url);
        $ext = '';
        switch($image_type){
            case 1: $ext = 'gif'; break;
            case 2: $ext = 'jpg'; break;
            case 3: $ext = 'png'; break;
            default: break;
        }

        $file = env('IMAGE_UPLOAD_DIR').'/temp/tempImage.'.$ext;
        //download
        self::_dl_file( $url, $file );

        switch ($image_type){
            case 1: $im = imagecreatefromgif($file); break;
            case 2: $im = imagecreatefromjpeg($file);  break;
            case 3: $im = imagecreatefrompng($file); break;
            default: return '';  break;
        }

        if($im && imagefilter($im, IMG_FILTER_GRAYSCALE)){
            switch ($image_type){
                case 1: imagegif($im,$file); break;
                case 2: imagejpeg($im, $file, 100);  break; // best quality
                case 3: imagepng($im, $file, 0); break; // no compression
                default: break;
            }
        }
        else{
            return false;
        }

        //upload
        $save_name  = CloudCDN::generate_filename_by_file($url);
        $ret = CloudCDN::upload($file, $save_name);

        imagedestroy($im);
        //delete temp file
        @unlink( $file );

        //return image url
        return CloudCDN::file_url( $ret );
    }
}

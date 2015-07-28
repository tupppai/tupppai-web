<?php
namespace Psgod\Services;

use \Psgod\Models\Upload as mUpload;

class Upload extends ServiceBase
{

    public static function newUpload($filename, $savename, $url, $size = array(), $type = 'qiniu')
    {
        $upload = new mUpload();
        $upload->filename = $filename;
        $upload->savename = $savename;
        $upload->pathname = $url;
        $array = explode('.', $filename);
        $upload->ext      = end($array);
        //todo:
        $upload->uid      = 0;
        $upload->ip       = get_client_ip();
        $upload->type     = $type;
        $upload->ratio    = isset($size['ratio'])?$size['ratio']: 0.75;
        $upload->scale    = isset($size['scale'])?$size['scale']: 1;
        $upload->size     = isset($size['size'])?$size['size']: 0;

        $upload->create_time = time();
        $upload->update_time = time();

        return $upload->save_and_return($upload);
    }

    public static function getUploadById($upload_id){
        return mUpload::findFirst($upload_id);
    }

    public static function resize($ratio, $scale, $savename, $width) {
        if(!isset($scale) || $scale == 0){
            $scale = 1;
        }
        if(!isset($ratio) || $ratio == 0){
            $ratio = 1.333;
        }
        if(!isset($savename) || $savename == ''){
            $savename = '';
        }
        $width = $width*$scale;

        $temp = array();
        $temp['image_width']    = $width;
        $temp['image_height']   = intval($width*$ratio);
        $temp['image_url']      = \CloudCDN::file_url($savename, $width);

        return $temp;
    }
}

<?php
namespace App\Services;

use App\Models\Upload as mUpload;
use App\Services\ActionLog as sActionLog;

use App\Facades\CloudCDN;

class Upload extends ServiceBase
{

    /**
     * ratio = height / width
     */
    public static function addNewUpload($filename, $savename, $url, $ratio, $scale, $size, $type = 'qiniu')
    {
        $uid    = _uid();
        $arr    = explode('.', $filename);
        $ext    = end($arr);

        $upload = new mUpload;
        sActionLog::init('ADD_NEW_UPLOAD', $upload);

        $upload->assign(array(
            'filename'=>$filename,
            'savename'=>$savename,
            'pathname'=>$url,
            'ext'=>$ext,
            'uid'=>$uid,
            'type'=>$type,
            'size'=>$size,
            'ratio'=>$ratio,
            'scale'=>$scale
        ));

        $upload->save();
        sActionLog::save($upload);
        return $upload;
    }

    public static function getUploadById($upload_id){
        $upload = (new mUpload)->get_upload_by_id($upload_id);

        return $upload;
    }

    public static function getUploadByIds($upload_ids){
        $upload = (new mUpload)->get_upload_by_ids($upload_ids);

        return $upload;
    }

    public static function getImageUrlById($upload_id) {
        $upload = (new mUpload)->get_upload_by_id($upload_id);

        return CloudCDN::file_url($upload->savename);
    }

    public static function updateImage($upload_id, $scale, $ratio) {
        $upload = (new mUpload)->get_upload_by_id($upload_id);
        if( !$upload ){
            return error('UPLOAD_NOT_EXIST');
        }
        sActionLog::init('UPDATE_IMAGE', $upload );
        $upload->update_image($scale, $ratio);

        sActionLog::save( $upload );
        return $upload;
    }

    public static function updateImages($upload_ids, $scales, $ratios) {
        $uploads = array();

        foreach($upload_ids as $key=>$upload_id) {
            $upload = (new mUpload)->get_upload_by_id($upload_id);
            $scale  = $scales[$key];
            $ratio  = $ratios[$key];

            if( !$upload ){
                return error('UPLOAD_NOT_EXIST');
            }
            sActionLog::init('UPDATE_IMAGE', $upload );
            $upload->update_image($scale, $ratio);

            sActionLog::save( $upload );
            $uploads[] = $upload;
        }
        return $uploads;
    }

    public static function resizeImage($name, $width = 320, $scale = 1, $ratio = 1.33) {
        $width = intval($width*$scale);
        $height= intval($width*$ratio);

        $max_height = $width;

        $result = array();
        $result['image_url']      = CloudCDN::file_url($name, $width);
        if($height > $max_height) {
            $height = $max_height;
            $width  = intval($height / $ratio);
        }

        $result['image_width']    = $width;
        $result['image_height']   = $height;
        $result['image_ratio']    = $ratio;
        return $result;
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

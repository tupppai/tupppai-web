<?php
namespace App\Services;

use \App\Models\Upload as mUpload;
use App\Services\ActionLog as sActionLog;

class Upload extends ServiceBase
{

    public static function addNewUpload($filename, $savename, $url, $ratio, $scale, $size, $type = 'qiniu')
    {
        $uid    = _uid();
        $arr    = explode('.', $filename);
        $ext    = end($arr);
        sActionLog::init('ADD_NEW_UPLOAD' );

        $upload = new mUpload();
        $upload->assign(array(
            'filename'=>$filename,
            'savename'=>$savename,
            'pathname'=>$url,
            'ext'=>$ext,
            'uid'=>$uid,
            'type'=>$type,
            'ratio'=>$ratio,
            'scale'=>$scale,
            'size'=>$size,
            'ratio'=>$ratio,
            'scale'=>$scale
        ));

        $u =  $upload->save();
        sActionLog::save();
        return $u;
    }

    public static function getUploadById($upload_id){
        $upload = (new mUpload)->get_upload_by_id($upload_id);

        return $upload;
    }

    public static function getUploadByIds($upload_ids){
        $upload = (new mUpload)->get_upload_by_ids($upload_ids);

        return $upload;
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

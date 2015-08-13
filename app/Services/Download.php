<?php

namespace App\Services;
use Phalcon\Mvc\Model\Resultset\Simple as Resultset;

use \App\Models\Download as mDownload,
    \App\Models\Reply as mReply;

class Download extends ServiceBase
{

    /**
     * 添加新的下载
     */
    public static function addNewDownload($uid, $type, $target_id, $url, $status){

        $download            = new mDownload();
        $download->uid       = $uid;
        $download->type      = $type;
        $download->target_id = $target_id;
        $download->create_time   = time();
        $download->update_time   = time();
        $download->asker_ip  = get_client_ip();
        $download->url       = $url;
        $download->status    = $status;

        return $download->save();
    }

    /**
     * 是否被该用户下载
     */
    public static function hasDownloaded($uid, $type, $target_id) {
        $mDownload = (new mDownload)->has_downloaded($uid, $type, $target_id);
        return $mDownload?true: false;
    }
    public static function hasDownloadedAsk($uid, $ask_id) {
        return self::hasDownloaded($uid, mDownload::TYPE_ASK, $ask_id);
    }
    public static function hasDownloadedReply($uid, $reply_id) {
        return self::hasDownloaded($uid, mDownload::TYPE_REPLY, $reply_id);
    }

    /**
     * 获取用户进行中数量
     */
    public static function getUserDownloadCount ( $uid ) {
        $download_count = (new mDownload)->count_user_download($uid);
        $reply_count    = (new mReply)->count_user_reply($uid);

        return $download_count - $reply_count;
    }

    /**
     * 获取正在进行中的列表
     */
    public static function getProcressing($uid, $page, $limit) {
        $mDownload = new mDownload;
        $downloads = $mDownload->page(array('uid'=>$uid), $page, $limit);
        return $downloads;
    }

    /**
     * 下载过后修改下载状态
     */
    public static function uploadStatus($uid, $type, $target_id, $image_url){
        if(!$type || !$target_id)
            return false;

        $mDownload = new mDownload;
        if(!in_array($type, array($mDownload::TYPE_ASK, $mDownload::TYPE_REPLY)))
            return error('DOWNLOAD_NOT_EXIST');

        $download = $mDownload::findFirst("uid = $uid AND type= ".$type.
            " AND target_id = $target_id ".
            " AND status = ".$mDownload::STATUS_NORMAL
        );
        $image_url = get_cloudcdn_url($image_url);

        if($download) {
            $download->status = mDownload::STATUS_REPLIED;
            $download->save_and_return($download);
        }
        else {
            $mDownload::addNewDownload($uid, $type, $target_id, $image_url, $mDownload::STATUS_NORMAL);
        }
        return true;
    }
}

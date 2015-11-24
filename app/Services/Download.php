<?php

namespace App\Services;

use App\Models\Download as mDownload,
    App\Models\Reply as mReply,
    App\Models\Ask as mAsk;

use App\Services\Ask as sAsk,
    App\Services\Reply as sReply,
    App\Services\ActionLog as sActionLog;

use App\Facades\CloudCDN;

class Download extends ServiceBase
{
    public static function getDownloaded( $uid, $page, $size, $last_updated ){
        $mDownload = new mDownload();
        $mAsk = new mAsk();
        $mReply = new mReply();

        //todo 暂时只能下载求助
        //$downloaded = $mDownload->get_downloaded( $uid, $page, $size, $last_updated );
        $downloaded = $mDownload->get_ask_downloaded( $uid, $page, $size, $last_updated );
        $downloadedList = array();
        foreach( $downloaded as $dl ){
            $downloadedList[] = self::detail( $dl );
        }
        return $downloadedList;
    }

    public static function getDone( $uid, $page, $size, $last_updated ){
        $mDownload = new mDownload();

        $done = $mDownload->get_done( $uid, $page, $size, $last_updated );
        $doneList = array();
        foreach( $done as $dl ){
            $doneList[] = self::detail( $dl );
        }
        return $doneList;
    }

    /**
     * 通过id获取download——id
     */
    public static function getDownloadById($id) {
        return (new mDownload)->get_download_record_by_id($id);
    }

    public static function detail( $dl ){
        $mAsk = new mAsk();
        $mReply = new mReply();

        $result = $dl->toArray();

        switch( $dl->type ){
        case mAsk::TYPE_ASK:
            $ask    = $mAsk->get_ask_by_id( $dl->target_id );
            $result = array_merge(sAsk::detail($ask), $result);
            $result['uid'] = $ask->uid;
            break;
        case mAsk::TYPE_REPLY:
            $reply  = $mReply->get_reply_by_id( $dl->target_id );
            $result = array_merge(sReply::detail($reply), $result);
            $result['uid'] = $reply->uid;
            break;
        }
        $result['id'] = $dl->target_id;
        $result['type'] = $dl->type;
        return $result;
    }

    public static function deleteDLRecord( $uid, $target_id ){
        $mDownload = new mDownload();
        $download = $mDownload-> get_download_record( $uid, $target_id );
        if(!$download){
            return error( 'DOWNLOAD_RECORD_DOESNT_EXIST', '请选择删除的记录' );
        }
        if( $download->uid != $uid ){
            return error( 'NOT_YOUR_RECORD', '这个不是你的下载记录');
        }

        sActionLog::init( 'DELETE_DOWNLOAD', $download );
        $download->status = mDownload::STATUS_DELETED;
        $download->save();
        sActionLog::save( $download );
        return (bool)$download;
    }

    public static function getFile( $type, $target_id ){
        switch( $type ){
        case mDownload::TYPE_ASK:
            if($ask = sAsk::getAskById($target_id)) {
                $ask = sAsk::detail( $ask, 0 );
                $url = $ask['image_url'];
            }
            break;
        case mDownload::TYPE_REPLY:
            if($reply = sReply::getReplyById($target_id)) {
                $reply = sReply::detail( $reply, 0 );
                $url   = $reply['image_url'];
            }
            break;
        default:
            return error( 'WRONG_ARGUMENTS', '未定义类型' );
        }

        if($url==''){
            return error( 'DOWNLOAD_FILE_DOESNT_EXISTS', '访问出错' );
        }

        return $url;
    }


    public static function saveDownloadRecord( $uid, $type, $target_id, $url ){
        $mDownload = new mDownload();

        sActionLog::init( 'DOWNLOAD_FILE' );
        $mDownload->assign(array(
            'uid'   => $uid,
            'type'  => $type,
            'target_id' => $target_id,
            'url' => $url,
            'ip'  => get_client_ip(),
            'status' => mDownload::STATUS_NORMAL
        ));
        $mDownload->save();
        sActionLog::save( $mDownload );

        return $mDownload;
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
     * 获取帖子下载数量
     */
    public static function countDownload($type, $target_id) {
        return (new mDownload)->count_download($type, $target_id);
    }

    /**
     * 获取用户的下载数
     */
    public static function countProcessing( $uid ){
        $mDownload = new mDownload;
        $processing_amount = $mDownload->count_user_download( $uid, mDownload::TYPE_ASK, mDownload::STATUS_NORMAL );
        return $processing_amount;
    }
    public static function countDone( $uid ){
        $mDownload = new mDownload;
        $processing_amount = $mDownload->count_user_download( $uid, mDownload::TYPE_ASK, mDownload::STATUS_HIDDEN );
        return $processing_amount;
    }
    public static function countDownloaded( $uid ){
        $mDownload = new mDownload;
        $processing_amount = $mDownload->count_user_download( $uid, mDownload::TYPE_ASK );
        return $processing_amount;
    }

    /**
     * 获取用户进行中数量
     */
    public static function getUserDownloadCount ( $uid ) {
        return self::countDownloaded( $uid );
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
     * 上传作品之后修改状态
     */
    public static function uploadStatus($uid, $ask_id, $image_url){
        $mDownload = new mDownload;

        $download  = $mDownload->get_download_record($uid, $ask_id);
        $image_url = CloudCDN::file_url($image_url);

        if( $download ) {
            $download->status = mDownload::STATUS_HIDDEN;
            $download->save();
        }
        else {
            $download = self::saveDownloadRecord( $uid, mDownload::TYPE_ASK, $ask_id, $image_url );
        }
        return $download;
    }
}

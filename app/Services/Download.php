<?php

namespace App\Services;

use \App\Models\Download as mDownload,
    \App\Models\Reply as mReply,
    \App\Models\Ask as mAsk;

use \App\Services\Ask as sAsk,
    \App\Services\Reply as sReply,
    \App\Services\ActionLog as sActionLog;

class Download extends ServiceBase
{
    public static function getDownloaded( $uid, $last_updated, $page, $size ){
        $mDownload = new mDownload();
        $mAsk = new mAsk();
        $mReply = new mReply();

        $downloaded = $mDownload->get_downloaded( $uid, $last_updated, $page, $size );
        $downloadedList = array();
        foreach( $downloaded as $dl ){
            switch( $dl->type ){
                case mAsk::TYPE_ASK:
                    $downloadedList[] = sAsk::detail( $mAsk->get_ask_by_id( $dl->target_id  ));
                    break;
                case mAsk::TYPE_REPLY:
                    $downloadedList[] = sReply::detail( $mReply->get_reply_by_id( $dl->target_id ) );
                    break;
            }
        }
        return $downloadedList;
    }

    public static function deleteDLRecord( $uid, $id ){
        $mDownload = new mDownload();
        $download = $mDownload-> get_download_record_by_id( $id );
        if(!$download){
            return error( 'WRONG_ARGUMENTS', '请选择删除的记录' );
        }
        if( $download->uid != $uid ){
            return error( 'WRONG_ARGUMENTS', '这个不是你的下载记录');
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
                    $ask = sAsk::detail( $ask );
                    $url    = $ask['image_url'];
                }
                break;
            case mDownload::TYPE_REPLY:
                if($reply = sReply::getReplyById($target_id)) {
                    $reply = sReply::detail( $reply );
                    $url    = $reply['image_url'];
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
        $cond = [
            'uid' => $uid,
            'type' => $type,
            'target_id' => $target_id,
            'status' => $mDownload::STATUS_NORMAL
        ];

        $d = $mDownload->firstOrNew( $cond );
        $data = $cond;
        if( !$d->id ){
            $data['create_time'] = time();
            $data['ip'] = get_client_ip();
            $data['url'] = $url;
        }
        $data['update_time'] = time();
        $d->fill($data);

        sActionLog::init( 'DOWNLOAD_FILE' );
        $d->save();
        sActionLog::save( $d );

        return true;
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

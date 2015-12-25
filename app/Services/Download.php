<?php namespace App\Services;

use App\Models\Download as mDownload,
    App\Models\Reply as mReply,
    App\Models\Ask as mAsk;

use App\Services\Ask as sAsk,
    App\Services\Reply as sReply,
    App\Services\User as sUser,
    App\Services\Category as sCategory,
    App\Services\ActionLog as sActionLog;

use App\Counters\AskDownloads as cAskDownloads,
    App\Counters\UserDownloadAsks as cUserDownloadAsks,
    App\Counters\CategoryDownloads as cCategoryDownloads;


use App\Facades\CloudCDN;

class Download extends ServiceBase
{
    public static function getDownloaded( $uid, $page, $size, $last_updated, $channel_id = NULL ){
        $mDownload = new mDownload();
        $mAsk = new mAsk();
        $mReply = new mReply();

        //todo 暂时只能下载求助
        //$downloaded = $mDownload->get_downloaded( $uid, $page, $size, $last_updated );
        $downloaded = $mDownload->get_ask_downloaded( $uid, $channel_id, $page, $size, $last_updated );
        $downloadedList = array();
        foreach( $downloaded as $dl ){
            $downloadedList[] = self::detail( $dl );
        }
        return $downloadedList;
    }

    /**
     * 获取进行中的用户
     */
    public static function getAskDownloadedUsers($ask_id, $page, $size) {
        $download   = new mDownload;
        $users      = $download->get_ask_downloaded_users($ask_id, $page, $size);

        $data = array();
        foreach($users as $user) {
            $data[] = sUser::brief($user);
        }

        return $data;
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

    public static function deleteDLRecord( $uid, $target_id ){
        $mDownload = new mDownload();
        //ask
        $download = $mDownload->get_download_record( $uid, $target_id, $mDownload::STATUS_NORMAL);
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

        $urls = array();
        if($type == mLabel::TYPE_ASK) {
            $model  = sAsk::getAskById($target_id);
            if(!$model) 
                return error('ASK_NOT_EXIST');
            $type   = mDownload::TYPE_ASK;
            $uploads= sUpload::getUploadByIds(explode(',', $model->upload_ids));
            #todo: 打包下载
            foreach($uploads as $upload) {
                $urls[]   = CloudCDN::file_url($uploads[0]->savename);
            }
        }
        else if($type == mLabel::TYPE_REPLY) {
            $model  = sAsk::getAskById($target_id);
            if(!$model) 
                return error('REPLY_NOT_EXIST');
            $type   = mDownload::TYPE_REPLY; 
            $upload = sUpload::getUploadById($model->upload_id);
            $urls[]  = CloudCDN::file_url($upload->savename);
        }

        if (empty($urls)){
            return error( 'DOWNLOAD_FILE_DOESNT_EXISTS', '访问出错' );
        }

        return $urls;
    }


    public static function saveDownloadRecord( $uid, $type, $target_id, $url, $category_id = 0 ){
        $mDownload = new mDownload();

        sActionLog::init( 'DOWNLOAD_FILE' );
        $mDownload->assign(array(
            'uid'   => $uid,
            'type'  => $type,
            'target_id'     => $target_id,
            'category_id'   => $category_id,
            'url'   => $url,
            'ip'    => get_client_ip(),
            'status'    => mDownload::STATUS_NORMAL
        ));
        $mDownload->save();
        sActionLog::save( $mDownload );

        cAskDownloads::inc($target_id);
        cCategoryDownloads::inc($category_id);
        cUserDownloadAsks::inc($uid);

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

    /**
     * 格式化下载输出
     */
    public static function detail( $dl ){
        $mAsk = new mAsk();
        $mReply = new mReply();

        $result = $dl->toArray();

        switch( $dl->type ){
        case mAsk::TYPE_ASK:
            $ask    = sAsk::getAskById( $dl->target_id );
            if($dl->target_id == 0) {
                return $result;
            }
            $result['uid'] = $ask->uid;
            $result = array_merge(sAsk::detail($ask), $result);
            break;
        case mAsk::TYPE_REPLY:
            $reply  = $mReply->get_reply_by_id( $dl->target_id );
            $result['uid'] = $reply->uid;
            $result = array_merge(sReply::detail($reply), $result);
            break;
        }
        $result['id'] = $dl->target_id;
        $result['type'] = $dl->type;
        if( $result['category_id'] > config('global.CATEGORY_BASE') ){
            $category = sCategory::detail( sCategory::getCategoryById( $dl->category_id) );
            $result['category_name'] = $category['display_name'];
            $result['category_type'] = $category['category_type'];
        }
        else{
            $result['category_name'] = '';
            $result['category_type'] = '';
        }

        //todo: remove
        $result['category_id'] = intval($dl->category_id);
        return $result;
    }
}

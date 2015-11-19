<?php

namespace App\Services;

use Phalcon\Mvc\Model\Resultset\Simple as Resultset,
    App\Models\Ask as mAsk,
    App\Models\Follow as mFollow,
    App\Models\UserScore as mUserScore,
    App\Models\Comment as mComment,
    App\Models\Count as mCount,
    App\Models\Reply as mReply,
    App\Models\Label as mLabel,
    App\Models\Record as mRecord,
    App\Models\Usermeta as mUsermeta,
    App\Models\Role as mRole,
    App\Models\Download as mDownload,
    App\Models\Collection as mCollection,
    App\Models\ThreadCategory as mThreadCategory,
    App\Models\UserRole as mUserRole;

use App\Services\ActionLog as sActionLog,
    App\Services\Download as sDownload,
    App\Services\Count as sCount,
    App\Services\Label as sLabel,
    App\Services\Upload as sUpload,
    App\Services\UserScore as sUserScore,
    App\Services\UserDevice as sUserDevice,
    App\Services\Ask as sAsk,
    App\Services\Follow as sFollow,
    App\Services\Comment as sComment,
    App\Services\Message as sMessage,
    App\Services\Focus as sFocus,
    App\Services\UserRole as sUserRole,
    App\Services\Collection as sCollection,
    App\Services\ThreadCategory as sThreadCategory,
    App\Services\User as sUser;

use Queue, App\Jobs\Push, DB;
use App\Facades\CloudCDN;

class Reply extends ServiceBase
{

    /**
     * 添加新回复
     *
     * @param integer $uid        用户ID
     * @param string  $desc       大神带话
     * @param integer $reply_id     求PSID
     * @param \App\Models\Upload $upload_obj 上传对象
     */
    public static function addNewReply($uid, $ask_id, $upload_id, $desc = '', $type = null, $target_id = null)
    {
        if ( !$upload_id ) {
            return error('UPLOAD_NOT_EXIST');
        }
        $ask    = sAsk::getAskById($ask_id);
        if (!$ask) {
            return error('ASK_NOT_EXIST');
        }

        $reply = new mReply;
        sActionLog::init('POST_REPLY', $reply);

        $status = mReply::STATUS_NORMAL;
        if(sUserRole::checkAuth($uid, mRole::TYPE_PARTTIME)){
            // 兼职逻辑，如果兼职需要审核
            $status = mReply::STATUS_READY;
        }
        else {
            // 如果非兼职，则更新求助的作品数量
            $reply_count = $ask->reply_count + 1;
            $ask->assign(array(
                'reply_count'=>$reply_count,
                'update_time'=>time()
            ));
            $ask->save();
        }
        $upid = $upload_id;
        $upload = sUpload::getUploadById($upid);

        $reply->assign(array(
            'uid'=>$uid,
            'desc'=>$desc,
            'ask_id'=>$ask_id,
            'upload_id'=>$upload->id,
            'status'=>$status,
            'device_id'=>sUserDevice::getUserDeviceId($uid)
        ));

        sDownload::uploadStatus(
            $uid,
            $ask_id,
            $upload->savename
        );
        $reply->save();

        /*
        #作品推送
        Queue::push(new Push(array(
            'uid'=>$uid,
            'ask_id'=>$ask_id,
            'reply_id'=>$reply->id,
            'type'=>'post_reply'
        )));
         */
        Queue::push(new Push(array(
            'uid'=>$uid,
            'ask_id'=>$ask_id,
            'reply_id'=>$reply->id,
            'type'=>'ask_reply'
        )));

        // 给每个添加一个默认的category，话说以后会不会爆掉
        sThreadCategory::addNormalThreadCategory( $uid, mReply::TYPE_REPLY, $reply->id );
        sActionLog::save($reply);
        return $reply;
    }

    /**
     * 新建一个定时回复
     *
     * @param integer $uid        用户ID
     * @param string  $desc       大神带话
     * @param integer $reply_id     求PSID
     * @param \App\Models\Upload $upload_obj 上传对象
     * @param string  $time       定的时间 Y-m-d H:i:s
     * @param integer $status     状态
     */
    public static function addNewTimingReply($uid, $desc, $ask_id, $upload, $time, $status=self::STATUS_NORMAL)
    {
        $reply = self::addNewReply($uid,$ask_id, $upload_id, $desc);

        $reply->status  = $status;
        $reply->create_time = $time;
        $reply->update_time = $time;

        sReplymeta::writeMeta($reply->id, mReplymeta::KEY_TIMING, $time);
        $ret = $reply->save();

        sActionLog::init('NEW_BATCH_REPLY');
        sActionLog::save();
        //todo: action log
        return $ret;
    }

    public static function getUserReplies( $uid, $page, $size){
        $mReply= new mReply();
        $replies = $mReply->get_replies(array('replies.uid'=>$uid), $page, $size);

        $data       = array();
        foreach($replies as $reply){
            $data[] = self::detail($reply);
        }

        return $data;
    }

        // $builder = self::query_builder('r');
        // $asks    = 'Psgod\Models\Ask';
        // return $builder->where('r.status = '.self::STATUS_NORMAL.
        //     " AND r.uid = ".$uid.
        //     " AND r.create_time < ".$last_updated)
        //     //->join($asks, "r.ask_id= r.id", "a", 'left')
        //     //->columns('id, content, x, y, direction')
        //     ->orderBy('r.create_time desc')
        //     ->limit($limit, ($page-1)*$limit)
        //     ->getQuery()
        //     ->execute();

    /**
     * 通过id获取reply
     */
    public static function getReplyById($reply_id) {
        $reply = (new mReply)->get_reply_by_id($reply_id);
        return $reply;
    }

    /**
     * 通过upload_id获取reply，后续要改
     */
    public static function getReplyByUploadId($upload_id) {
        $reply = (new mReply)->get_reply_by_upload_id($upload_id);
        return $reply;
    }

    /**
     * 通过ids获取replies
     */
    public static function getRepliesByIds($reply_ids) {
        $replies = (new mReply)->get_replies_by_replyids($reply_ids, 1, 0);

        return $replies;
    }

    /**
     * 获取作品数量
     */
    public static function getRepliesCountByAskId($ask_id) {
        return (new mReply)->count_replies_by_askid($ask_id);
    }

    public static function getRepliesByAskId($ask_id, $page, $size) {
        $mReply = new mReply;

        $replies    = $mReply->get_replies_by_askid($ask_id, $page, $size);

        $data       = array();
        foreach($replies as $reply){
            $data[] = self::detail($reply);
        }

        return $data;
    }

    public static function getAskRepliesWithOutReplyId($ask_id, $reply_id, $page, $size) {

        $mReply = new mReply;

        $replies    = $mReply->get_ask_replies_without_replyid($ask_id, $reply_id, $page, $size);

        $data       = array();
        foreach($replies as $reply){
            $data[] = self::detail($reply);
        }

        return $data;
    }

    public static function getActivities( $page = 1 , $size = 15 ){
        $activity_ids = sThreadCategory::getRepliesByCategoryId( mThreadCategory::CATEGORY_TYPE_ACTIVITY, $page, $size );
        $activities = [];
        foreach( $activity_ids as $thr_cat ){
            $reply = self::detail( self::getReplyById( $thr_cat->target_id ) );
            $activities[] = $reply;
        }

        return $activities;
    }

    /**
     * [get_user_scores 获取评分]
     */
    public static function getReplyScore($uid, $reply_id)
    {
        $mUserScore = new mUserScore;

        $score = $mUserScore->getReplyScore($uid, $reply_id);
        return $score;
    }

    /**
     * 获取作品收藏列表
     */
    public static function getCollectionReplies($uid, $page, $limit) {
        $mCollection = new mCollection;
        $mReply      = new mReply;

        $collectionReplies = $mCollection->get_user_collection($uid, $page, $limit );
        $data       = array();
        foreach($collectionReplies as $reply){
            $data[] = self::detail($reply->reply);
        }

        return $data;
    }

    /**
     * 获取用户求p数量
     */
    public static function getUserReplyCount ( $uid ) {
        return (new mReply)->count_user_reply($uid);
    }

    /**
     * 获取各种数量
     */
    public static function getReplyCount ( $uid, $count_name ) {
        $reply = new mReply;
        $count_name  = $count_name.'_count';
        if (!property_exists($reply, $count)) {
            return error('WRONG_ARGUMENTS');
        }

        return mReply::sum(array(
            'column'     =>$count_name,
            'conditions' =>"uid = {$uid}"
        ));
    }

    /**
     * 数量变更
     */
    public static function updateReplyCount($target_id, $count_name, $status ){
        $type  = mReply::TYPE_REPLY;
        $count = sCount::updateCount($target_id, $type, $count_name, $status);
        //todo: 是否需要报错提示,不需要更新
        if (!$count)
            return false;

        $reply  = mReply::find($target_id);

        if (!$reply)
            return error('REPLY_NOT_EXIST');

        $count_name  = $count_name.'_count';
        if(!isset($reply->$count_name)) {
            return error('WRONG_ARGUMENTS');
        }

        if ($count->status == mCount::STATUS_NORMAL) {
            $value = 1;

            #点赞推送
            Queue::push(new Push(array(
                'uid'=>_uid(),
                'target_uid'=>$reply->uid,
                //前期统一点赞,不区分类型
                'type'=>'like_reply',
                'target_id'=>$id
            )));
        }
        else
            $value = -1;

        $reply->$count_name += $value;
        if ($reply->$count_name < 0)
            $reply->$count_name = 0;

        // 通过名字获取日志记录的键值
        $name   = ( $value==1? '': 'CANCEL_' ).strtoupper($count_name).'_REPLY';
        $key    = sActionLog::getActionKey($name);
        sActionLog::init( $key, $reply );

        $ret    = $reply->save();
        sActionLog::save( $ret );

        return $ret;
    }

    /**
     * 更新作品审核状态
     */
    public static function updateReplyStatus($reply, $status, $uid, $data="")
    {
        sActionLog::init( 'UPDATE_REPLY_STATUS', $reply );
        $mUserScore = new mUserScore;

        $reply->status = $status;
        $reply_id   = $reply->id;
        if( !$uid ){
            $uid = $reply->uid;
        }

        switch($status){
        case mReply::STATUS_NORMAL:
            sUserScore::updateScore($uid, mUserScore::TYPE_REPLY, $reply_id, $data);
            sAsk::updateAskCount ($reply->ask_id, 'reply', mCount::STATUS_NORMAL);
            //sreply::set_reply_count($reply->ask_id);
            break;
        case mReply::STATUS_READY:
            break;
        case mReply::STATUS_REJECT:
            $reply->del_by = $uid;
            $reply->del_time = time();
            sUserScore::updateContent($uid, mUserScore::TYPE_REPLY, $reply_id, $data);
            break;
        case mReply::STATUS_BANNED:
            break;
        case mReply::STATUS_DELETED:
            $reply->del_by = $uid;
            $reply->del_time = time();
            break;
        }

        $ret = $reply->save();
        sActionLog::save( $ret );
        return $ret;
    }

    public static function blockUserReplies( $uid ){
        $mReply = new mReply();
        sActionLog::init('BLOCK_USER_REPLIES');
        $mReply->change_replies_status( $uid, mReply::STATUS_BLOCKED, mReply::STATUS_NORMAL );
        sActionLog::save();
        return true;
    }

    public static function recoverBlockedReplies( $uid ){
        $mReply = new mReply();
        sActionLog::init('RESTORE_USER_REPLIES');
        $mReply->change_replies_status( $uid, mReply::STATUS_NORMAL, mReply::STATUS_BLOCKED );
        sActionLog::save();
        return true;
    }
    /**
     * 获取标准输出(含评论&作品
     */
    public static function detail( $reply, $width = 480) {
        if(!$reply) return array();

        $uid    = _uid();
        $width  = _req('width', $width);

        $data = array();
        $data['id']             = $reply->id;
        //$data['reply_id']     = $reply->id;
        $data['ask_id']         = $reply->ask_id;
        $data['type']           = mLabel::TYPE_REPLY;

        //$data['comments']       = sComment::getComments(mComment::TYPE_REPLY, $reply->id, 0, 5);
        //$data['labels']         = sLabel::getLabels(mLabel::TYPE_REPLY, $reply->id, 0, 0);
        $data['hot_comments']   = sComment::getHotComments(mComment::TYPE_REPLY, $reply->id);

        //$data['is_fan']      = sFollow::checkRelationshipBetween($reply->uid, $uid);

        $data['avatar']         = $reply->replyer->avatar;
        $data['sex']            = $reply->replyer->sex?1: 0;
        $data['uid']            = $reply->replyer->uid;
        $data['nickname']       = $reply->replyer->nickname;

        $data['is_follow']      = sFollow::checkRelationshipBetween($uid, $reply->uid);
        $data['is_download']    = sDownload::hasDownloadedReply($uid, $reply->id);
        $data['uped']           = sCount::hasOperatedReply($uid, $reply->id, 'up');
        $data['collected']      = sCollection::hasCollectedReply($uid, $reply->id);

        $data['upload_id']      = $reply->upload_id;
        $data['create_time']    = $reply->create_time;
        $data['update_time']    = $reply->update_time;
        $data['desc']           = $reply->desc;
        $data['up_count']       = $reply->up_count;
        $data['collect_count']  = sCollection::countCollectionsByReplyId($reply->id);
        //$data['comment_count']  = $reply->comment_count;
        $data['comment_count']  = sComment::countComments(mReply::TYPE_REPLY, $reply->id);
        $data['click_count']    = $reply->click_count;
        $data['inform_count']   = $reply->inform_count;

        $data['share_count']    = $reply->share_count;
        $data['weixin_share_count'] = $reply->weixin_share_count;

        $upload = $reply->upload;
        if(!$upload) {
            dd($reply);
        }

        $image = sUpload::resizeImage($upload->savename, $width, 1, $upload->ratio);
        $data  = array_merge($data, $image);

        //Ask uploads
        //todo: change to Reply->with()
        $ask = sAsk::getAskById($reply->ask_id);
        $data['ask_uploads']    = sAsk::getAskUploads($ask->upload_ids, $width);
        $data['reply_count']    = $ask->reply_count;

        $data['desc']           = $reply->desc;

        $reply->increment('click_count');

        return $data;
    }

    public static function brief( $reply ){
        $data = array();

        $uid    = _uid();
        $width  = _req('width', 480);
        $data['id']             = $reply->id;
        $data['ask_id']         = $reply->ask_id;
        $data['desc']           = $reply->desc;

        $data['avatar']         = $reply->replyer->avatar;
        $data['sex']            = $reply->replyer->sex;
        $data['uid']            = $reply->replyer->uid;
        $data['nickname']       = $reply->replyer->nickname;

        $data['is_follow']      = sFollow::checkRelationshipBetween($uid, $reply->uid);
        $data['is_download']    = sDownload::hasDownloadedReply($uid, $reply->id);
        $data['uped']           = sCount::hasOperatedReply($uid, $reply->id, 'up');
        $data['collected']      = sCollection::hasCollectedReply($uid, $reply->id);

        $data['upload_id']      = $reply->upload_id;
        $data['create_time']    = $reply->create_time;
        $data['update_time']    = $reply->update_time;
        $data['desc']           = $reply->desc;
        $data['up_count']       = $reply->up_count;
        $data['collect_count']  = sCollection::countCollectionsByReplyId($reply->id);
        //$data['comment_count']  = $reply->comment_count;
        $data['comment_count']  = sComment::countComments(mReply::TYPE_REPLY, $reply->id);
        $data['click_count']    = $reply->click_count;
        $data['inform_count']   = $reply->inform_count;

        $data['share_count']    = $reply->share_count;
        $data['weixin_share_count'] = $reply->weixin_share_count;

        $upload = $reply->upload;
        if(!$upload) {
            dd($reply);
        }

        $image = sUpload::resizeImage($upload->savename, $width, 1, $upload->ratio);
        $data  = array_merge($data, $image);

        //Ask uploads
        //todo: change to Reply->with()
        $ask = sAsk::getAskById($reply->ask_id);
        $data['ask_uploads']    = sAsk::getAskUploads($ask->upload_ids, $width);
        $data['reply_count']    = $ask->reply_count;

        DB::table('replies')->increment('click_count');

        return $data;
    }

    public static function getNewReplies( $uid, $lastFetchTime ){
        $ownAskIds = (new mAsk)->get_ask_ids_by_uid( $uid );
        $replies = (new mReply)->get_replies_of_asks( $ownAskIds, $lastFetchTime );

        return $replies;
    }

    public static function getReplies( $cond, $page, $limit, $uid = 0 ) {
        $mReply = new mReply;

        $replies= $mReply->get_replies($cond, $page, $limit, $uid );
        $data = array();
        foreach($replies as $reply){
            $data[] = self::detail($reply);
        }

        return $data;
    }

    public static function getBriefReplies( $cond, $page, $limit, $uid = 0 ) {
        $mReply = new mReply;

        $replies= $mReply->get_replies($cond, $page, $limit, $uid );
        $result = array();
        foreach($replies as $reply){
            $data = array();
            $uid    = _uid();
            $width  = _req('width', 480);
            $data['id']             = $reply->id;
            $data['ask_id']         = $reply->ask_id;
            $data['desc']           = $reply->desc;

            $data['upload_id']      = $reply->upload_id;
            $data['create_time']    = $reply->create_time;
            $data['update_time']    = $reply->update_time;

            $upload = $reply->upload;
            if(!$upload) {
                continue;
            }

            $image = sUpload::resizeImage($upload->savename, $width, 1, $upload->ratio);
            $data  = array_merge($data, $image);

            $result[] = $data;
        }

        return $result;
    }

}

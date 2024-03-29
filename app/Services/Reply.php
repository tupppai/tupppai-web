<?php

namespace App\Services;

use App\Models\Ask as mAsk,
    App\Models\Follow as mFollow,
    App\Models\UserScore as mUserScore,
    App\Models\UserLanding as mUserLanding,
    App\Models\Comment as mComment,
    App\Models\Count as mCount,
    App\Models\Reply as mReply,
    App\Models\Label as mLabel,
    App\Models\Record as mRecord,
    App\Models\Usermeta as mUsermeta,
    App\Models\Role as mRole,
    App\Models\Tag as mTag,
    App\Models\ThreadTag as mThreadTag,
    App\Models\Download as mDownload,
    App\Models\Collection as mCollection,
    App\Models\ThreadCategory as mThreadCategory,
    App\Models\UserRole as mUserRole;

use App\Services\ActionLog as sActionLog,
    App\Services\Download as sDownload,
    App\Services\Count as sCount,
    App\Services\Category as sCategory,
    App\Services\Label as sLabel,
    App\Services\Upload as sUpload,
    App\Services\UserScore as sUserScore,
    App\Services\UserDevice as sUserDevice,
    App\Services\UserLanding as sUserLanding,
    App\Services\Ask as sAsk,
    App\Services\Tag as sTag,
    App\Services\SysMsg as sSysMsg,
    App\Services\Follow as sFollow,
    App\Services\Comment as sComment,
    App\Services\Reward as sReward,
    App\Services\Message as sMessage,
    App\Services\Focus as sFocus,
    App\Services\UserRole as sUserRole,
    App\Services\Collection as sCollection,
    App\Services\ThreadTag as sThreadTag,
    App\Services\ThreadCategory as sThreadCategory,
    App\Services\WXMsg as sWXMsg,
    App\Services\User as sUser;

use App\Counters\AskCounts as cAskCounts;
use App\Counters\ReplyCounts as cReplyCounts;
use App\Counters\UserCounts as cUserCounts;
use App\Counters\CategoryCounts as cCategoryCounts;

use Queue, App\Jobs\Push, DB;
use Redis;
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
    public static function addNewReply($uid, $ask_id, $upload_id, $desc = '', $activity_id = NULL, $tags = '')
    {
        if ( !$upload_id ) {
            return error('UPLOAD_NOT_EXIST');
        }
        if( $treply = self::getReplyByUploadId($upload_id) ){
            return $treply;
            //return error('SYSTEM_ERROR', '该作品已发布成功');
        }
        $ask    = sAsk::getAskById($ask_id);
        // 在没有activity的情况下，ask必须存在。
        if (!$ask && !$activity_id) {
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
            if($ask) {
                cAskCounts::inc($ask->id, 'reply');
            }
        }
        if( sUser::isBlocked( $uid ) ){
            $status = mReply::STATUS_BLOCKED;
        }

        $upid = $upload_id;
        $upload = sUpload::getUploadById($upid);

        $reply->assign(array(
            'uid'=>$uid,
            'desc'=>emoji_to_shortname($desc),
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
        cUserCounts::inc($uid, 'reply');
        cUserCounts::inc($uid, 'inprogress', -1);

        //tags
        $tags = explode(',', $tags);
        $tags = array_filter( $tags );
        foreach( $tags as $tag_name ){
            $tag = sTag::addNewTag( $uid, $tag_name );
            sThreadTag::setTag( $uid, mReply::TYPE_REPLY, $reply->id, $tag->id, mReply::STATUS_NORMAL );
        }

        if($ask) {
            $ask->update_time = $reply->update_time;
            $ask->save();
        }

        /*
        #作品推送
        Queue::push(new Push(array(
            'uid'=>$uid,
            'ask_id'=>$ask_id,
            'reply_id'=>$reply->id,
            'type'=>'post_reply'
        )));
         */
        if( $ask ){
            Queue::push(new Push(array(
                'uid'=>$uid,
                'ask_id'=>$ask_id,
                'reply_id'=>$reply->id,
                'type'=>'ask_reply'
            )));
            $replyAuthor = sUser::getUserByUid( $uid );
            //发送微信模板消息
            $userlanding = sUserLanding::getUserLandingByUid( $ask->uid, mUserLanding::TYPE_WEIXIN_MP );

            $result = false;
            if( $userlanding ){
                $openid = $userlanding->openid;
                try{
                    $tplVars = [
                        'first'=>'P图大神“'.$replyAuthor->nickname.'”把你的图片P成这样啦，快去看看吧~',
                        'keyword1' => '普通求P',
                        'keyword2' => date('Y-m-d H:i:s'),
                        'keyword3' => $reply->desc,
                        'remark' => '如果希望被更多大神P，马上分享你的求P帖，邀请你身边的大神朋友一起来玩吧~'
                    ];
                    $jumpUrl = '/services/index.html#detail/detail/2/'.$reply->id;
                    $result = sWXMsg::sendMsg(sWXMsg::TPL_ID_HAS_NEW_REPLY, $tplVars, [$openid], $jumpUrl);
                }
                catch( \Exception $e ){
                    $result = 'false';
                }
            }
        }

        // 给每个添加一个默认的category，话说以后会不会爆掉
        sThreadCategory::addNormalThreadCategory( $uid, mReply::TYPE_REPLY, $reply->id );
        if( $activity_id ){
            sThreadCategory::addCategoryToThread( $uid, mReply::TYPE_REPLY, $reply->id, $activity_id, mThreadCategory::STATUS_NORMAL );
        }
        else{
            $activity_id = 0;
        }

        cCategoryCounts::inc( $activity_id, 'replies' );
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
        return $ret;
    }

    public static function getUserReplies( $uid, $page, $size){
        $mReply= new mReply();
        $replies = $mReply->get_user_replies(array('uid'=>$uid), $page, $size);

        $data       = array();
        foreach($replies as $reply){
            $data[] = self::detail($reply);
        }

        return $data;
    }

    public static function getUserReplyIds( $uid, $page, $size ){
        return (new mReply)->get_reply_ids_by_uid( $uid, $page, $size );
    }

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
    public static function getRepliesCountByAskId($ask_id, $uid = null) {
        return (new mReply)->count_replies_by_askid($ask_id, $uid );
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

    /**
     * 通过askid获取塞满假数据的replies
     */
    public static function getFakeRepliesByAskId($ask_id, $page, $size) {
        $mReply = new mReply;

        $replies    = $mReply->get_replies_by_askid($ask_id, $page, $size);

        $data       = array();
        foreach($replies as $reply){
            $data[] = self::fake($reply);
        }

        return $data;
    }

    public static function getReplyIdsByUid($uid) {
        $mReply = new mReply;

        $replies    = $mReply->get_replies_by_uid($uid);

        $ids = array();
        foreach($replies as $reply){
            $ids[] = $reply->id;
        }

        return $ids;
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
            return error('COUNT_NOT_EXIST');
        }

        if ($count->status == mCount::STATUS_NORMAL) {
            $value = 1;

            #点赞推送
            if($count_name == 'up_count') {
                Queue::push(new Push(array(
                    'uid'=>_uid(),
                    'target_uid'=>$reply->uid,
                    //前期统一点赞,不区分类型
                    'type'=>'like_reply',
                    'target_id'=>$reply->id
                )));
            }
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
            if( $reply->ask_id ){
                sAsk::replyAsk($reply->ask_id, mCount::STATUS_NORMAL);
            }
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
        if( $status == mReply::STATUS_DELETED ){
            sSysMsg::postMsg( _uid(), '您的作品"'.$ret->desc.'"已被管理员删除。', mReply::TYPE_REPLY, $ret->id, '', date( 'Y-m-d H:i:s', time()), $ret->uid, 'ask_delete', '' );
           Queue::push(new Push([
                'type'=>'reply_delete',
                'reply_id'=>$reply->id,
                'uid' => $reply->uid
            ]));
        }
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

    public static function getRepliesV2( $cond, $page, $limit, $uid = 0 ) {
        $mReply = new mReply;

        $replies= $mReply->get_replies_v2($cond, $page, $limit, $uid );
        $data = array();
        foreach($replies as $reply){
            $data[] = self::detailV2($reply);
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
            $data['desc']           = shortname_to_unicode($reply->desc);

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

    public static function fake( $reply ){
        $data = array();

        $uid    = _uid();
        $width  = _req('width', 480);
        $data['id']             = $reply->id;
        $data['ask_id']         = $reply->ask_id;
        $data['type']           = mReply::TYPE_REPLY;

        $data['avatar']         = $reply->replyer->avatar;
        $data['sex']            = $reply->replyer->sex;
        $data['uid']            = $reply->replyer->uid;
        $data['nickname']       = shortname_to_unicode($reply->replyer->nickname);

        $data['is_follow']      = false;//sFollow::checkRelationshipBetween($uid, $reply->uid);
        $data['is_fan']         = false;
        $data['is_download']    = false;//sDownload::hasDownloadedReply($uid, $reply->id);
        $data['uped']           = false;//sCount::hasOperatedReply($uid, $reply->id, 'up');
        $data['collected']      = false;//sCollection::hasCollectedReply($uid, $reply->id);

        $data['upload_id']      = $reply->upload_id;
        $data['create_time']    = $reply->create_time;
        $data['update_time']    = $reply->update_time;
        $data['desc']           = shortname_to_unicode($reply->desc);

        $data['love_count']     = sCount::getLoveReplyNum($uid, $reply->id);
        $data['collect_count']  = 0;

        $counts = cReplyCounts::get( $reply->id );
        $data = array_merge( $data, $counts );

        $upload = $reply->upload;
        if(!$upload) {
            return error('UPLOAD_NOT_EXIST');
        }

        $image = sUpload::resizeImage($upload->savename, $width, 1, $upload->ratio);
        $data  = array_merge($data, $image);

        //Ask uploads
        //todo: change to Reply->with()
        //$ask = sAsk::getAskById($reply->ask_id);
        $data['ask_uploads']    = array();//sAsk::getAskUploads($ask->upload_ids, $width);

        //DB::table('replies')->increment('click_count');

        return $data;
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
        $data['reply_id']     = $reply->id;
        $data['ask_id']         = $reply->ask_id;
        $data['type']           = mLabel::TYPE_REPLY;

        $data['hot_comments']   = sComment::getHotComments(mComment::TYPE_REPLY, $reply->id);

        $data['avatar']         = $reply->replyer->avatar;
        $data['sex']            = $reply->replyer->sex?1: 0;
        $data['uid']            = $reply->replyer->uid;
        $data['is_star']        = sUserRole::checkUserIsStar( $reply->replyer->uid );
        $data['nickname']       = shortname_to_unicode($reply->replyer->nickname);

        $data['is_follow']      = sFollow::checkRelationshipBetween($uid, $reply->uid);
        $data['is_fan']         = sFollow::checkRelationshipBetween($reply->uid, $uid);
        $data['is_download']    = sDownload::hasDownloadedReply($uid, $reply->id);
        $data['uped']           = sCount::hasOperatedReply($uid, $reply->id, 'up');
        $data['collected']      = sCollection::hasCollectedReply($uid, $reply->id);

        $data['upload_id']      = $reply->upload_id;
        $data['create_time']    = $reply->create_time;
        $data['update_time']    = $reply->update_time;
        $data['desc']           = shortname_to_unicode($reply->desc);

        $data['love_count']     = sCount::getLoveReplyNum($uid, $reply->id);

        $counts = cReplyCounts::get($reply->id);
        $data = array_merge($data, $counts);

        $upload = $reply->upload;
        if(!$upload) {
            return error('UPLOAD_NOT_EXIST');
        }

        $image = sUpload::resizeImage($upload->savename, $width, 1, $upload->ratio);
        $data  = array_merge($data, $image);

        //Ask uploads
        //todo: change to Reply->with()
        $data['ask_uploads'] = [];
        if( $reply->ask_id ){
            $ask = sAsk::getAskById($reply->ask_id);
            if($ask) {
                $data['ask_uploads']    = sAsk::getAskUploads($ask->upload_ids, $width);
            }
        }

        $data['is_homework'] = false;
        $data['category_id'] = 0;
        $data['category_name'] = '';
        $data['category_type'] = '';
        $is_homework = sThreadCategory::checkedThreadAsCategoryType( mLabel::TYPE_REPLY, $reply->id, mThreadCategory::CATEGORY_TYPE_TUTORIAL );
        $is_timeline = sThreadCategory::checkedThreadAsCategoryType( mLabel::TYPE_REPLY, $reply->id, mThreadCategory::CATEGORY_TYPE_TIMELINE );
        if( $is_homework ){
            $data['is_homework'] = true;
            $tutorial_detail = sAsk::tutorialDetail( sAsk::getAskById($reply->ask_id) );
            $data['tutorial_title'] = $tutorial_detail['title'];
            $data['category_id'] = mThreadCategory::CATEGORY_TYPE_TUTORIAL;
            $data['category_name'] = '教程';
            $data['category_type'] = 'tutorial';
        }
        else if( $is_timeline ){
            $data['is_timeline'] = true;
            $data['category_id'] = mThreadCategory::CATEGORY_TYPE_TIMELINE;
            $data['category_name'] = '动态';
            $data['category_type'] = 'timeline';
        }

        if( $data['category_id'] > config('global.CATEGORY_BASE') ){
            $category = sCategory::detail( sCategory::getCategoryById( $dl->category_id) );
            $data['category_name'] = $category['display_name'];
            $data['category_type'] = $category['category_type'];
        }

        $threadTags = sThreadTag::getTagsByTarget( mThreadTag::TYPE_REPLY, $reply->id );
        $tags = [];
        foreach( $threadTags as $threadTag ){
            $tag = sTag::getTagById( $threadTag->tag_id );
            $tags[] = sTag::brief( $tag );
        }
        $data['tags'] = $tags;

        return $data;
    }

    /**
     * 获取标准输出(含评论&作品
     */
    public static function detailV2( $reply, $width = 480, $commentLimit = 3) {
        if(!$reply) return array();

        $uid    = _uid();
        $width  = _req('width', $width);

        $create_time            = date('Y-m-d');
        if(!empty($reply->create_time)){
            $create_time = $reply->create_time;
        }
        $data = array();
        $data['id']             = $reply->id;
        $data['created_at']     = $create_time;
        $data['reply_id']       = $reply->id;
        $data['ask_id']         = $reply->ask_id;
        $data['type']           = mLabel::TYPE_REPLY;
        $data['avatar']         = $reply->replyer->avatar;
        $data['sex']            = $reply->replyer->sex?1: 0;
        $data['uid']            = $reply->replyer->uid;
        $data['nickname']       = shortname_to_unicode($reply->replyer->nickname);
        $data['uped']           = sCount::hasOperatedReply($uid, $reply->id, 'up');

        $data['upload_id']      = $reply->upload_id;
        $data['desc']           = shortname_to_unicode($reply->desc);
        $data['love_count']     = sCount::getLoveReplyNum($uid, $reply->id);

        $data['is_follow']      = (int)sFollow::checkRelationshipBetween($uid, $reply->uid);
        $data['is_fan']         = (int)sFollow::checkRelationshipBetween($reply->uid, $uid);

        $counts = cReplyCounts::get($reply->id);
        $data = array_merge($data, $counts);

        $upload = $reply->upload;
        if(!$upload) {
            return error('UPLOAD_NOT_EXIST');
        }

        $image = sUpload::resizeImage($upload->savename, $width, 1, $upload->ratio);
        $data  = array_merge($data, $image);

        $th_cats = sThreadCategory::getCategoriesByTarget( mLabel::TYPE_REPLY, $reply->id, [
            mThreadCategory::STATUS_NORMAL,
            mThreadCategory::STATUS_DONE
        ] );
        $cats  = [];
        foreach( $th_cats as $th_cat ){
            if( $th_cat->category_id < config('global.CATEGORY_BASE') ){
                continue;
            }
            $cats[] = sCategory::detail( sCategory::getCategoryById( $th_cat->category_id ) );
        }

        $data['category_type'] = '';
        $data['category_id'] = 0;
        $data['category_name'] = '';
        if( $cats ){
            $data['category_type'] = $cats[0]['category_type'];
            $data['category_id']   = $cats[0]['id'];
            $data['category_name'] = $cats[0]['display_name'];
        }
        $data['has_rewarded'] = false;
        if( $uid ){
            $data['has_rewarded'] = sReward::checkUserHasRewardReply( $uid, $reply->id );
        }
        $data['rewarder_avatars'] = sReward::getRewardUserAvatarsByTarget( mLabel::TYPE_REPLY, $reply->id );

        //Ask uploads
        //todo: change to Reply->with()
        $data['ask_uploads'] = [];
        if( $reply->ask_id ){
            $ask = sAsk::getAskById($reply->ask_id);
            $askDetailed = sAsk::detail($ask);
            if($ask) {
                //旧版
                $data['ask_uploads']   = sAsk::getAskUploads($ask->upload_ids, $width);
                $data['ask'] = [];
                $data['ask']['desc']      = $askDetailed['desc'];
                $data['ask']['author'] = [];
                $data['ask']['author']['uid']    = $askDetailed['uid'];
                $data['ask']['author']['nickname']    = $askDetailed['nickname'];
                $data['ask']['author']['avatar'] = $askDetailed['avatar'];
                $data['ask']['category_type'] = '';
                $data['ask']['category_id'] = 0;
                if( $askDetailed['categories'] ){
                    $data['ask']['category_type'] = $askDetailed['categories'][0]['category_type'];
                    $data['ask']['category_id']   = $askDetailed['categories'][0]['id'];
                    $data['ask']['category_name'] = $askDetailed['categories'][0]['display_name'];
                }
            }
        }

        $data['comment']        = sComment::getCommentsV2(mComment::TYPE_REPLY, $reply->id, 0, $commentLimit);
        $threadTags = sThreadTag::getTagsByTarget( mThreadTag::TYPE_REPLY, $reply->id );
        $tags = [];
        foreach( $threadTags as $threadTag ){
            $tag = sTag::getTagById( $threadTag->tag_id );
            $tags[] = sTag::brief( $tag );
        }
        $data['tags'] = $tags;

        return $data;
    }
    public static function brief( $reply ){
        $data = array();

        $uid    = _uid();
        $width  = _req('width', 480);
        $data['id']             = $reply->id;
        $data['ask_id']         = $reply->ask_id;
        $data['desc']           = shortname_to_unicode($reply->desc);
        $data['type']           = mReply::TYPE_REPLY;

        $data['avatar']         = $reply->replyer->avatar;
        $data['sex']            = $reply->replyer->sex;
        $data['uid']            = $reply->replyer->uid;
        $data['nickname']       = shortname_to_unicode($reply->replyer->nickname);

        $data['is_follow']      = sFollow::checkRelationshipBetween($uid, $reply->uid);
        $data['is_fan']         = sFollow::checkRelationshipBetween($reply->uid, $uid);
        $data['is_download']    = sDownload::hasDownloadedReply($uid, $reply->id);
        $data['uped']           = sCount::hasOperatedReply($uid, $reply->id, 'up');
        $data['collected']      = sCollection::hasCollectedReply($uid, $reply->id);

        $data['upload_id']      = $reply->upload_id;
        $data['create_time']    = $reply->create_time;
        $data['update_time']    = $reply->update_time;

        $data['love_count']     = sCount::getLoveReplyNum($uid, $reply->id);

        $counts = cReplyCounts::get( $reply->id );
        $data = array_merge( $data, $counts );

        $upload = $reply->upload;
        if(!$upload) {
            return error('UPLOAD_NOT_EXIST');
        }

        $image = sUpload::resizeImage($upload->savename, $width, 1, $upload->ratio);
        $data  = array_merge($data, $image);

        //Ask uploads
        //todo: change to Reply->with()
        $data['ask_uploads'] = [];
        if( $reply->ask_id ){
            $ask = sAsk::getAskById($reply->ask_id);
            $data['ask_uploads']    = sAsk::getAskUploads($ask->upload_ids, $width);
        }

        return $data;
    }

    /** ======================= redis counter ========================= */
    /**
     * 分享求助
     */
    public static function shareReply($reply_id, $status) {
        $count = sCount::updateCount ($reply_id, mLabel::TYPE_REPLY, 'share', $status);
        cReplyCounts::inc($reply_id, 'share');
        return $count;
    }
    /**
     * 更新求助举报数量
     */
    public static function informReply($reply_id, $status) {
        $count = sCount::updateCount ($reply_id, mLabel::TYPE_REPLY, 'inform', $status);

        cReplyCounts::inc($reply_id, 'inform');
        return true;
    }
    /**
     * 更新求助评论数量
     */
    public static function commentReply($reply_id, $status, $commenter_uid=NULL) {
        $uid   = _uid();
        if( !$uid ){
            if( is_null($commenter_uid) ){
                return error('EMPTY_UID', '评论者id不能为空');
            }
            $uid = $commenter_uid;
        }

        $count = sCount::updateCount ($reply_id, mLabel::TYPE_REPLY, 'comment', $status, 1, $uid);
        $reply = self::getReplyById($reply_id);

        if($count->status == mCount::STATUS_NORMAL) {
            sActionLog::init( 'TYPE_POST_COMMENT', $reply);
            cReplyCounts::inc($reply->id, 'comment');
            cUserCounts::inc($uid, 'comment');
            cUserCounts::inc($reply->uid, 'badges');
        }
        else {
            sActionLog::init( 'TYPE_DELETE_COMMENT', $reply);
            cReplyCounts::inc($reply->id, 'comment', -1);
            cUserCounts::inc($uid, 'comment', -1);
        }

        sActionLog::save($reply);
        return $reply;
    }

    /**
     * 更新作品点赞数量
     */
    public static function upReply($reply_id, $status, $sender_uid = NULL) {
        $reply = self::getReplyById($reply_id);
        if(!$reply) {
            return error('REPLY_NOT_EXIST');
        }
        $uid = _uid();
        if( !$uid ){
            if( !$sender_uid ){
                return error('EMPTY_UID', '点赞用户不能为空');
            }
            else{
                $uid = $sender_uid;
            }
        }
        $count = sCount::updateCount ($reply_id, mLabel::TYPE_REPLY, 'up', $status, 1, $uid );

        if($count->status == mCount::STATUS_NORMAL) {
            //todo 推送一次，尝试做取消推送
            if(_uid() != $reply->uid)
                Queue::push(new Push(array(
                    'uid'=>$uid,
                    'target_uid'=>$reply->uid,
                    //前期统一点赞,不区分类型
                    'type'=>'like_reply',
                    'target_id'=>$reply->id,
                )));

            cReplyCounts::inc($reply->id,'up');
            cUserCounts::inc($reply->uid, 'badges');
            cUserCounts::inc($reply->uid, 'uped');
            cUserCounts::inc($sender_uid, 'up');
            cCategoryCounts::inc( $reply->id, 'up');
            sActionLog::init( 'TYPE_UP_REPLY', $reply);
        }
        else {
            cReplyCounts::inc($reply->id,'up', -1);
            cUserCounts::inc($reply->uid, 'uped', -1);
            cUserCounts::inc($sender_uid, 'up', -1);
            cCategoryCounts::inc( $reply->id, 'up', -1);
            sActionLog::init( 'TYPE_CANCEL_UP_REPLY', $reply);
        }

        sActionLog::save($reply);

        $is_grad = sThreadCategory::checkedThreadAsCategoryType( mComment::TYPE_REPLY, $reply->id, mThreadCategory::CATEGORY_TYPE_GRADUATION);
        $counts = cReplyCounts::get($reply->id);
        if( $is_grad && ($counts['up_count'] >30 || $counts['comment_count'] >20)){
            //毕业季活动，增加帖子的权重
            Redis::zadd('grad_replies',$counts['up_count']*0.3+$counts['comment_count']*0.7, $reply->id);
        }
        else{
            Redis::zrem('grad_replies', $reply->id);
        }

        return $reply;
    }

    public static function loveReply($reply_id, $num, $status = null) {
        $reply = self::getReplyById($reply_id);
        if( !$reply ) {
            return error('REPLY_NOT_EXIST');
        }

        if( $num < 0 || $num > mLabel::COUNT_LOVE) {
            return error('WRONG_ARGUMENTS');
        }
        if( $num >= mLabel::COUNT_LOVE ){
            $status = mCount::STATUS_DELETED;
        }

        if(is_null($status)) {
            $status     = mCount::STATUS_NORMAL;
        }

        $count      = sCount::updateCount ($reply_id, mLabel::TYPE_REPLY, 'up', $status, $num);
        $change_num = $count->delta;

        if($change_num != 0) {
            cUserCounts::inc($reply->uid, 'badges');
            cReplyCounts::inc($reply->id, 'up', $change_num);
            cUserCounts::inc($reply->uid, 'uped', $change_num);
            cUserCounts::inc(_uid(), 'up', $change_num);
            cCategoryCounts::inc($reply->id, 'up', $change_num);
        }

        sActionLog::init( 'TYPE_CANCEL_UP_REPLY', $reply);
        if($count->status == mCount::STATUS_NORMAL) {
            //todo 推送一次，尝试做取消推送
            if(_uid() != $reply->uid)
                Queue::push(new Push(array(
                    'uid'=>_uid(),
                    'target_uid'=>$reply->uid,
                    //前期统一点赞,不区分类型
                    'type'=>'like_reply',
                    'target_id'=>$reply->id,
                )));

            sActionLog::init( 'TYPE_UP_REPLY', $reply);
        }
        sActionLog::save($reply);
        return $reply;
    }

    /**
     * 获取Ask- > 第一个作品
     */
    public static function getFirstReply($ask_id)
    {
        $mReply = new mReply;
        return $mReply->get_first_reply($ask_id);
    }

    /**
     * 获取Ask- > 最后一个作品
     */
    public static function getLastReply($ask_id)
    {
        $mReply = new mReply;
        return $mReply->get_last_reply($ask_id);
    }

    /**
     * 获取Ask- > 点赞数最高的作品
     * return replyID or false
     */
    public static function getMaxLikeReplyForAsk($askID)
    {
        $Reply = new mReply();
        $replies = $Reply->get_normal_all_replies_by_ask_id($askID);
        $replies = $replies->toArray();
        foreach ($replies as $key => $reply) {
            $counts = cReplyCounts::get($reply['id']);
            $repliesLoveCount[$reply['id']] = $counts['up_count'];
        }
        if(is_array($repliesLoveCount) && !empty($repliesLoveCount)){
            $LoveMaxReplyId =  array_search(max($repliesLoveCount),$repliesLoveCount);
            return self::getReplyById($LoveMaxReplyId);
        }
        return false;
    }

    public static function sumClickByReplyIds( $replyIds ){
        return (new mReply)->sum_clicks_by_reply_ids( $replyIds );
    }

    public static function countUserReply( $uid ){
        return (new mReply)->count_user_reply($uid);
    }

    public static function countUsersByReplyIds( $replyIds ){
        return (new mReply)->count_users_by_reply_ids( $replyIds );
    }

//    public static function reply_ask_index_brief($array)
//    {
//        if(empty($array)){
//            return [];
//        }
//        $ask['ask_id'] = $array['ask']['ask_id'];
//        $ask['type'] = $array['ask']['type'];
//        $ask['avatar'] = $array['ask']['avatar'];
//        $ask['uid'] = $array['ask']['uid'];
//        $ask['nickname'] = $array['ask']['nickname'];
//        $ask['upload_id'] = $array['ask']['upload_id'];
//        $ask['desc'] = $array['ask']['desc'];
//        $ask['comment_count'] = $array['ask']['comment_count'];
//        $ask['ask_images'] = [];
//        foreach($array['ask']['ask_uploads'] as $ask_upload){
//            $ask['images'][] = $ask_upload['image_url'];
//        }
//        $replies['reply_id'] = $array['replies']['reply_id'];
//        $replies['type'] = $array['replies']['type'];
//        $replies['avatar'] = $array['replies']['avatar'];
//        $replies['uid'] = $array['replies']['uid'];
//        $replies['nickname'] = $array['replies']['nickname'];
//        $replies['upload_id'] = $array['replies']['upload_id'];
//        $replies['desc'] = $array['replies']['desc'];
//        $replies['comment_count'] = $array['replies']['comment_count'];
//        $replies['images'] = [];
//        foreach($array['replies']['ask_uploads'] as $ask_upload){
//            $replies['ask_images'][] = $ask_upload['image_url'];
//        }
//
//        $data['ask'] = $ask;
//        $data['reply'] = $replies;
//        dd($data);
//        return $data;
//    }
}

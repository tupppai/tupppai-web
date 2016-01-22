<?php namespace App\Services;

use App\Models\Ask      as mAsk,
    App\Models\User     as mUser,
    App\Models\Count    as mCount,
    App\Models\Label    as mLabel,
    App\Models\Reply    as mReply,
    App\Models\Follow   as mFollow,
    App\Models\Record   as mRecord,
    App\Models\Comment  as mComment,
    App\Models\Download as mDownload,
    App\Models\UserRole as mUserRole,
    App\Models\ThreadCategory as mThreadCategory;

use App\Services\User       as sUser,
    App\Services\Count      as sCount,
    App\Services\Focus      as sFocus,
    App\Services\Follow     as sFollow,
    App\Services\Reply      as sReply,
    App\Services\Label      as sLabel,
    App\Services\Upload     as sUpload,
    App\Services\Comment    as sComment,
    App\Services\UserRole   as sUserRole,
    App\Services\UserDevice as sUserDevice,
    App\Services\Download   as sDownload,
    App\Services\ActionLog  as sActionLog,
    App\Services\ThreadCategory as sThreadCategory,
    App\Services\Category as sCategory,
    App\Services\Collection as sCollection;

use App\Counters\AskUpeds as cAskUpeds;
use App\Counters\AskClicks as cAskClicks;
use App\Counters\AskInforms as cAskInforms;
use App\Counters\AskShares as cAskShares;
use App\Counters\AskComments as cAskComments;
use App\Counters\AskReplies as cAskReplies;
use App\Counters\AskFocuses as cAskFocuses;
use App\Counters\UserUpeds as cUserUpeds;
use App\Counters\UserComments as cUserComments;
use App\Counters\UserReplies as cUserReplies;
use App\Counters\UserAsks as cUserAsks;
use App\Counters\UserBadges as cUserBadges;
use App\Counters\CategoryUpeds as cCategoryUpeds;

use Carbon\Carbon;
use Queue, DB;
use App\Facades\CloudCDN;

class Ask extends ServiceBase
{
    /**
     * 添加新求PS
     *
     * @param string $uid        用户ID
     * @param string $desc       求PS详情
     * @param \App\Models\Upload $upload_obj 上传对象
     */
    public static function addNewAsk($uid, $upload_ids, $desc, $category_id = NULL)
    {
        $uploads = sUpload::getUploadByIds($upload_ids);
        if( !$uploads ) {
            return error('UPLOAD_NOT_EXIST');
        }

        $device_id = sUserDevice::getUserDeviceId($uid);

        $ask = new mAsk;
        $data = array(
            'uid'=>$uid,
            'desc'=>emoji_to_shortname($desc),
            'upload_ids'=>implode(',', $upload_ids),
            'device_id'=>$device_id
        );
        sActionLog::init('POST_ASK', $ask);
        //Todo AskSaveHandle
        $ask->assign( $data );

        if( sUser::isBlocked( $ask->uid ) ){
            /*屏蔽用户*/
            $ask->status = mAsk::STATUS_BLOCKED;
        }
        else{
            /*正常用户*/
            $ask->status = mAsk::STATUS_NORMAL;
        }
        $ask->save();

        #求助推送
        #todo:推送给好友,邀请求助
        /*
        Queue::push(new Push(array(
            'uid'=>$uid,
            'ask_id'=>$ask->id,
            'type'=>'post_ask'
        )));
         */

        // 存储钱将缓存里面的计数器加1,可能隐藏bug：加多了一次
        cUserAsks::inc($ask->id);

        // 给每个添加一个默认的category，话说以后会不会爆掉
        sThreadCategory::addNormalThreadCategory( $uid, mAsk::TYPE_ASK, $ask->id);
        if( $category_id ){
            sThreadCategory::addCategoryToThread( $uid, mReply::TYPE_ASK, $ask->id, $category_id, mThreadCategory::STATUS_NORMAL );
        }
        sActionLog::save($ask);
        return $ask;
    }

    public static function getAsksByIds($ask_ids) {
        return (new mAsk)->get_asks_by_askids($ask_ids, 1, 0);
    }

    /**
     * 通过id获取求助
     */
    public static function getAskById($ask_id) {
        return (new mAsk)->get_ask_by_id($ask_id);
    }

    public static function getActivities( $type, $page = 1 , $size = 15 ){
        switch( $type ){
            case 'valid':
                $status = mThreadCategory::STATUS_NORMAL;
                break;
            case 'done':
                $status = mThreadCategory::STATUS_DONE;
                break;
            case 'next':  //即将开始的活动（公开的）
                $status = mThreadCategory::STATUS_READY;
                break;
            case 'hidden':
            case 'ready': //后台储备的
                $status = mThreadCategory::STATUS_HIDDEN;
                break;
            case 'all':
            default:
                $status = [
                    mThreadCategory::STATUS_NORMAL,
                    mThreadCategory::STATUS_READY,
                    mThreadCategory::STATUS_DONE
                ];
                break;
        }

        $activity_ids = sThreadCategory::getAsksByCategoryId( mThreadCategory::CATEGORY_TYPE_ACTIVITY, $status, $page, $size );
        $activities = [];
        foreach( $activity_ids as $thr_cat ){
            $activity = sCategory::getCategoryById($thr_cat->category_id);
            $activities[] = array(
                'type'=>mAsk::TYPE_ASK,
                'id'=>$thr_cat->target_id,
                'ask_id'=>$thr_cat->target_id,
                'name'=>$activity->display_name,
                //todo remove
                'image_url'=>$activity->banner_pic,
                'pc_banner_pic'=>$activity->pc_banner_pic,
                'banner_pic'=>$activity->banner_pic,
                'url'=>$activity->url
            );
            //$ask = self::detail( self::getAskById( $thr_cat->target_id ) );
            //$activities[] = $ask;
        }

        return $activities;
    }
    /**
     * 通过类型获取首页数据
     */
    public static function getAsksByCond($cond = array(), $page, $limit) {
        $mAsk = new mAsk;
        if( isset( $cond['category_id'] ) ){

            //上面算了15个
            $ths = sThreadCategory::getAsksByCategoryId( $cond['category_id'], [ mThreadCategory::STATUS_NORMAL, mThreadCategory::STATUS_DONE ], $page, $limit );
            $ask_ids = array_column( $ths->toArray(), 'target_id' );
            //下面就不能从page开始算，要第一页
            $asks = (new mAsk)->get_asks_by_askids( $ask_ids, 1, $limit );
        }
        else{
            $asks = $mAsk->get_asks($cond, $page, $limit);
        }

        $data = array();
        foreach($asks as $ask){
            $row = self::detail($ask);
            $row['hot_comments'] = sComment::getHotComments(mComment::TYPE_ASK, $ask->id);
            $row['desc'] = strip_tags($row['desc']);
            $data[] = $row;
        }

        return $data;
    }

    /**
     * 获取用户的求P和作品
     */
    public static function getUserAsksReplies($uid, $page, $limit){
        $mAsk = new mAsk;

        $asks = $mAsk->get_asks( array('uid'=>$uid), $page, $limit);

        $data = array();
        foreach($asks as $ask){
            $tmp    = self::detail($ask);
            //产品说要10个最少
            //$tmp['replies'] = sReply::getRepliesByAskId($ask->id, 0, 10);
            $tmp['replies'] = sReply::getFakeRepliesByAskId($ask->id, 0, 10);
            $data[] = $tmp;
        }

        return $data;
    }

    /**
     * 获取关注人的求助列表
     */
    public static function getFollowAsks($uid, $page, $limit){
        $mFollow = new mFollow;
        $mAsk    = new mAsk;

        $followUsers = $mFollow->getFollowUsers($uid);

        $followUids  = array();
        foreach($followUsers as $user){
            $followUids[] = $user->uid;
        }

        $asks = $mAsk->get_asks_by_uids($followUids, $page, $limit);

        $data = array();
        foreach($asks as $ask){
            $data[] = self::detail($ask);
        }
        return $data;
    }

    /**
     * 我的收藏分页
     */
    public static function getFocusAsks($uid, $page, $limit) {
        $mFocus  = new mFocus;
        $mAsk    = new mAsk;

        $focusAsks = $mFocus->get_user_focus_asks($uid);

        $focusAskids = array();
        foreach($focusAsks as $focus){
            $focusAskids[] = $focus->ask_id;
        }

        $asks = $mAsk->get_asks_by_askids($focusAskids, $page, $limit);

        $data = array();
        foreach($asks as $ask){
            $data[] = self::detail($ask);
        }
        return $data;
    }

    /**
     * 获取回复人列表
     */
    public static function getReplyers($ask_id, $page, $size) {
        $mReply  = new mReply;
        $mUser   = new mUser;

        $replies = $mReply->get_replies(array('ask_id'=>$ask_id), $page, $size);
        $reply_uids = array();
        foreach($replies as $reply) {
            $reply_uids[] = $reply->uid;
        }

        $replyers = $mUser->get_user_by_uids($reply_uids, 0, 0);
        $replyer_arr = array();
        foreach($replyers as $user) {
            $replyer_arr[$user->uid] = sUser::detail($user);
        }

        $data = array();
        foreach($replies as $reply){
            if(isset($replyer_arr[$reply->uid]))
                $data[] = $replyer_arr[$reply->uid];
        }

        return $data;
    }

    /**
     * 获取用户求p数量
     */
    public static function getUserAskCount ( $uid ) {
        return (new mAsk)->count_asks_by_uid($uid);
    }

    /**
     * 数量变更
     */
    public static function updateAskCount ($id, $count_name, $status){
        $count = sCount::updateCount ($id, mLabel::TYPE_ASK, $count_name, $status);

        $mAsk   = new mAsk;
        $ask    = $mAsk->get_ask_by_id($id);
        if (!$ask)
            return error('ASK_NOT_EXIST');

        $count_name  = $count_name.'_count';
        if(!isset($ask->$count_name)) {
            return error('COUNT_TYPE_NOT_EXIST', 'Ask doesn\'t exists '.$count_name.'.');
        }

        $value = 0;
        if ($count->status == mCount::STATUS_NORMAL) {
            $value = 1;

            #点赞推送
            if($count_name == 'up_count')
                Queue::push(new Push(array(
                    'uid'=>_uid(),
                    'target_uid'=>$ask->uid,
                    //前期统一点赞,不区分类型
                    'type'=>'like_ask',
                    'target_id'=>$ask->id,
                )));
        }
        else
            $value = -1;

        $ask->$count_name += $value;
        if ($ask->$count_name < 0)
            $ask->$count_name = 0;

        // 通过名字获取日志记录的键值
        $name   = ( $value==1? '': 'CANCEL_' ).strtoupper($count_name).'_ASK';
        $key    = sActionLog::getActionKey($name);

        $ret    = $ask->save();
        sActionLog::log($key, $ask, $ret);

        return $ret;
    }

    /**
     * 更新求助的内容
     */
    public static function updateAskDesc($ask_id, $desc){
        $ask = self::getAskById($ask_id);
        $ask->desc = $desc;
        $ask->save();

        return $ask;
    }

    /*
    * 恢复求P状态为常态
    */
    public static function freezeAskStatus($ask)
    {
        $ask->status = mAsk::STATUS_FROZEN;
        $ask->save();

        return $ask;
    }

    /**
     * 更新求助审核状态
     */
    public static function updateAskStatus($ask, $status, $_uid, $data=""){
        sActionLog::init( 'UPDATE_ASK_STATUS', $ask );
        $ask->status = $status;

        switch($status){
        case mAsk::STATUS_NORMAL:
            break;
        case mAsk::STATUS_READY:
            break;
        case mAsk::STATUS_REJECT:
            $ask->del_by = $_uid;
            $ask->del_time = time();
            break;
        case mAsk::STATUS_DELETED:
            $ask->del_by = $_uid;
            $ask->del_time = time();
            break;
        }

        $ret = $ask->save();
        sActionLog::save( $ask );

        return $ret;
    }

    public static function blockUserAsks( $uid ){
        $mAsk = new mAsk();
        sActionLog::init('BLOCK_USER_ASKS');
        $mAsk->change_asks_status( $uid, mAsk::STATUS_BLOCKED, mAsk::STATUS_NORMAL );
        sActionLog::save();
        return true;
    }

    public static function recoverBlockedAsks( $uid ){
        $mAsk = new mAsk();
        sActionLog::init('RESTORE_USER_ASKS', $mAsk);
        $mAsk->change_asks_status( $uid, mAsk::STATUS_NORMAL, mAsk::STATUS_BLOCKED );
        $mAsk->uid = $uid;
        //记录被修改到人
        sActionLog::save($mAsk);
        return true;
    }

    public static function getAskUploads($upload_ids_str, $width) {
        $ask_uploads = array();

        $uploads = sUpload::getUploadByIds(explode(',', $upload_ids_str));
        foreach($uploads as $upload) {
            $ask_uploads[] = sUpload::resizeImage($upload->savename, $width, 1, $upload->ratio);
        }

        return $ask_uploads;
    }

    /**
     * 获取标准输出(含评论&作品
     */
    public static function detail( $ask, $width = 480) {
        if(!$ask) return array();

        $uid    = _uid();
        $width  = _req('width', $width);
        $data = array();
        $data['id']             = $ask->id;
        $data['ask_id']         = $ask->id;
        $data['type']           = mLabel::TYPE_ASK;

        $data['is_follow']      = sFollow::checkRelationshipBetween($uid, $ask->uid);
        $data['is_fan']         = sFollow::checkRelationshipBetween($ask->uid, $uid);

        $data['is_download']    = sDownload::hasDownloadedAsk($uid, $ask->id);
        $data['uped']           = sCount::hasOperatedAsk($uid, $ask->id, 'up');
        $data['collected']      = sFocus::hasFocusedAsk($uid, $ask->id);

        $data['avatar']         = $ask->asker->avatar;
        //todo: default value ?
        $data['sex']            = $ask->asker->sex?1:0;
        $data['uid']            = $ask->asker->uid;
        $data['is_star']        = sUserRole::checkUserIsStar( $ask->asker->uid );
        $data['nickname']       = shortname_to_unicode($ask->asker->nickname);

        $data['upload_id']      = $ask->upload_ids;
        $data['create_time']    = $ask->create_time;
        $data['update_time']    = $ask->update_time;
        $data['desc']           = $ask->desc? shortname_to_unicode($ask->desc): '(这个人好懒，连描述都没写)';

        $th_cats = sThreadCategory::getCategoriesByTarget( mLabel::TYPE_ASK, $ask->id, [
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

        $data['categories']     = $cats;

        //todo
        $data['uped_num']       = 0;
        $data['up_count']       = cAskUpeds::get($ask->id, $uid); //$ask->up_count;
        $data['comment_count']  = cAskComments::get($ask->id);
        $data['reply_count']    = cAskReplies::get($ask->id, $uid);
        $data['click_count']    = cAskClicks::get($ask->id);
        $data['inform_count']   = cAskInforms::get($ask->id);
        $data['collect_count']  = cAskFocuses::get($ask->id);
        $data['share_count']    = cAskShares::get($ask->id);
        $data['love_count']     = sCount::getLoveAskNum($uid, $ask->id);

        //这个不存redis了
        $data['weixin_share_count'] = sCount::countWeixinShares(mLabel::TYPE_ASK, $ask->id);

        $data['ask_uploads']    = self::getAskUploads($ask->upload_ids, $width);
        if($data['ask_uploads']){
            $data = array_merge($data, $data['ask_uploads'][0]);
        }

        cAskClicks::inc($ask->id);

        return $data;
    }

    public static function brief( $ask ){
        $data = array();

        $uid    = _uid();
        $width  = _req('width', 480);
        $data['id']             = $ask->id;
        $data['ask_id']         = $ask->id;
        $data['type']           = mAsk::TYPE_ASK;

        $data['avatar']         = $ask->asker->avatar;
        $data['sex']            = $ask->asker->sex;
        $data['uid']            = $ask->asker->uid;
        $data['nickname']       = shortname_to_unicode($ask->asker->nickname);

        $data['upload_id']      = $ask->upload_ids;
        $data['create_time']    = $ask->create_time;
        $data['update_time']    = $ask->update_time;
        $data['desc']           = $ask->desc? shortname_to_unicode($ask->desc): '(这个人好懒，连描述都没写)';

        //todo
        $data['uped_num']       = 0;
        $data['up_count']       = cAskUpeds::get($ask->id, $uid);
        $data['reply_count']    = cAskReplies::get($ask->id, $uid);
        $data['comment_count']  = cAskComments::get($ask->id);

        $data['click_count']    = cAskClicks::get($ask->id);
        $data['inform_count']   = cAskInforms::get($ask->id);
        $data['collect_count']  = cAskFocuses::get($ask->id);
        $data['share_count']    = cAskShares::get($ask->id);

        $data['weixin_share_count'] = sCount::countWeixinShares(mLabel::TYPE_ASK, $ask->id);


        $data['ask_uploads']    = self::getAskUploads($ask->upload_ids, $width);
        $data = array_merge($data, $data['ask_uploads'][0]);

        cAskClicks::inc($ask->id);

        return $data;
    }

    public static function tutorialDetail( $ask ){
        $data = self::detail( $ask );

        $content  = json_decode($data['desc'], true);
        $data['title'] = $content['title'];
        $data['description']  = $content['description'];

        //todo::
        $data['has_shared_to_wechat'] = false;
        $data['paid_amount'] = -1;

        //如果分享到了朋友圈， 相当于打赏0元
        if( $data['paid_amount'] < 0 ){
            $data['ask_uploads'] = array_slice( $data['ask_uploads'], 0, 2 );
        }

        return $data;
    }


    /** ======================= redis counter ========================= */
    /**
     * 分享求助
     */
    public static function shareAsk($ask_id, $status) {
        $count = sCount::updateCount ($ask_id, mLabel::TYPE_ASK, 'share', $status);

        cAskShares::inc($ask_id);
        return $count;
    }

    /**
     * 更新求助举报数量
     */
    public static function informAsk($ask_id, $status) {
        $count = sCount::updateCount ($ask_id, mLabel::TYPE_ASK, 'inform', $status);

        cAskInforms::inc($ask_id);
        return $count;
    }

    /**
     * 更新求助评论数量
     */
    public static function commentAsk($ask_id, $status) {
        $count = sCount::updateCount ($ask_id, mLabel::TYPE_ASK, 'comment', $status);
        $ask   = self::getAskById($ask_id);
        $uid   = _uid();

        if($count->status == mCount::STATUS_NORMAL) {
            sActionLog::init( 'TYPE_POST_COMMENT', $ask);
            cAskComments::inc($ask->id);
            cUserComments::inc($uid);
            cUserBadges::inc($ask->uid);
        }
        else {
            sActionLog::init( 'TYPE_DELETE_COMMENT', $ask);
            cAskComments::inc($ask->id, -1);
            cUserComments::inc($uid, -1);
        }

        sActionLog::save($ask);
        return $ask;
    }

    /**
     * 更新求助作品数量
     */
    public static function replyAsk($ask_id, $status) {
        $count = sCount::updateCount ($ask_id, mLabel::TYPE_ASK, 'reply', $status);
        $ask   = self::getAskById($ask_id);
        $uid   = _uid();

        if($count->status == mCount::STATUS_NORMAL) {
            sActionLog::init( 'TYPE_POST_REPLY', $ask);
            cAskReplies::inc($ask->id, $uid);
            cUserBadges::inc($ask->uid);
            cUserReplies::inc($ask->uid);
        }
        else {
            sActionLog::init( 'TYPE_DELETE_REPLY', $ask);
            cAskReplies::inc($ask->id, $uid, -1);
            cUserReplies::inc($ask->uid, -1);
        }

        sActionLog::save($ask);
        return $ask;
    }

    /**
     * 更新求助点赞数量
     */
    public static function upAsk($ask_id, $status) {
        $ask   = self::getAskById($ask_id);
        if(!$ask) {
            return error('ASK_NOT_EXIST');
        }
        $count = sCount::updateCount ($ask_id, mLabel::TYPE_ASK, 'up', $status);
        $uid   = _uid();

        if($count->status == mCount::STATUS_NORMAL) {
            //todo 推送一次，尝试做取消推送
            Queue::push(new Push(array(
                'uid'=>_uid(),
                'target_uid'=>$ask->uid,
                //前期统一点赞,不区分类型
                'type'=>'like_ask',
                'target_id'=>$ask->id,
            )));

            sActionLog::init( 'TYPE_UP_ASK', $ask);
            cAskUpeds::inc($ask->id);
            cCategoryUpeds::inc(mLabel::TYPE_ASK, $ask->id);
            cUserUpeds::inc($uid);
            cUserBadges::inc($ask->uid);
        }
        else {
            sActionLog::init( 'TYPE_CANCEL_UP_ASK', $ask);
            cAskUpeds::inc($ask->id, -1);
            cCategoryUpeds::inc(mLabel::TYPE_ASK, $ask->id, -1);
            cUserUpeds::inc($uid, -1);
        }

        sActionLog::save($ask);
        return $ask;
    }

    /**
     * Ask第一个作品是否是x天内出现
     */
    public static function isAskHasFirstReplyXDay($askID, $day)
    {
        $firstReply = sReply::getFirstReply($askID);
        if(!$firstReply){
            return false;
        }
        $firstReplyTime = Carbon::createFromTimestamp($firstReply->create_time);
        $diffDay        = $firstReplyTime->diffInDays(Carbon::now());
        $isDayForReply  = ($diffDay <= $day) ? true : false;
        return $isDayForReply;
    }

}

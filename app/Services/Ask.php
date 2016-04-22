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
    App\Services\Reward     as sReward,
    App\Services\SysMsg     as sSysMsg,
    App\Services\Comment    as sComment,
    App\Services\Message    as sMessage,
    App\Services\UserRole   as sUserRole,
    App\Services\UserDevice as sUserDevice,
    App\Services\Download   as sDownload,
    App\Services\ActionLog  as sActionLog,
    App\Services\ThreadCategory as sThreadCategory,
    App\Services\Category as sCategory,
    App\Services\Collection as sCollection;

use App\Counters\AskCounts as cAskCounts;
use App\Counters\UserCounts as cUserCounts;
use App\Counters\CategoryCounts as cCategoryCounts;

use Queue, DB;
use App\Jobs\Push;
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
        if( $a = self::getAskByUploadIds($upload_ids) ) {
            return $a;
            return error('SYSTEM_ERROR', '改求助已上传成功');
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
        cUserCounts::inc($uid,'ask');

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

    public static function getAskByUploadIds($upload_ids) {
        return (new mAsk)->get_ask_by_upload_ids($upload_ids);
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
        if( !isset( $cond['category_id'] ) ){
            $cond['category_id'] = 0;
        }
        $uid = isset( $cond['uid'] ) ? $cond['uid'] : NULL;
        //上面算了15个
        $ths = sThreadCategory::getAsksByCategoryId( $cond['category_id'], [ mThreadCategory::STATUS_NORMAL, mThreadCategory::STATUS_DONE ], $page, $limit, NULL, $uid );
        $ask_ids = array_column( $ths->toArray(), 'target_id' );
        //下面就不能从page开始算，要第一页
        $asks = (new mAsk)->get_asks_by_askids( $ask_ids, 1, $limit );

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
     * 通过类型获取首页数据 V2版本
     */
    public static function getAsksByCondV2($cond = array(), $page, $limit) {
        $mAsk = new mAsk;
        if( !isset( $cond['category_id'] ) ){
            $cond['category_id'] = 0;
        }
        $uid = isset( $cond['uid'] ) ? $cond['uid'] : NULL;
        //上面算了15个
        $ths = sThreadCategory::getAsksByCategoryIdV2( $cond['category_id'], [ mThreadCategory::STATUS_NORMAL, mThreadCategory::STATUS_DONE ], $page, $limit, NULL, $uid );
        $ask_ids = array_column( $ths->toArray(), 'target_id' );
        //下面就不能从page开始算，要第一页
        $asks = (new mAsk)->get_asks_by_askids_v2( $ask_ids, 1, $limit );

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

        $asks = sThreadCategory::getUsersAsk( $uid, $page, $limit );
        // $asks = $mAsk->get_asks( array('uid'=>$uid), $page, $limit);

        $data = array();
        foreach($asks as $ask){
            $tmp    = self::detail(self::getAskById( $ask->target_id ) );
            //产品说要10个最少
            //$tmp['replies'] = sReply::getRepliesByAskId($ask->id, 0, 10);
            $tmp['replies'] = sReply::getFakeRepliesByAskId($ask->target_id, 0, 10);
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
            return error('COUNT_NOT_EXIST', 'Ask doesn\'t exists '.$count_name.'.');
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
        if( $status == mAsk::STATUS_DELETED ){
            sSysMsg::postMsg( _uid(), '您的求助"'.$ask->desc.'"已被管理员删除。', mAsk::TYPE_ASK, $ask->id, '', time(), $ask->uid, 'ask_delete', '' );
            Queue::push(new Push([
                'type'=>'ask_delete',
                'ask_id'=>$ask->id,
                'uid' => $ask->uid
            ]));
        }
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

        $upload_ids = explode(',', $upload_ids_str);
        foreach($upload_ids as $upload_id) {
            $upload = sUpload::getUploadById( $upload_id );
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
        $data['love_count']     = sCount::getLoveAskNum($uid, $ask->id);
        $data = array_merge( $data, cAskCounts::get($ask->id) );

        $data['ask_uploads']    = self::getAskUploads($ask->upload_ids, $width);
        if($data['ask_uploads']){
            $data = array_merge($data, $data['ask_uploads'][0]);
        }

        return $data;
    }

    /**
     * 获取标准输出(含评论&作品
     */
    public static function detailV2( $ask, $width = 480) {
        if(!$ask) return array();

        $uid    = _uid();
        $width  = _req('width', $width);
        $data = array();
        $data['id']             = $ask->id;
        $data['ask_id']         = $ask->id;
        $create_time            = date('Y-m-d H:i');
        if(!empty($ask->create_time)){
            $create_time        = date('Y-m-d H:i');
        }
        $data['created_at']     = $create_time;
        $data['type']           = mLabel::TYPE_ASK;
        $data['avatar']         = $ask->asker->avatar;
        $data['sex']            = $ask->asker->sex?1:0;
        $data['uid']            = $ask->asker->uid;
        $data['nickname']       = shortname_to_unicode($ask->asker->nickname);
        $data['upload_id']      = $ask->upload_ids;
        $data['desc']           = $ask->desc? shortname_to_unicode($ask->desc): '(这个人好懒，连描述都没写)';
        $data['love_count']     = sCount::getLoveAskNum($uid, $ask->id);
        $data['ask_uploads']    = self::getAskUploads($ask->upload_ids, $width);
        //todo
        $data['uped_num']       = 0;
        $data['love_count']     = sCount::getLoveAskNum($uid, $ask->id);
        $data['comment']        = sComment::getCommentsV2(mComment::TYPE_ASK, $ask->id, 0, 5);
        $data = array_merge( $data, cAskCounts::get($ask->id) );
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

        $data = array_merge( $data, cAskCounts::get($ask->id) );

        $data['ask_uploads']    = self::getAskUploads($ask->upload_ids, $width);
        $data = array_merge($data, $data['ask_uploads'][0]);

        return $data;
    }

    public static function ask_index_brief($ask)
    {
        if(empty($ask)){
            return [];
        }
        $data['id'] = $ask['id'];
        $data['ask_id'] = $ask['ask_id'];
        $data['type'] = $ask['type'];
        $data['avatar'] = $ask['avatar'];
        $data['sex'] = $ask['sex'];
        $data['uid'] = $ask['uid'];
        $data['nickname'] = $ask['nickname'];
        $data['desc'] = $ask['desc'];
        $data['ask_image_url'] = $ask['ask_uploads'][0]['image_url'];
        return $data;
    }

    public static function tutorialDetail( $ask ){
        $data = self::detail( $ask );

        $content  = json_decode($data['desc'], true);
        $data['title'] = $content['title'];
        $data['description']  = $content['description'];
        $data['desc'] = '#教程#'.$data['title'];
        $data['up_count'] = sReward::getAskRewardCount( $ask->id ) + sCount::countWeixinShares( mLabel::TYPE_ASK, $ask->id );
        $data['is_tutorial'] = true;
        //是否分享到微信朋友圈
        //todo:: timeine_share const
        $has_shared_to_timeline = (int)sCount::hasOperatedAsk( _uid(), $ask->id, 'timeline_share');
        //打赏次数
        $paid_times = sReward::getUserRewardAskCount( _uid() , $ask->id );

        if( $has_shared_to_timeline || $paid_times || (_uid() == $ask->uid) ){
            $data['has_unlocked'] = (int)true;
        }
        else{
            $data['has_unlocked'] = (int)false;
            $data['ask_uploads'] = array_slice( $data['ask_uploads'], 0, 2 );
        }

        if( $paid_times ){
            $data['has_bought'] = (int)true;
        }
        else{
            $data['has_bought'] = (int)false;
        }

        return $data;
    }


    /** ======================= redis counter ========================= */
    /**
     * 分享求助
     */
    public static function shareAsk($ask_id, $status, $share_type = 'share') {
        $count = sCount::updateCount ($ask_id, mLabel::TYPE_ASK, $share_type, $status);
        cAskCounts::inc($ask_id, $share_type);
        return $count;
    }

    /**
     * 更新求助举报数量
     */
    public static function informAsk($ask_id, $status) {
        $count = sCount::updateCount ($ask_id, mLabel::TYPE_ASK, 'inform', $status);
        cAskCounts::inc($ask_id, 'inform');
        return $count;
    }

    /**
     * 更新求助评论数量
     */
    public static function commentAsk($ask_id, $status, $commenter_uid) {
        $uid   = _uid();
        if( !$uid ){
            if( is_null($commenter_uid) ){
                return error('EMPTY_UID', '评论者id为空');
            }
            $uid = $commenter_uid;
        }

        $count = sCount::updateCount ($ask_id, mLabel::TYPE_ASK, 'comment', $status, 0, $uid);
        $ask   = self::getAskById($ask_id);

        if($count->status == mCount::STATUS_NORMAL) {
            sActionLog::init( 'TYPE_POST_COMMENT', $ask);
            cAskCounts::inc($ask->id, 'comment');
            cUserCounts::inc($uid, 'comment');
            cUserCounts::inc($ask->uid, 'badges');
        }
        else {
            sActionLog::init( 'TYPE_DELETE_COMMENT', $ask);
            cAskCounts::inc($ask->id, -1, 'comment');
            cUserCounts::inc($uid, 'comment', -1);
        }

        sActionLog::save($ask);
        return $ask;
    }

    /**
     * 更新求助作品数量
     */
    public static function replyAsk($ask_id, $status, $commenter_uid = NULL ) {
        $uid   = _uid();
        if( !$uid ){
            if( is_null($commenter_uid) ){
                return error('EMPTY_UID', '作品作者id为空');
            }
            $uid = $commenter_uid;
        }
        $count = sCount::updateCount ($ask_id, mLabel::TYPE_ASK, 'reply', $status, 0, $uid);
        $ask   = self::getAskById($ask_id);

        if($count->status == mCount::STATUS_NORMAL) {
            sActionLog::init( 'TYPE_POST_REPLY', $ask);
            cAskCounts::inc($ask->id, 'reply');
            cUserCounts::inc($ask->uid, 'badges');
            cUserCounts::inc($ask->uid, 'reply');
        }
        else {
            sActionLog::init( 'TYPE_DELETE_REPLY', $ask);
            cAskCounts::inc($ask->id, 'reply', -1);
            cUserCounts::inc($ask->uid, 'reply', -1);
        }

        sActionLog::save($ask);
        return $ask;
    }

    /**
     * 更新求助点赞数量
     */
    public static function upAsk($ask_id, $status, $commenter_uid) {
        $ask   = self::getAskById($ask_id);
        if(!$ask) {
            return error('ASK_NOT_EXIST');
        }
        $uid   = _uid();
        if( !$uid ){
            if( is_null($commenter_uid) ){
                return error('EMPTY_UID', '点赞者id为空');
            }
            $uid = $commenter_uid;
        }
        $count = sCount::updateCount ($ask_id, mLabel::TYPE_ASK, 'up', $status, 0, $uid);

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
            cAskCounts::inc($ask->id, 'up');
            cCategoryCounts::inc($ask->id, 'up');
            cUserCounts::inc($uid, 'up');
            cUserCounts::inc($ask->uid, 'badges');
        }
        else {
            sActionLog::init( 'TYPE_CANCEL_UP_ASK', $ask);
            cAskCounts::inc($ask->id, 'up', -1);
            cCategoryCounts::inc($ask->id, 'up', -1);
            cUserCounts::inc($uid, 'up', -1);
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

    public static function sumClickByAskIds( $askIds ){
        return (new mAsk)->sum_clicks_by_ask_ids( $askIds );
    }

}

<?php

namespace Psgod\Services;

use \Psgod\Models\Ask      as mAsk,
    \Psgod\Models\User     as mUser,
    \Psgod\Models\Count    as mCount,
    \Psgod\Models\Label    as mLabel,
    \Psgod\Models\Reply    as mReply,
    \Psgod\Models\Follow   as mFollow,
    \Psgod\Models\Record   as mRecord,
    \Psgod\Models\Comment  as mComment,
    \Psgod\Models\Download as mDownload,
    \Psgod\Models\UserRole as mUserRole;

use \Psgod\Services\User       as sUser,
    \Psgod\Services\Count      as sCount,
    \Psgod\Services\Focus      as sFocus,
    \Psgod\Services\Label      as sLabel,
    \Psgod\Services\Upload     as sUpload,
    \Psgod\Services\Comment    as sComment,
    \Psgod\Services\Download   as sDownload,
    \Psgod\Services\ActionLog  as sActionLog,
    \Psgod\Services\Collection as sCollection;

class Ask extends ServiceBase
{
    /**
     * 添加新求PS
     *
     * @param string $uid        用户ID
     * @param string $desc       求PS详情
     * @param \Psgod\Models\Upload $upload_obj 上传对象
     */
    public static function addNewAsk($uid, $upload_id, $desc)
    {
        $upload = sUpload::getUploadById($upload_id);
        if( !$upload ) {
            return error('UPLOAD_NOT_EXIST');
        }

        $ask = new mAsk;
        sActionLog::init('POST_ASK', $ask);

        $ask->assign(array(
            'uid'=>$uid,
            'desc'=>$desc,
            'upload_id'=>$upload_id
        ));
        $ask->save();

        sActionLog::save($ask);
        return $ask;
    }

    /**
     * 通过id获取求助
     */
    public static function getAskById($ask_id, $click = true) {
        $askModel = new mAsk();
        $ask   = $askModel->getAskById($ask_id);
        if( !$ask ){
            return error('ASK_NOT_EXIST');
        }
        // 点击数加一
        if($click)
            self::updateAskCount ($ask->id, 'click', mCount::STATUS_NORMAL);

        return $ask;
    }

    /**
     * 通过类型获取首页数据
     */
    public static function getAsksByType($type, $page, $limit) {
        $mAsk = new mAsk;
        $asks = $mAsk->page(array(), $page, $limit, $type);

        $data = array();
        foreach($asks as $ask){
            $data[] = self::detail($ask);
        }

        return $data;
    }

    /**
     * 获取用户的求P
     */
    public static function getUserAsks($uid, $page, $limit){
        $mAsk = new mAsk;

        $asks = $mAsk->page(array( 'uid'=>$uid ), $page, $limit);

        $data = array();
        foreach($asks as $ask){
            $data[] = self::detail($ask);
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
    public static function getFocusAsks($uid, $page , $limit) {
        $mFocus  = new mFocus;
        $mAsk    = new mAsk;

        $focusAsks = $mFocus->getFocusAsks($uid);

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

        $replies = $mReply->page(array('ask_id', $ask_id), $page, $size);
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
            $data[] = $replyer_arr[$reply->uid];
        }

        return $data;
    }

    /**
     * 获取用户求p数量
     */
    public static function getUserAskCount ( $uid ) {
        return mAsk::count(array("uid = {$uid} AND status = ".mFollow::STATUS_NORMAL));
    }

    /**
     * 获取各种数量
     */
    public static function getAskCount ( $uid, $count_name ) {
        $ask = new mAsk;
        $count_name  = $count_name.'_count';
        if (!property_exists($ask, $count)) {
            return error('WRONG_ARGUMENTS');
        }

        return mAsk::sum(array(
            'column'     =>$count_name,
            'conditions' =>"uid = {$uid}"
        ));
    }

    /**
     * 数量变更
     */
    public static function updateAskCount ($id, $count_name, $status){
        $count = sCount::updateCount ($id, mLabel::TYPE_ASK, $count_name, $status);
        //todo: 是否需要报错提示,不需要更新
        if (!$count)
            return false;

        $ask    = mAsk::findFirst($id);
        if (!$ask)
            return error('ASK_NOT_EXIST');

        $count_name  = $count_name.'_count';
        if (!property_exists($ask, $count_name)) {
            return error('WRONG_ARGUMENTS');
        }

        $value = 0;
        if ($count->status == mCount::STATUS_NORMAL)
            $value = 1;
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
     * 更新求助审核状态
     */
    public static function updateAskStatus($ask, $status, $data=""){
        $ask->status = $status;

        switch($status){
        case self::STATUS_NORMAL:
            break;
        case self::STATUS_READY:
            break;
        case self::STATUS_REJECT:
            $ask->del_by = $this->_uid;
            $ask->del_time = time();
            break;
        case self::STATUS_DELETED:
            $ask->del_by = $this->_uid;
            $ask->del_time = time();
            break;
        }

        $ret = $ask->save();

        return $ret;
    }

    /**
     * 通过ask的id数组获取ask对象
     * @param [array] ask_ids
     * @return [array][object]
     */
    public static function umengListUserAskCount($ask_ids) {
        if(!$ask_ids){
            return error('CODE_WRONG_INPUT');
        }
        $ask = new mAsk();
        return $ask->list_user_ask_count($ask_ids);
    }


    /**
     * 获取标准输出(含评论&作品
     */
    public static function detail( $ask ) {

        $uid    = _uid();
        $width  = _req('width', 480);

        $data = array();
        $data['id']             = $ask->id;
        $data['ask_id']         = $ask->id;
        $data['type']           = mLabel::TYPE_ASK;
        $data['comments']       = sComment::getComments(mComment::TYPE_ASK, $ask->id, 0, 5);
        $data['labels']         = sLabel::getLabels(mLabel::TYPE_ASK, $ask->id, 0, 0);

        $data['replyer']        = self::getReplyers($ask->id, 0, 7);

        $data['is_download']    = sDownload::hasDownloadedAsk($uid, $ask->id);
        $data['uped']           = sCount::hasOperatedAsk($uid, $ask->id, 'up');
        $data['collected']      = sFocus::hasFocusedAsk($uid, $ask->id);

        $data['avatar']         = $ask->asker->avatar;
        $data['sex']            = $ask->asker->sex;
        $data['uid']            = $ask->asker->uid;
        $data['nickname']       = $ask->asker->nickname;

        $data['upload_id']      = $ask->upload_id;
        $data['create_time']    = $ask->create_time;
        $data['update_time']    = $ask->update_time;
        $data['desc']           = $ask->desc;
        $data['up_count']       = $ask->up_count;
        $data['comment_count']  = $ask->comment_count;
        $data['click_count']    = $ask->click_count;
        $data['inform_count']   = $ask->inform_count;

        $data['share_count']    = $ask->share_count;
        $data['weixin_share_count'] = $ask->weixin_share_count;
        $data['reply_count']    = $ask->reply_count;

        $upload = $ask->upload;
        $data['image_width']    = $width;
        if( $upload && $upload->ratio ) {
            $data['image_height']   = intval( $width * 1.333 );
        }
        else {
            $data['image_height']   = intval( $width * $upload->ratio );
        }
        $data['image_url']      = \CloudCDN::file_url($upload->savename, $width);

        return $data;
    }
}

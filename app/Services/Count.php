<?php

namespace App\Services;

use App\Models\Count as mCount,
    App\Models\Ask as mAsk,
    App\Models\Reply as mReply;

use App\Services\ActionLog as sActionLog,
    App\Services\Ask as sAsk,
    App\Services\Reply as sReply,
    App\Services\Thread as sThread,
    App\Services\Comment as sComment;

use Log;

class Count extends ServiceBase
{

    /**
     * 添加记录
     */
    public static function addNewCount($uid, $target_id, $type, $action, $status) {
        sActionLog::init( 'ADD_NEW_COUNT' );
        $count = new mCount();
        $count->assign(array(
            'uid'=>$uid,
            'target_id'=>$target_id,
            'type'=>$type,
            'action'=>$action,
            'status'=>$status
        ));

        $ret = $count->save();
        sActionLog::save( $ret );
        return $ret;
    }

    /**
     * 更新记录
     */
    public static function updateCount($target_id, $type, $action, $status = mCount::STATUS_NORMAL, $num = 0, $sender_uid = NULL ) {
        $uid    = _uid();
        if( !$uid ){
            if( !$sender_uid ){
                return error('EMPTY_UID', '请选择操作者id');
            }
            else{
                $uid = $sender_uid;
            }
        }
        $action = self::getActionKey($action);

        if (!$action)
            return error('ACTION_NOT_EXIST');

        $count = (new mCount)->where('uid', $uid)
            ->where('type', $type)
            ->where('target_id', $target_id)
            ->where('action', $action)
            ->first();

        //todo 校验能否重复点击num
        if ($count) {
            sActionLog::init( 'UPDATE_COUNT', $count );
        }
        else {
            $count = new mCount;
            sActionLog::init( 'ADD_NEW_COUNT' );
            $count->num = 0;
        }

        $num_before = $count->num;
        $count->num = ($count->num + 1) % mCount::COUNT_LOVE ;
        //只有count相同的时候才能启用num逻辑
        if( !is_null($status) && $status == mCount::STATUS_DELETED ){
            $count->num = 0;
        }
        $num_after  = $count->num;

        $count->uid     = $uid;
        $count->type    = $type;
        $count->target_id   = $target_id;
        $count->action      = $action;
        $count->update_time = time();
        $count->status      = $status;

        $count->save();
        sActionLog::save( $count );

        $count->delta = $num_after - $num_before;
        return $count;
    }


    public static function getLoveNum( $uid, $target_type, $target_id ){
        $num = (new mCount)->where('uid', $uid)
            ->select('num')
            ->where('type', $target_type)
            ->where('target_id', $target_id)
            ->where('action', self::ACTION_UP)
            ->pluck('num');

        return intval($num);
    }

    /**
     * 获取喜欢的次数
     */
    public static function getLoveAskNum($uid, $ask_id) {
        return self::getLoveNum( $uid, mCount::TYPE_ASK, $ask_id );
    }

    /**
     * 获取喜欢的次数
     */
    public static function getLoveReplyNum($uid, $reply_id) {
        return self::getLoveNum( $uid, mCount::TYPE_REPLY, $reply_id );
    }

    /**
     * 是否点赞
     */
    public static function hasOperated( $uid, $target_type, $target_id, $type ){
        $action_key = self::getActionKey($type);

        $count = (new mCount)->has_counted($uid, $target_type, $target_id, $action_key);
        /*
        if( $type == 'up' && $count && $count->num < 3 ) {
            return false;
        }
         */

        return $count?true: false;
    }
    /**
     * 是否操作系列
     */
    public static function hasOperatedAsk($uid, $target_id, $type = 'up') {
        return self::hasOperated($uid, mCount::TYPE_ASK, $target_id, $type);
    }
    public static function hasOperatedReply($uid, $target_id, $type = 'up') {
        return self::hasOperated($uid, mCount::TYPE_REPLY, $target_id, $type);
    }
    public static function hasOperatedComment($uid, $target_id, $type = 'up') {
        return self::hasOperated($uid, mCount::TYPE_COMMENT, $target_id, $type);
    }

    //public static function get_counts_by_uid($uid){
    //public static function get_uped_reply_counts_by_uid( $uid ){

    const ACTION_UP             =  1;
    const ACTION_LIKE           =  2;
    const ACTION_COLLECT        =  3;
    const ACTION_DOWN           =  4;
    const ACTION_SHARE          =  5;
    const ACTION_WEIXIN_SHARE   =  6;
    const ACTION_INFORM         =  7;
    const ACTION_CLICK          =  8;
    const ACTION_COMMENT        =  9;
    const ACTION_REPLY          = 10;
    const ACTION_TIMELINE_SHARE = 11;

    public static function data($key = null) {
        $data = array(
            self::ACTION_UP         => 'up',
            self::ACTION_LIKE       => 'like',
            self::ACTION_COLLECT    => 'collect',
            self::ACTION_DOWN       => 'down',
            self::ACTION_SHARE      => 'share',
            self::ACTION_WEIXIN_SHARE   => 'weixin_share',
            self::ACTION_INFORM     => 'inform',
            self::ACTION_CLICK      => 'click',
            self::ACTION_COMMENT    => 'comment',
            self::ACTION_REPLY      => 'reply',
            self::ACTION_TIMELINE_SHARE => 'timeline_share'
        );

        return $data;
    }

    public static function getActionKey($key) {
        $data = self::data();
        if (!$key) {
            return error('KEY_NOT_EXIST');
        }
        $data = array_flip($data);
        if(!isset($data[$key])){
            return error('KEY_NOT_EXIST');
        }

        return $data[$key];
    }

    /**
     * 获取点赞数据
     */
    public static function getUnreadLikes( $uid, $last_fetch_msg_time ){
        //目前只有作品会收到赞
        //todo 考虑给counts表加一个target_uid字段
        $reply_ids = sReply::getReplyIdsByUid($uid);

        $likes = (new mCount)->get_counts_by_replyids($reply_ids, $last_fetch_msg_time, mCount::ACTION_UP);

        return $likes;
    }

    public static function getUpedCountsByUid( $uid, $page, $size ){
        $mCount = new mCount();
        $counts = $mCount->get_counts_by_uid( $uid, self::ACTION_UP, $page, $size );
        $data   = array();
        foreach($counts as $count) {
            $data[] = sThread::parse($count->type, $count->target_id);
        }
        return $data;
    }

    public static function countActionByTarget($target_type, $target_id, $action ){
        $mCount = new mCount();
        return $mCount->count_by_cond([
            'type'      => $target_type,
            'target_id' => $target_id,
            'action'    => $action
        ]);
    }
    public static function countWeixinShares($type, $id) {
        return self::countActionByTarget( $type, $id, self::ACTION_WEIXIN_SHARE);
    }
}

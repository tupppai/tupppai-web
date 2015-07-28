<?php

namespace Psgod\Services;

use \Psgod\Models\Count as mCount,
    \Psgod\Models\Ask as mAsk,
    \Psgod\Models\Reply as mReply;

use \Psgod\Services\ActionLog as sActionLog,
    \Psgod\Services\Ask as sAsk,
    \Psgod\Services\Reply as sReply,
    \Psgod\Services\Comment as sComment;

class Count extends ServiceBase
{

    /**
     * 添加记录
     */
    public static function addNewCount($uid, $target_id, $type, $action, $status) {
        $count = new mCount();
        $count->assign(array(
            'uid'=>$uid,
            'target_id'=>$target_id,
            'type'=>$type,
            'action'=>$action,
            'status'=>$status
        ));

        $ret = $count->save();
        return $ret;
    }

    /**
     * 更新记录
     */
    public static function updateCount($target_id, $type, $action, $status) {
        $uid    = _uid();
        $action = self::getActionKey($action);

        if (!$action)
            return error('ACTION_NOT_EXIST');

        $count = mCount::findFirst(
            " uid = {$uid} ".
            " AND target_id = {$target_id} ".
            " AND type = {$type} ".
            " AND action={$action}"
        );

        // 如果状态相同则不更新
        if($count && $count->status == $status) {
            return false;
        }

        if( !$count ) {
            $ret = self::addNewCount(
                $uid,
                $target_id,
                $type,
                $action,
                $status
            );
        }
        else {
            $count->status = $status;
            $ret = $count->save();
        }

        return $ret;
    }

    /**
     * 是否点赞
     */
    public static function hasOperated( $uid, $target_type, $target_id, $type ){
        $action_key = self::getActionKey($type);

        $count = mCount::findFirst(
            ' type=' . $target_type .
            ' AND target_id=' . $target_id .
            ' AND status=' . mCount::STATUS_NORMAL .
            ' AND uid=' . $uid .
            ' AND action=' . $action_key
        );

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

    const ACTION_UP             = 1;
	const ACTION_LIKE           = 2;
	const ACTION_COLLECT        = 3;
	const ACTION_DOWN           = 4;
	const ACTION_SHARE          = 5;
    const ACTION_WEIXIN_SHARE   = 6;
	const ACTION_INFORM         = 7;
	const ACTION_CLICK          = 8;
	const ACTION_COMMENT        = 9;

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
            self::ACTION_COMMENT    => 'comment'
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
}

<?php

namespace App\Services;

use \App\Models\Count as mCount,
    \App\Models\Ask as mAsk,
    \App\Models\Reply as mReply;

use \App\Services\ActionLog as sActionLog,
    \App\Services\Ask as sAsk,
    \App\Services\Reply as sReply,
    \App\Services\Comment as sComment;

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
    public static function updateCount($target_id, $type, $action, $status) {
        #todo: remove _uid()
        $uid    = _uid();
        $action = self::getActionKey($action);

        if (!$action)
            return error('ACTION_NOT_EXIST');

        $cond = [
            'uid' => $uid, 
            'type' => $type,
            'target_id' => $target_id,
            'action' => $action
        ];
        $count = (new mCount)->firstOrNew( $cond );
        sActionLog::init( 'UPDATE_COUNT', $count );

        $data = $cond;
        if( !$count->id ){
            if( $status == mCount::STATUS_DELETED ){
                return true;
            }
            $data['create_time'] = time();
        }
        $data['update_time'] = time();
        $data['status'] = $status;
        $ret = $count->fill($data)->save();
        sActionLog::save( $ret );

        return $ret;
    }

    /**
     * 是否点赞
     */
    public static function hasOperated( $uid, $target_type, $target_id, $type ){
        $action_key = self::getActionKey($type);

        $count = (new mCount)->has_counted($uid, $target_type, $target_id, $action_key);
        
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

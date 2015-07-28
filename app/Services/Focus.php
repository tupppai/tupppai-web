<?php

namespace App\Services;
use \App\Models\Focus as mFocus;

class Focus extends ServiceBase
{

    /**
     * 添加新关注
     */
    public static function addNewFocus($uid, $aid, $status){
        $focus = new self();
        $focus->uid = $uid;
        $focus->ask_id = $aid;
        $focus->create_time = time();
        $focus->update_time = time();
        $focus->status = $status;

        return $focus->save_and_return($focus);
    }

    public static function focusAsk($uid, $ask_id, $status) {

        if( !mAsk::findFirst($target_id) )
            return error('ASK_NOT_EXIST');

        $focus = mFocus::findFirst(
            " uid = {$uid} ".
            " AND ask_id = {$target_id} "
        );
        if( !$focus ) {
            return self::addNewFocus(
                $uid,
                $ask_id,
                $status
            ) ;
        }

        if($focus->status == $status) {
            return $focus;
        }
        if( $status == Focus::STATUS_NORMAL ){
            ActionLog::log(ActionLog::TYPE_FOCUS_ASK, array(), $ret);
        }
        else {
            ActionLog::log(ActionLog::TYPE_CANCEL_FOCUS_ASK, array(), $ret);
        }

        $focus->status = $status;
        $focus = $focus->save_and_return($focus);

        return $focus;
    }

    /**
     * 是否被该用户下载
     */
    public static function hasFocusedAsk($uid, $ask_id) {

        $mDownload = mFocus::findFirst(
            " uid = {$uid} ".
            " AND ask_id = {$ask_id} ".
            " AND status = " . mFocus::STATUS_NORMAL
        );

        return $mDownload?true: false;
    }

    /**
     * 获取关注列表
     */
    public function getFocusAsks($uids) {
        $focuses = self::find("uid={$uid} AND status=".self::STATUS_NORMAL);
        return $focuses;
    }

    public static function checkUserAskFocus( $target_id, $uid = 0){
        $builder = Focus::query_builder();
        $res = $builder ->where('uid='.$uid.' AND status='.Focus::STATUS_NORMAL.' AND ask_id='.$target_id)
                        ->columns('count(*) as c ')
                        ->getQuery()
                        ->execute();

        if( $res->toArray()[0]['c'] ){
            return true;
        }
        else{
            return false;
        }
    }

    //public static function has_focused_ask( $target_id, $uid = 0){
}

<?php

namespace App\Services;
use \App\Models\Focus as mFocus,
    \App\Models\Ask as mAsk;

class Focus extends ServiceBase
{

    /**
     * 添加新关注
     */
    public static function addNewFocus($uid, $ask_id, $status){
        $focus = new mFocus();
        //todo: actionlog
        $hasFocused = self::userHasFocusedAsk( $uid, $ask_id );
        if( $hasFocused ){
            $focusRecord = $focus->where( array('uid'=> $uid,  'ask_id'=> $ask_id ) )->first();
            $focusRecord->status = $status;
        }
        else{
            $focusRecord = $focus->assign(array(
                'uid'=>$uid,
                'ask_id'=>$ask_id,
                'status'=>$status
            ));
        }
        return $focusRecord->save();
    }

    public static function userHasFocusedAsk( $uid, $aid ){
        $focus = new mFocus();
        return $focus->where( array('uid'=>$uid, 'ask_id'=>$aid ) )->first();
    }

    public static function focusAsk($uid, $ask_id, $status) {
        $mAsk   = new mAsk;
        $mFocus = new mFocus;

        if( !$mAsk->get_ask_by_id($ask_id) )
            return error('ASK_NOT_EXIST');

        $focus = $mFocus->get_user_focus_ask($uid, $ask_id);

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
        $focus = $focus->save();

        return $focus;
    }

    /**
     * 是否被该用户下载
     */
    public static function hasFocusedAsk($uid, $ask_id) {
        $mDownload = (new mFocus)->has_focused_ask($uid, $ask_id);

        return $mDownload?true: false;
    }
    //public function getFocusAsks($uids) {
    //public static function checkUserAskFocus( $target_id, $uid = 0){
    //public static function has_focused_ask( $target_id, $uid = 0){
}

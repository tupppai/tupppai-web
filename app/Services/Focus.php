<?php

namespace App\Services;
use \App\Services\Ask as sAsk;
use \App\Services\ActionLog as sActionLog;
use \App\Models\Focus as mFocus,
    \App\Models\Ask as mAsk;

class Focus extends ServiceBase
{

    public static function getFocusByUid( $uid, $page, $size ){
        $mFocus = new mFocus();
        $focusAsks = $mFocus->get_user_focus_asks( $uid, $page, $size );

        $focusAsksList = array();
        foreach( $focusAsks as $ask ){
            $focusAsksList[] = sAsk::detail( $ask->ask );
        }

        return $focusAsksList;
    }


    /**
     * 添加新关注
     */
    public static function addNewFocus($uid, $ask_id, $status){
        $focus = new mFocus();
        sActionLog::init('ADD_NEW_FOCUS');
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
        $f =  $focusRecord->save();
        sActionLog::save( $f );
        return  $f;
    }

    public static function getFocusesByAskId($ask_id) {
        $mFocus  = new mFocus;
        $focuses = $mFocus->get_focuses_by_askid($ask_id);

        return $focuses;
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

        $cond = ['uid'=>$uid,'ask_id'=>$ask_id];
        $focus = $mFocus->firstOrNew($cond);

        $data = $cond;
        if( !$focus->id ){
            if( $status == mFocus::STATUS_DELETED ){
                return true;
            }
            $data['create_time'] = time();
        }
        $data['update_time'] = time();
        $data['status'] = $status;
        $focus->assign( $data )->save();

        if( $status == mFocus::STATUS_NORMAL ){
            sActionLog::save('FOCUS_ASK', $focus);
        }
        else {
            sActionLog::save('CANCEL_FOCUS_ASK', $focus);
        }
        return true;
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

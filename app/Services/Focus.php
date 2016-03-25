<?php

namespace App\Services;
use App\Services\Ask as sAsk;
use App\Services\ActionLog as sActionLog;

use App\Models\Focus as mFocus,
    App\Models\Ask as mAsk;

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

    public static function countFocusesByAskId($ask_id) {
        $mFocus = new mFocus;
        $num    = $mFocus->count_focuses_by_askid($ask_id);

        return $num;

    }

    public static function userHasFocusedAsk( $uid, $aid ){
        $focus = new mFocus();
        #sky 在model里面写一个get_xxx_byxxx
        return $focus->where( array('uid'=>$uid, 'ask_id'=>$aid ) )->first();
    }

    public static function focusAsk($uid, $ask_id, $status) {
        $mAsk   = new mAsk;
        $mFocus = new mFocus;

        $ask = sAsk::getAskById($ask_id);
        if( !$ask )
            return error('ASK_NOT_EXIST');

        $focus = $mFocus->get_user_focus_ask($uid, $ask_id);
        $data = array();

        if( !$focus && $status == mFocus::STATUS_DELETED ){
            return error('FOCUS_NOT_EXIST');
        }
        else if( !$focus ){
            $focus = new mFocus;
        }

        $data['uid']    = $uid;
        $data['ask_id'] = $ask_id;
        $data['status'] = $status;
        $focus->assign( $data )->save();

        if( $status == mFocus::STATUS_NORMAL ){
            sActionLog::save('FOCUS_ASK', $focus);
            cAskCounts::inc($ask->id, 'focus');
        }
        else {
            sActionLog::save('CANCEL_FOCUS_ASK', $focus);
            cAskCounts::inc($ask->id, 'focus', -1);
        }

        return $focus;
    }

    /**
     * 是否被该用户下载
     */
    public static function hasFocusedAsk($uid, $ask_id) {
        $mDownload = (new mFocus)->has_focused_ask($uid, $ask_id);

        return $mDownload?true: false;
    }
}

<?php

namespace Psgod\Services;

use \Psgod\Models\Follow as mFollow;

class Follow extends ServiceBase
{
    
    /**
     * 关注用户
     */
    public static function addNewFollow ($uid, $followWho, $status) {
        $user = sUser::getUserByUid($followWho);
        if( !$user ) {
            return error('USER_NOT_EXIST');
        }

        $follow = new mFollow;
        $follow->assign(array(
            'uid'=>$uid,
            'follow_who'=>$followWho,
            'status'=>$status
        ));
        $follow->save();

        sActionLog::log(sActionLog::TYPE_POST_ASK, array(), $follow);
        return $follow;
    }

    /**
     * 更新关系状态
     */
    public static function updateFollowStatus ( $follow, $status ){
        if ( !$follow ){
            return error('FOLLOW_NOT_EXIST');
        }

        $follow->status = $status;
        $ret = $follow->save();

        return $ret;
    }

    public static function getUserFansByUid ( $uid ) {
        return mFollow::find("follow_who=$uid");
    }

    public static function getUserFollowByUid ( $uid ) {
        return mFollow::find("udi=$uid");
    }

    public static function getUserFansCount ( $uid ) {
        return mFollow::count(array("follow_who = {$uid} AND status = ".mFollow::STATUS_NORMAL));
    }

    public static function getUserFollowCount ( $uid ) {
        return mFollow::count(array("uid = {$uid} AND status = ".mFollow::STATUS_NORMAL));
    }

}

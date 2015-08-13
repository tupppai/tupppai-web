<?php

namespace App\Services;

use App\Models\Follow as mFollow;

use Queue, App\Jobs\Push;

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

        sActionLog::init('TYPE_FOLLOW_USER', array());
        $follow = new mFollow;
        $follow->assign(array(
            'uid'=>$uid,
            'follow_who'=>$followWho,
            'status'=>$status
        ));
        $follow->save();

        #关注推送
        Queue::push(new Push(array(
            'uid'=>$followWho,
            'type'=>'follow'
        )));

        sActionLog::save($follow);
        return $follow;
    }

    /**
     * 更新关系状态
     */
    public static function updateFollowStatus ( $follow, $status ){
        if ( !$follow ){
            return error('FOLLOW_NOT_EXIST');
        }
        sActionLog::init('TYPE_UNFOLLOW_USER', array());

        $follow->status = $status;
        $ret = $follow->save();

        sActionLog::save($follow);
        return $ret;
    }

    public static function getUserFansByUid ( $follow_who ) {
        return (new mFollow)->get_user_fans($follow_who);
    }

    public static function getUserFollowByUid ( $uid ) {
        return (new mFollow)->get_user_followers($uid);
    }

    public static function getUserFansCount ( $uid ) {
        return (new mFollow)->count_user_fans($uid);
    }

    public static function getUserFollowCount ( $uid ) {
        return (new mFollow)->count_user_followers($uid);
    }
}

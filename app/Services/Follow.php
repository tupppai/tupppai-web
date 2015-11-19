<?php

namespace App\Services;

use App\Models\Follow as mFollow;
use App\Models\User as mUser;
use App\Services\ActionLog as sActionLog;

use Queue, App\Jobs\Push;

class Follow extends ServiceBase
{

    public static function follow( $me, $friendUid, $status ){
        $mUser = new mUser();
        $mFollow = new mFollow();

        $friend = $mUser->get_user_by_uid( $friendUid );
        if( !$friend ){
            return false;
        }
        
        $relation = $mFollow->update_friendship( $me, $friendUid, $status );

        if($status > mFollow::STATUS_DELETED) {
            #关注推送
            Queue::push(new Push(array(
                'uid'=>$me,
                'target_uid'=>$friendUid,
                'type'=>'follow'
            )));
        }
        
        return (bool)$relation;
    }

    public static function checkRelationshipBetween( $uid, $friendUid ){
        $mFollow = new mFollow();
        $relationship = $mFollow->get_friend_relation_of( $uid, $friendUid );

        if( $relationship && $relationship->status == mFollow::STATUS_NORMAL ){
            $isFriend = true;
        }
        else{
            $isFriend = false;
        }
        return $isFriend;
    }

    public static function getNewFollowers( $uid, $last_fetch_msg_time ){
        return (new mFollow)->where([
            'follow_who' => $uid,
            'status' => mFollow::STATUS_NORMAL
        ])
        ->where('update_time', '>', $last_fetch_msg_time )
        ->get();
    }

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
            'uid'=>$uid,
            'target_uid'=>$followWho,
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

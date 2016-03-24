<?php

namespace App\Services;

use App\Models\Follow as mFollow;
use App\Models\User as mUser;
use App\Services\ActionLog as sActionLog;

use App\Services\User as sUser;

use App\Counters\UserCounts as cUserCounts;
use App\Counters\UserFollows as cUserFollows;

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

        $val = ($status > mFollow::STATUS_DELETED)?1: -1;

        cUserFollows::inc($me, $val);
        cUserCounts::inc($friendUid, 'fans', $val);

        return (bool)$relation;
    }
    
    public static function blockUser( $uid, $target_uid, $status = mUser::STATUS_BLOCKED ) {
        $user = sUser::getUserByUid($target_uid) ;
        if( !$user ) {
            return error('USER_NOT_EXIST');
        }
        $relation = self::follow( $uid, $target_uid, $status);

        return $relation;
    }

    public static function checkIsBlocked($uid, $target_uid) {
        
        $mFollow = new mFollow();
        $relationship = $mFollow->get_friend_relation_of( $uid, $target_uid );

        if( $relationship && $relationship->status == mFollow::STATUS_BLOCKED ){
            return true;
        }
        
        return false;
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
        ->where('create_time', '>', $last_fetch_msg_time )
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
}

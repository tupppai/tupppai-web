<?php
namespace App\Services;

use \App\Models\ActionLog as mActionLog;
use \App\Models\UserScheduling as mUserScheduling,
    \App\Models\UserRole as mUserRole;

class UserScheduling extends ServiceBase
{

    /**
     * 检测用户是否为兼职账号,并且是否上班时间
     */
    public static function checkScheduling($user) {
        $mUserScheduling = new mUserScheduling;
        // 如果兼职登录，那么需要检查兼职的工作时间
        $role_str = $user['role_id'];
        $uid      = $user['uid'];

        $roles = explode(',', $role_str);

        if( !in_array( mUserRole::ROLE_STAFF, $roles ) ) {
            return true;
        }

        $mUserScheduling->get_scheduling_by_uid($uid);
        if(!$scheduling){
            return false;
        }

        return true;
    }

    public static function getBalance($uid) {
        $sum = mUserScheduling::sum(array(
            'column'    => "end_time-start_time",
            'conditions'=> "uid=".$uid." AND end_time < ".time(),
            'group'     => "status"

        ));
        $ret = array(0, 0);
        foreach($sum as $row) {
            $ret[$row->status] = $row->sumatory;
        }
        return $ret;
    }

    public static function pay_scores($uid, $time = null){
        if(!$time)  $time = time();
        $sql = "UPDATE user_schedulings set status = ".self::STATUS_PAID.
            " WHERE uid = $uid".
            " AND end_time < $time".
            " AND status = ".self::STATUS_NORMAL;
        // Base model
        $user_scheduling = new mUserScheduling();
        // Execute the query
        return new Resultset(null, $user_scheduling, $user_scheduling->getReadConnection()->query($sql));
    }

    public static function operTypes(){
        return array(
            'verify_count'=>array(
                mActionLog::TYPE_VERIFY_ASK,
                mActionLog::TYPE_VERIFY_REPLY,
                mActionLog::TYPE_REJECT_ASK,
                mActionLog::TYPE_REJECT_REPLY,
                mActionLog::TYPE_DELETE_ASK,
                mActionLog::TYPE_DELETE_REPLY
            ),
            'pass_count'=>array(
                mActionLog::TYPE_VERIFY_REPLY,
                mActionLog::TYPE_VERIFY_ASK
            ),
            'reject_count'=>array(
                mActionLog::TYPE_REJECT_REPLY,
                mActionLog::TYPE_REJECT_ASK
            ),
            'delete_count'=>array(
                mActionLog::TYPE_DELETE_ASK,
                mActionLog::TYPE_DELETE_REPLY
            ),
            'forbit_count'=>array(
                mActionLog::TYPE_FORBID_USER
            ),
            'delete_comment_count'=>array(
                mActionLog::TYPE_DELETE_COMMENT
            ),
            'post_ask'=>array(
                mActionLog::TYPE_POST_ASK
            ),
            'add_parttime'=>array(
                mActionLog::TYPE_ADD_PARTTIME
            ),
        );
    }
}

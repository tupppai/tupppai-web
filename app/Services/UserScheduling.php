<?php
namespace App\Services;

use App\Models\ActionLog as mActionLog,
    App\Models\UserScheduling as mUserScheduling,
    App\Models\UserRole as mUserRole;

use App\Services\ActionLog as sActionLog;

class UserScheduling extends ServiceBase
{

    /**
     * 检测用户是否为兼职账号,并且是否上班时间
     */
    public static function checkScheduling($user) {
        $mUserScheduling = new mUserScheduling;
        // 如果兼职登录，那么需要检查兼职的工作时间
        //$role_str = $user['role_id'];
        //$roles = explode(',', $role_str);
        $roles    = $user['role_id'];
        $uid      = $user['uid'];

        if( empty($roles) || !in_array( mUserRole::ROLE_STAFF, $roles ) ) {
            return true;
        }

        $mUserScheduling->get_scheduling_by_uid($uid);
        if(!$scheduling){
            return false;
        }

        return true;
    }

    public static function getBalance($uid) {
        return (new mUserScheduling)->get_balance($uid);
    }

    public static function pay_scores($uid, $time = null){
        if(!$time)  $time = time();
        //todo::eloquentize
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
                sActionLog::TYPE_VERIFY_ASK,
                sActionLog::TYPE_VERIFY_REPLY,
                sActionLog::TYPE_REJECT_ASK,
                sActionLog::TYPE_REJECT_REPLY,
                sActionLog::TYPE_DELETE_ASK,
                sActionLog::TYPE_DELETE_REPLY
            ),
            'pass_count'=>array(
                sActionLog::TYPE_VERIFY_REPLY,
                sActionLog::TYPE_VERIFY_ASK
            ),
            'reject_count'=>array(
                sActionLog::TYPE_REJECT_REPLY,
                sActionLog::TYPE_REJECT_ASK
            ),
            'delete_count'=>array(
                sActionLog::TYPE_DELETE_ASK,
                sActionLog::TYPE_DELETE_REPLY
            ),
            'forbit_count'=>array(
                sActionLog::TYPE_FORBID_USER
            ),
            'delete_comment_count'=>array(
                sActionLog::TYPE_DELETE_COMMENT
            ),
            'post_ask'=>array(
                sActionLog::TYPE_POST_ASK
            ),
            'add_parttime'=>array(
                sActionLog::TYPE_ADD_PARTTIME
            ),
            'create_user_count' => array(       
                 ActionLog::TYPE_REGISTER            
            )
        );
    }
}

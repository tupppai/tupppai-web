<?php
namespace App\Services;

use \App\Models\ActionLog as mActionLog;
use \App\Models\UserScheduling as mUserScheduling;

class UserScheduling extends ServiceBase
{

    public static function isWorking($uid){
        $time = time();
        return mUserScheduling::findFirst("uid=$uid AND end_time > $time AND start_time <= $time");
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

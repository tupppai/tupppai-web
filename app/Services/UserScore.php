<?php
namespace App\Services;
use \App\Models\UserScore as mUserScore,
    \App\Models\User as mUser;

class UserScore extends ServiceBase
{

    /**
     * 获取各种分数
     */
    public static function getBalance($uid) {
        return (new mUserScore)->get_balance($uid);
    }

    /**
     * 更新得分
     */
    public static function updateScore($uid, $type, $item_id, $data) {
        $mUserScore = new mUserScore;
        $mUser      = new mUser;

        $score = $mUserScore->get_user_score($type, $item_id, $uid);
        if(!$score) {
            $score = new mUserScore;
            $score->uid = $uid;
            $score->type= $type;
            $score->item_id = $item_id;
        }
        $score->content = '';
        $score->status  = 0;
        $score->score   = $data;
        $score->oper_by = _uid();
        $score->action_time = time();

        //update ps score
        $user = $mUser->get_user_by_uid($uid);
        $user->ps_score += floatval($data);
        $user->save();

        return $score->save();
    }

    /**
     * 更新审批结果
     */
    public static function updateContent($uid, $type, $item_id, $data) {
        $mUserScore = new mUserScore;

        $score = $mUserScore->get_user_score($type, $item_id, $uid);
        if(!$score) {
            $score = new self;
            $score->uid = $uid;
            $score->type= $type;
            $score->item_id = $item_id;
        }
        $score->content = is_null($data)? '': $data;
        $score->oper_by = _uid();
        $score->action_time = time();
        $score->status  = 0;
        $score->score   = 0;

        return $score->save();
    }

    /**
     * 获取作品得分
     */
    public static function getReplyScore($uid, $reply_id){
        $mUserScore = new mUserScore;

        $score = $mUserScore->get_user_score(mUserScore::TYPE_REPLY, $item_id, $uid);
        return $result;
    }

    public static function payScores($uid){
        
        $mUserScore = new mUserScore;

        return $mUserScore->pay_score($uid);
    }


    public static function getOperUserName($type, $item_id) {
        $mUserScore = new mUserScore;
        $usre_score = $mUserScore->get_user_score($type, $item_id);
        if(!$user_score)
            return '--';
        $user = mUser::findFirst($user_score->uid);
        return $user->nickname;
    }

    public static function countPassedReplies($uid) {
        return (new mUserScore)->count_passed_replies($uid);
    }

    public static function countRejectedReplies($uid) {
        return (new mUserScore)->count_rejected_replies($uid);
    }

    public static function sumOperUserScore($uid) {
        return (new mUserScore)->sum_scores_by_operuid($uid);
    }
    
    public static function avgOperUserScore($uid) {
        return (new mUserScore)->avg_scores_by_operuid($uid);
    }
 
    public static function avgUserScore($uid) {
        return (new mUserScore)->avg_scores_by_uid($uid);
    }
}

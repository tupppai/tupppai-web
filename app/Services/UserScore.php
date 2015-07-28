<?php
namespace Psgod\Services;
use \Psgod\Models\UserScore as mUserScore,
    \Psgod\Models\User as mUser;

class UserScore extends ServiceBase
{

    /**
     * 获取各种分数
     */
    public static function getBalance($uid) {
        $sum = mUserScore::sum(array(
            'column'    => "score",
            'conditions'=> "uid=".$uid,
            'group'     => "status"

        ));
        $ret = array(0, 0);
        foreach($sum as $row) {
            $ret[$row->status] = $row->sumatory;
        }
        return $ret;
    }

    /**
     * 更新得分
     */
    public static function updateScore($uid, $type, $item_id, $data) {
        $score = mUserScore::findFirst(array("uid = {$uid} AND type = {$type} AND item_id = {$item_id}"));
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

        //todo move out from model
        $user = User::findUserByUID($uid);
        $user->ps_score += floatval($data);
        $user->save();

        return $score->save_and_return($score);
    }
    
    /** 
     * 更新审批结果
     */
    public static function updateContent($uid, $type, $item_id, $data) {
        $score = mUserScore::findFirst(array("uid = {$uid} AND type = {$type} AND item_id = {$item_id}"));
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

        return $score->save_and_return($score);
    }
    
    /**
     * 获取作品得分
     */
    public static function getReplyScore($uid, $reply_id){
        $result = mUserScore::findFirst(array(
            "type = ".mUserScore::TYPE_REPLY.
            " AND uid = {$uid} ".
            " AND item_id = {$reply_id}"
        ));
    }

    public static function payScores($uid){
        $sql = "UPDATE user_scores set status = ".mUserScore::STATUS_PAID." WHERE uid = $uid AND status = ".mUserScore::STATUS_NORMAL;
        // Base model
        $user_score = new mUserScore();
        // Execute the query
        return $user_score->getReadConnection()->query($sql);
    }


    public static function getOperUserName($type, $item_id) {
        $mUserScore = new mUserScore;
        $user_score = mUserScore::findFirst("type={$type} AND item_id={$item_id}");
        if(!$user_score) 
            return '--';
        $user = mUser::findFirst($user_score->uid);
        return $user->nickname;
    }
}

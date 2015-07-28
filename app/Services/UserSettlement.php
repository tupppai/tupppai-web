<?php

namespace Psgod\Services;
use \Psgod\Models\UserSettlement as mUserSettlement;

class UserSettlement extends ServiceBase
{

    public static function getPaidMoney($uid) {
        return sprintf("%0.2f", mUserSettlement::sum(array(
             'column'=>'score',
             'conditions'=> "operate_to=$uid"
         )));
    }
    
    public static function staff_paid($uid, $operate_to, $pre_score, $paid_score, $rate = 1){
        $score = new mUserSettlement();
        $score->uid = $uid;
        $score->operate_to  = $operate_to;
        $score->operate_type= mUserSettlement::TYPE_PAID;
        $score->score_item  = 0;
        $score->score       = $paid_score*$rate;
        $score->data        = "$pre_score|$paid_score|$rate";
        $time = time();
        $score->create_time = $time;
        $score->update_time = $time;

        UserScheduling::pay_scores($operate_to, $time);
        return $score->save_and_return($score);
    }

    public static function paid($uid, $operate_to, $pre_score, $paid_score){
        $score = new mUserSettlement();
        $score->uid = $uid;
        $score->operate_to  = $operate_to;
        $score->operate_type= mUserSettlement::TYPE_PAID;
        $score->score_item  = 0;
        $score->score       = $paid_score;
        $score->data        = $pre_score."|".$paid_score;
        $score->create_time = time();
        $score->update_time = time();

        UserScore::pay_scores($operate_to);
        return $score->save_and_return($score);
    }

    public static function paid_list($operate_to, $page, $limit){
        $builder    = mUserSettlement::query_builder();
        $user       = 'Psgod\Models\User';
        $builder->join($user, "ur.uid = uid", "ur", 'RIGHT')
                ->where("ur.operate_to = {$operate_to} AND ur.type= ".mUserSettlement::TYPE_PAID);
        return mUserSettlement::query_page($builder, $page, $limit);
    }
}

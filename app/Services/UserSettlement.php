<?php

namespace App\Services;
use App\Models\UserSettlement as mUserSettlement;

use App\Services\UserScheduling as sUserScheduling,
    App\Services\UserScore as sUserScore;

class UserSettlement extends ServiceBase
{

    public static function staff_paid($uid, $operate_to, $pre_score, $paid_score, $rate = 1){
        $score = new mUserSettlement();
        $score->uid = $uid;
        $score->operate_to  = $operate_to;
        $score->operate_type= mUserSettlement::TYPE_PAID;
        $score->score_item  = 0;
        $score->score       = $paid_score*$rate;
        $score->data        = "$pre_score|$paid_score|$rate";

        sUserScheduling::pay_scores($operate_to, $time);
        return $score->save();
    }

    public static function paid($uid, $operate_to, $pre_score, $paid_score){
        $score = new mUserSettlement();
        $score->uid = $uid;
        $score->operate_to  = $operate_to;
        $score->operate_type= mUserSettlement::TYPE_PAID;
        $score->score_item  = 0;
        $score->score       = $paid_score;
        $score->data        = $pre_score."|".$paid_score;

        sUserScore::pay_scores($operate_to);
        return $score->save();
    }

    public static function sumTotalScore($operate_to=null) {
        return (new mUserSettlement)->sum_total_score($operate_to);
    }


    public static function paid_list($operate_to, $page, $limit){
        $builder    = mUserSettlement::query_builder();
        $user       = 'App\Models\User';
        $builder->join($user, "ur.uid = uid", "ur", 'RIGHT')
                ->where("ur.operate_to = {$operate_to} AND ur.type= ".mUserSettlement::TYPE_PAID);
        return mUserSettlement::query_page($builder, $page, $limit);
    }

    public static function getPaidMoney($uid) {
        return sprintf("%0.2f", mUserSettlement::sum(array(
             'column'=>'score',
             'conditions'=> "operate_to=$uid"
         )));
    }
}

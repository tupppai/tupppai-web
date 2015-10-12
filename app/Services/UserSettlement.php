<?php

namespace App\Services;
use App\Models\UserSettlement as mUserSettlement,
    App\Models\User as mUser,
    App\Models\Usermeta as mUsermeta,
    App\Models\UserScheduling as mUserScheduling,
    App\Models\UserScore as mUserScore;

use App\Services\UserScheduling as sUserScheduling,
    App\Services\ActionLog as sActionLog,
    App\Services\Usermeta as sUsermeta,
    App\Services\Config as sConfig,
    App\Services\UserScore as sUserScore;

class UserSettlement extends ServiceBase
{
    public static function parttimePaid( $uid, $operator_uid ){
        $mUser = new mUser();
        $user = $mUser->get_user_by_uid($uid);
        if(!$user) {
            return error( 'USER_NOT_EXIST', '用户不存在');
        }

        $balance = sUserScore::getBalance($uid);
        $current_score = $balance[mUserScore::STATUS_NORMAL];
        $paid_score    = $balance[mUserScore::STATUS_PAID];

        if( $current_score <= 0 ) {
            return error( 'NOTHING_TO_BE_PAID', '当前未结算资金为0');
        }

        $mUserSettlement = new mUserSettlement();
        sActionLog::init( 'PARTTIME_PAID' );
        $res = self::paid( $operator_uid, $uid, $paid_score, $current_score );
        sActionLog::save( $res );

    }

    public static function payStaff( $uid, $operator_uid ){
        $mUser = new mUser();
        $user = $mUser->get_user_by_uid($uid);
        if(!$user) {
            return error( 'USER_NOT_EXIST', '用户不存在');
        }

        $deafult_rate =sConfig::getConfig(mUsermeta::KEY_STAFF_TIME_PRICE_RATE);
        $rate = sUsermeta::get( $uid, mUsermeta::KEY_STAFF_TIME_PRICE_RATE, $deafult_rate );

        $balance = sUserScore::getBalance( $uid );
        $current_score = $balance[mUserScheduling::STATUS_NORMAL] * $rate;
        $paid_score    = $balance[mUserScheduling::STATUS_PAID] * $rate;

        if( $current_score <= 0 ) {
            return error( 'NOTHING_TO_BE_PAID', '当前未结算资金为0' );
        }
        sActionLog::init( 'STAFF_PAID' );
        $res = self::staffPaid($this->_uid, $uid, $paid_score, $current_score, $rate);
        sActionLog::log( $res );

        return $res;
    }


    public static function staffPaid($uid, $operate_to, $pre_score, $paid_score, $rate = 1){
        $mUserSettlement = new mUserSettlement();
        $uid = $uid;
        $operate_to  = $operate_to;
        $operate_type= mUserSettlement::TYPE_PAID;
        $score_item  = 0;
        $score       = $paid_score*$rate;
        $data        = "$pre_score|$paid_score|$rate";

        $us = $mUserSettlement->pay( $uid, $operate_to, $operate_type, $score_item, $score, $data );
        sUserScheduling::pay_scores($operate_to, $time);
        return $us;
    }

    public static function paid($uid, $operate_to, $pre_score, $paid_score){
        $mUserSettlement = new mUserSettlement();
        $uid = $uid;
        $operate_to  = $operate_to;
        $operate_type= mUserSettlement::TYPE_PAID;
        $score_item  = 0;
        $score       = $paid_score;
        $data        = $pre_score."|".$paid_score;

        $us = $mUserSettlement->pay( $uid, $operate_to, $operate_type, $score_item, $score, $data );
        sUserScore::payScores($operate_to);
        return $us;
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

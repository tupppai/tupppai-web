<?php namespace App\Services;

use App\Models\Reward as mReward;
use App\Trades\User as tUser;
use App\Services\Ask as sAsk;
use Illuminate\Support\Facades\DB;
use Log;

class Reward extends ServiceBase
{
    public static function updateStatus($reward_id, $status = mReward::STATUS_NORMAL) {
        return (new mReward)->update_status($reward_id, $status);
    }

    /**
     * 直接通过支付的打赏
     */
    public static function moneyRewardTarget( $uid, $target_type, $target_id, $amount, $status = mReward::STATUS_NORMAL ){
        //记录打赏
        $reward = (new mReward)->create_reward($uid, $target_type, $target_id, $amount, $status);

        return $reward;
    }
    public static function moneyRewardAsk($uid, $ask_id, $amount, $status = mReward::STATUS_NORMAL)
    {
        return self::moneyRewardTarget( $uid, mReward::TYPE_ASK, $amount, $status);
    }
    public static function moneyRewardAsk($uid, $ask_id, $amount, $status = mReward::STATUS_NORMAL)
    {
        return self::moneyRewardTarget( $uid, mReward::TYPE_ASK, $amount, $status);
    }

    public static function createReward($send_uid, $target_type, $target_id, $amount, $reason, $status = mReward::STATUS_NORMAL)
    {
        $reward = null;
        try {
            if( $target_type == mReward::TYPE_ASK){
                //获取打赏(求P)
                $target = sAsk::getAskById($target_id);
            }
            else if ( $target_type == mReward::TYPE_REPLY ){
                $target = sReply::getReplyById($target_id);
            }

            $recv_uid = $target->uid;

            if (!tUser::checkUserBalance($uid, $amount)) {

                return false;
            }

            DB::connection('db_trade')->transaction(function () use ($recv_uid, $amount, $send_uid, $target_id, $reason, $status) {
                if (!tUser::checkUserBalance($send_uid, $amount)) {

                    return false;
                }
                //记录打赏
                $reward = (new mReward)->create_reward($send_uid, $target_type, $target_id, $amount, $status);
                //支付
                tUser::pay($send_uid, $recv_uid, $amount, $reason);

            });
        }catch(\Exception $e){
            return error('REWARD_EXIST');
        }
        return $reward;
    }

    public static function createRewardAsk($uid, $ask_id, $amount, $reason, $status = mReward::STATUS_NORMAL)
    {
        return self::createReward( $uid, mReward::TYPE_ASK, $ask_id, $amount, $reason, $status );
    }

    public static function createRewardReply($uid, $reply_id, $amount, $reason, $status = mReward::STATUS_NORMAL)
    {
        return self::createReward( $uid, mReward::TYPE_REPLY, $reply_id, $amount, $reason, $status );
    }

    /*
     *  获取用户随机打赏次数
     *  return  int
     * */
    public static function getUserRewardAskCount($uid, $ask_id)
    {
        return (new mReward)->count_user_ask_reward_id($uid, $ask_id);
    }

    /*
    * 获取ask打赏次数
    */
    public static function getAskRewardCount( $ask_id )
    {
        return (new mReward)->count_ask_reward_by_id( $ask_id );
    }
}

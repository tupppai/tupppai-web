<?php namespace App\Services;

use App\Models\Reward as mReward;
use App\Trades\User as tUser;
use App\Services\Ask as sAsk;
use Illuminate\Support\Facades\DB;
use Log;

class Reward extends ServiceBase
{
    const STATUS_FAILED = -1;
    const STATUS_NORMAL = 1;

    public static function createReward($uid, $ask_id, $amount, $status = mReward::STATUS_NORMAL)
    {
        try {
            //获取打赏(求P)
            $ask = sAsk::getAskById($ask_id);
            $ask_uid = $ask->uid;

            if (!tUser::checkUserBalance($uid, $amount)) {

                return false;
            }

            DB::connection('db_trade')->transaction(function () use ($ask_uid, $amount, $uid, $ask_id, $status) {
                if (!tUser::checkUserBalance($uid, $amount)) {

                    return false;
                }
                //记录打赏
                $reward = new mReward;
                $reward->uid = $uid;
                $reward->askid = $ask_id;
                $reward->amount = $amount;
                $reward->status = $status;
                $reward->save();

                //支付
                tUser::pay($uid, $ask_uid, $amount);
            });
        }catch(\Exception $e){
            error('REWARD_EXIST');
        }
        return true;
    }
    /*
     *  获取用户随机打赏次数
     *  return  int
     * */
    public static function getUserRewardCount($uid, $ask_id)
    {
        return mReward::get_user_reward_count($uid, $ask_id);
    }
    /*
    * 获取ask打赏次数
    */
    public static function getAskRewardCount( $ask_id )
    {
        return mReward::get_ask_reward_count( $ask_id );
    }
}

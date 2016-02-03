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
    public static function moneyReward($uid, $ask_id, $amount, $status = mReward::STATUS_NORMAL)
    {
        //记录打赏
        $reward = (new mReward)->create_reward($uid, $ask_id, $amount, $status);

        return $reward;
    }

    public static function createReward($uid, $ask_id, $amount, $status = mReward::STATUS_NORMAL)
    {
        $reward = null;
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
                $reward = (new Reward)->create_reward($uid, $ask_id, $amount, $status);
                //支付
                tUser::pay($uid, $ask_uid, $amount, '打赏');

            });
        }catch(\Exception $e){
            return error('REWARD_EXIST');
        }
        return $reward;
    }

    /*
     *  获取用户随机打赏次数
     *  return  int
     * */
    public static function getUserRewardCount($uid, $ask_id)
    {
        return (new mReward)->count_user_reward($uid, $ask_id);
    }

    /*
    * 获取ask打赏次数
    */
    public static function getAskRewardCount( $ask_id )
    {
        return (new mReward)->count_ask_reward( $ask_id );
    }
}

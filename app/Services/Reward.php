<?php namespace App\Services;

use App\Models\Reward as mReward;
use App\Trades\User as tUser;
use App\Services\Ask as sAsk;
use Illuminate\Support\Facades\DB;
use Log;

class Reward extends ServiceBase
{
    const STATUS_FAILED = 1;
    const STATUS_NORMAL = 2;
    const STATUS_EXIST  = 3;

    public static function create_reward($uid, $ask_id, $amount)
    {
        try {
            //获取打赏(求P)
            $ask = sAsk::getAskById($ask_id);
            $ask_uid = $ask->uid;

            if (!tUser::checkUserBalance($uid, $amount)) {

                return false;
            }

            DB::connection('db_trade')->transaction(function () use ($ask_uid, $amount, $uid, $ask_id) {
                if (!tUser::checkUserBalance($uid, $amount)) {

                    return false;
                }
                //记录打赏
                $reward = new mReward;
                $reward->uid = $uid;
                $reward->askid = $ask_id;
                $reward->amount = $amount;
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
     *  获取随机打赏金额
     *  return  int or boole
     * */
    public static function get_reward_amount($uid, $ask_id)
    {
        $reward = mReward::where('uid', $uid)->where('askid', $ask_id)->first();

        if (empty($reward)) {
            return false;
        }

        $mount = $reward->amount;

        return $mount;
    }
}
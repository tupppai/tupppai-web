<?php namespace App\Services;

use App\Models\Reward as mReward;
use App\Trades\User as tUser;
use App\Services\Ask as sAsk;
use App\Services\User as sUser;
use App\Services\Reply as sReply;
use Illuminate\Support\Facades\DB;
use Log;
use App\Counters\AskCounts as cAskCounts;
use App\Counters\ReplyCounts as cReplyCounts;


class Reward extends ServiceBase
{
    public static function updateStatus($reward_id, $status = mReward::STATUS_NORMAL) {
        $reward = (new mReward)->update_status($reward_id, $status);
        $val = 1;
        if( $reward->status <= mReward::STATUS_DELETED ){
            $val = -1;
        }
        if( $reward->target_type == mReward::TYPE_ASK ){
            cAskCounts::inc($reward->target_id, 'reward', $val);
        }
        else if( $target_type == mReward::TYPE_REPLY ){
            cReplyCounts::inc($reward->target_id, 'reward', $val);
        }
        return $reward;
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
        return self::moneyRewardTarget( $uid, mReward::TYPE_ASK, $ask_id, $amount, $status);
    }
    public static function moneyRewardReply($uid, $reply_id, $amount, $status = mReward::STATUS_NORMAL)
    {
        return self::moneyRewardTarget( $uid, mReward::TYPE_REPLY, $reply_id, $amount, $status);
    }

    public static function createReward($send_uid, $target_type, $target_id, $amount, $reason, $status = mReward::STATUS_NORMAL)
    {
        $reward = null;
        // try {
            if( $target_type == mReward::TYPE_ASK){
                //获取打赏(求P)
                $target = sAsk::getAskById($target_id);
                if( !$target ){
                    return error('ASK_NOT_EXIST');
                }
            }
            else if ( $target_type == mReward::TYPE_REPLY ){
                $target = sReply::getReplyById($target_id);

                if( !$target ){
                    return error('REPLY_NOT_EXIST');
                }
            }

            $recv_uid = $target->uid;
            DB::connection('db_trade')->transaction(function () use (&$reward, $recv_uid, $amount, $send_uid, $target_type, $target_id, $reason, $status) {
                if (!tUser::checkUserBalance($send_uid, $amount)) {
                    return false;
                }
                //记录打赏
                $reward = (new mReward)->create_reward($send_uid, $target_type, $target_id, $amount, $status);
                if( $status > mReward::STATUS_DELETED ){
                    if( $target_type == mReward::TYPE_ASK ){
                        cAskCounts::inc($target_id, 'reward');
                    }
                    else if( $target_type == mReward::TYPE_REPLY ){
                        cReplyCounts::inc($target_id, 'reward');
                    }
                }
                // //支付
                // tUser::pay($send_uid, $recv_uid, $amount, $reason);
            });
        // }catch(\Exception $e){
        //     return error('REWARD_EXIST', $e->getMessage());
        // }
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
    * 获取ask打赏次数(uid重复)
    */
    public static function getAskRewardCount( $ask_id )
    {
        return (new mReward)->count_ask_reward_by_id( $ask_id );
    }

    public static function getReplyRewardCount( $reply_id ){
        return (new mReward)->count_reply_reward_by_id( $reply_id );
    }

    public static function countRewardUserAmountByTarget( $target_type, $target_id ){
        return (new mReward)->count_reward_user_amount_by_target( $target_type, $target_id );
    }

    public static function countRewardAskUserAmount( $ask_id ){
        return self::countRewardUserAmountByTarget( mReward::TYPE_ASK, $ask_id );
    }

    public static function countRewardReplyUserAmount( $reply_id ){
        return self::countRewardUserAmountByTarget( mReward::TYPE_REPLY, $reply_id );
    }

    public static function countUserRewardByTarget( $uid, $target_type, $target_id ){
        return (new mReward)->count_user_reward_by_target( $uid, $target_type, $target_id );
    }
    public static function checkUserHasRewardTarget( $uid, $target_type, $target_id ){
        return (bool)self::countUserRewardByTarget( $uid, $target_type, $target_id );
    }

    public static function checkUserHasRewardAsk( $uid, $target_id ){
        return self::checkUserHasRewardTarget( $uid, mReward::TYPE_ASK, $target_id );
    }

    public static function checkUserHasRewardReply( $uid, $target_id ){
        return self::checkUserHasRewardTarget( $uid, mReward::TYPE_REPLY, $target_id );
    }

    public static function getRewardUserAvatarsByTarget( $target_type, $target_id, $page = 1, $size = 5 ){
        //get users
        $uids = (new mReward)->get_users_by_target( $target_type, $target_id, $page, $size );
        //get avatars
        $users = sUser::getUserByUids( $uids );
        $avatars = array_column( $users, 'avatar');
        return $avatars;
    }
}

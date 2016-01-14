<?php namespace App\Handles\Trade;

use App\Events\Event;
use App\Models\Ask as mAsk;
use App\Services\User as sUser;
use App\Trades\Account as tAccount;
use App\Trades\User as tUser;
use Illuminate\Support\Facades\DB;


class Trade
{
    public function __construct()
    {

    }

    public function handle(Event $handle)
    {
        //This is Logic
        return 2;
    }

    /*
    * 恢复求P状态为常态
    */
    public function setAskStatus($ask)
    {
        //操作psgod库
        //DB::transaction(function() use($ask){
        if (sUser::isBlocked($ask->uid)) {
            /*屏蔽用户*/
            $ask->status = mAsk::STATUS_BLOCKED;
        } else {
            /*正常用户*/
            $ask->status = mAsk::STATUS_NORMAL;
        }
        $ask->save();
        //});
    }

    /*
     * 获取商品金额
     */
    public function getGoodsAmount($product)
    {
        return 0.5;
    }

    /*
     * 计算用户购买商品后余额
     */
    public static function getUserGoodsBalance($uid, $amount)
    {
        //设置余额
        $userGoodsBalance = tUser::getBalance($uid);
        $userGoodsBalance = ($userGoodsBalance - $amount);
        return $userGoodsBalance;
    }

    /*
     * 计算用户购买商品后冻结金额
     */
    public static function getUserGoodsFreezing($uid, $amount)
    {
        $userGoodsFreezing = tUser::getFreezing($uid);
        $userGoodsFreezing = ($userGoodsFreezing + $amount);
        return $userGoodsFreezing;
    }

    /*
     * 冻结金额
     */
    public function freeze($uid, $amount)
    {
        $userGoodsBalance = self::getUserGoodsBalance($uid, $amount);
        tUser::setBalance($uid, $userGoodsBalance);
        //设置冻结
        $userGoodsFreezing = self::getUserGoodsFreezing($uid, $amount);
        tUser::setFreezing($uid, $userGoodsFreezing);
    }

    /*
     * 检查扣除商品费用后,用户余额是否充足
     * */
    public function checkUserBalance($uid, $amount)
    {
        $balance = tUser::getBalance($uid);
        $balance = ($balance - $amount);
        if (0 > $balance) {
            return false;
        }
        return true;
    }

    /*
     * 用户资产流水 - 冻结
     */
    public function freezeAccount($uid, $amount, $balance, $status, $memo = '成功')
    {
        $tAccount = new tAccount($uid);
        $tAccount->setBalance($balance)
            ->setType(tAccount::ACCOUNT_OPERATE_TYPE_FREEZE)
            ->setMemo($memo)
            ->setStatus($status)
            ->setAmount($amount)
            ->save();
        return $tAccount;
    }

}

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
        DB::transaction(function() use($ask){
            if( sUser::isBlocked( $ask->uid ) ){
                /*屏蔽用户*/
                $ask->status = mAsk::STATUS_BLOCKED;
            }else{
                /*正常用户*/
                $ask->status = mAsk::STATUS_NORMAL;
            }
            $ask->save();
        });
    }
    /*
     * 获取商品金额
     */
    public function getGoodsAmount($product)
    {
        return 0.5;
    }
    /*
     * 获取用户余额
     */
    public static function getBalance($uid)
    {
        return tUser::getBalance($uid);
    }
    /*
     * 冻结金额
     */
    public function freeze($uid,$amount)
    {
        //设置余额
        $userBalance = tUser::getBalance($uid);
        $userBalance = ($userBalance-$amount);
        tUser::setBalance($uid,$userBalance);
        //设置冻结
        $userFreezing = tUser::getFreezing($uid);
        $userFreezing = ($userFreezing+$amount);
        tUser::setFreezing($uid,$userFreezing);
    }
    /*
     * 检查扣除商品费用后,用户余额是否充足
     * */
    public function checkUserBalance($uid,$amount)
    {
        $balance = self::getBalance($uid);
        $balance = ($balance - $amount);
        if(0 > $balance){
            return false;
        }
        return true;
    }
    /*
     * 用户资产流水 - 冻结
     */
    public function freezeAccount($uid,$amount,$status,$memo = '成功')
    {
        //获取用户余额
        $balance = self::getBalance($uid);

        //计算用户余额
        $balance = ($balance-$amount);
        $balance = (double)$balance;

        $tAccount = new tAccount($uid);
        $tAccount->setBalance($balance)
            ->setType(tAccount::ACCOUNT_TYPE_FREEZE)
            ->setMemo($memo)
            ->setStatus($status)
            ->setAmount($amount)
            ->save();
        return $tAccount;
    }
}

<?php namespace App\Handles\Trade;

use App\Events\Event;

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
        tUser::setBalance($uid,$amount);
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

        $tAccount = new tAccount($uid);
        $tAccount->setBalance($balance);
        $tAccount->setType(tAccount::ACCOUNT_TYPE_FREEZE);
        $tAccount->setMemo($memo);
        $tAccount->setStstus($status);
        $tAccount->setAmount($amount);
        $tAccount->save();
        return $tAccount;
    }
}

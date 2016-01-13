<?php

namespace App\Handles\Trade;

use App\Models\Ask as mAsk;
use App\Services\User as sUser;
use App\Trades\User as tUser;

class AsksSaveHandle
{
    public function handle(Event $event)
    {
        $ask = $event->arguments['ask'];

        //获取商品金额
        $amount = $this->getGoodsAmount(1);

        //检查余额是否充足
        $this->checkUserBalance($ask->uid,$amount);

        //写流水交易失败,余额不足

        //Todo 这里抛出异常or return false

        //操作psgod_trade库
        DB::reconnect('psgod_trade')->transaction(function() use($ask,$amount){
            //冻结(求P用户)金额
            $this->freeze($ask->uid,$amount);
            //写流水
            $this->transaction($ask->uid,$amount);
            //恢复求P状态为常态
            $this->setAskStatus($ask);
        });



    }
    /*恢复求P状态为常态*/
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
     * 获取订单金额
     * */
    public function getGoodsAmount($product)
    {
        return 0.5;
    }
    /*
     * 冻结金额
     * */
    public function freeze($uid,$amount)
    {
        tUser::setBalance($uid);
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
     * 冻结流水
     * */
    public function freezeTransaction($uid,$amount)
    {
        //获取用户余额
        $balance = self::getBalance($uid);
        //获取真实与俄
        $balance = ($balance-$amount);
        $tAccount->setBalance($balance);
        $tAccount->setFreezeAmount($amount);
        $tAccount->save();
    }
    /*
     * 获取用户余额
     * */
    public static function getBalance($uid)
    {
        return tUser::getBalance($uid);
    }
}
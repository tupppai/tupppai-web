<?php

namespace App\Handles\Trade;

use App\Events\Event;
use App\Models\Ask as mAsk;
use App\Services\Ask as sAsk;
use App\Services\User as sUser;
use App\Trades\Account as tAccount;
use App\Trades\User as tUser;
use Illuminate\Support\Facades\DB;

class AsksSaveHandle extends Trade
{
    public function handle(Event $event)
    {
        $ask    = $event->arguments['ask'];


        //获取商品金额
        $amount = $this->getGoodsAmount(1);

        //检查扣除商品费用后,用户余额是否充足
        $checkUserBalance = tUser::checkBalance($ask->uid, $amount);
        if(!$checkUserBalance) {
            //写流水交易失败,余额不足
            tAccount::freezeAccount($ask->uid, $amount, tUser::getBalance($ask->uid), tAccount::STATUS_ACCOUNT_FAIL, '余额不足');
            return error('TRADE_USER_BALANCE_ERROR');
        }

        //操作psgod_trade库
        DB::connection('db_trade')->transaction(function() use($ask, $amount){

            //检查扣除商品费用后,用户余额是否充足 多次检查 防止并发
            $checkUserBalance = tUser::checkBalance($ask->uid, $amount);
            if(!$checkUserBalance) {
                //写流水交易失败,余额不足
                return error('TRADE_USER_BALANCE_ERROR');
            }

            //冻结(求P用户)金额
            tUser::freezeBalance($ask->uid, $amount);
            //写冻结流水
            $balance = tUser::getBalance($ask->uid);
            tAccount::freezeAccount($ask->uid, $amount, $balance, tAccount::STATUS_ACCOUNT_SUCCEED);
            //恢复求P状态为常态
            sAsk::setTradeAskStatus($ask);

        });
    }
}

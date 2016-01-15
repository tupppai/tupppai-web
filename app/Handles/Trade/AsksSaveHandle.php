<?php

namespace App\Handles\Trade;

use App\Events\Event;
use App\Jobs\CheckAskForReply;
use App\Services\Ask as sAsk;
use App\Services\Product as sProduct;
use App\Trades\Account as tAccount;
use App\Trades\User as tUser;
use Carbon\Carbon;
use Queue;
use Illuminate\Support\Facades\DB;

class AsksSaveHandle extends Trade
{
    public function handle(Event $event)
    {
        try {
            $ask = $event->arguments['ask'];


            //获取商品金额
            $amount = sProduct::getProductById(1);
            $amount = $amount['price'];

            //检查扣除商品费用后,用户余额是否充足
            $checkUserBalance = tUser::checkBalance($ask->uid, $amount);
            if (!$checkUserBalance) {
                //写流水交易失败,余额不足
                tAccount::freezeAccount($ask->uid, $amount, tUser::getBalance($ask->uid), tAccount::STATUS_ACCOUNT_FAIL, '余额不足');
                return error('TRADE_USER_BALANCE_ERROR');
            }

            //操作psgod_trade库
            DB::connection('db_trade')->transaction(function () use ($ask, $amount) {

                //检查扣除商品费用后,用户余额是否充足 多次检查 防止并发
                $checkUserBalance = tUser::checkBalance($ask->uid, $amount);
                if (!$checkUserBalance) {
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
            //设置延迟3天检查解冻
            $laterSevenPay = Carbon::now()->addDays(3);
            Queue::later($laterSevenPay, new CheckAskForReply($ask->id, $ask->uid));
        } catch (\Exception $e) {
            Log::error('ReplySaveHandle', $e);
        }
    }
}

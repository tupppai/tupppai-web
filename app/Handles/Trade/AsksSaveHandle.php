<?php

namespace App\Handles\Trade;

use App\Events\Event;
use App\Jobs\CheckAskHasReply;
use App\Services\Ask as sAsk;
use App\Services\Product as sProduct;
use App\Trades\Account as tAccount;
use App\Trades\User as tUser;
use Carbon\Carbon;
use Queue;
use Illuminate\Support\Facades\DB;
use Log;

class AsksSaveHandle extends Trade
{
    public function handle(Event $event)
    {
        try {
            $ask = $event->arguments['ask'];


            //获取商品金额
            $amount = sProduct::getProductById(1);
            $amount = $amount['price'];
            //保存价格到ask amount 字段
            $ask->amount = $amount;
            $ask->save();

            //检查扣除商品费用后,用户余额是否充足
            $checkUserBalance = tUser::checkBalance($ask->uid, $amount);
            if (!$checkUserBalance) {
                //写流水交易失败,余额不足
                tAccount::writeAccount($ask->uid, $amount, tUser::getBalance($ask->uid), tAccount::STATUS_ACCOUNT_FAIL, tAccount::TYPE_ACCOUNT_FREEZE, '冻结失败,余额不足');
                return error('TRADE_USER_BALANCE_ERROR', '交易失败，余额不足');
            }

            //操作psgod_trade库
            DB::connection('db_trade')->transaction(function () use ($ask, $amount) {

                //检查扣除商品费用后,用户余额是否充足 多次检查 防止并发
                $checkUserBalance = tUser::checkBalance($ask->uid, $amount);
                if (!$checkUserBalance) {
                    //写流水交易失败,余额不足
                    return error('TRADE_USER_BALANCE_ERROR', '交易失败，余额不足');
                }
                
                //冻结(求P用户)金额
                tUser::freezeBalance($ask->uid, $amount);
                //写冻结流水
                $balance = tUser::getBalance($ask->uid);
                tAccount::writeAccount($ask->uid, $amount, $balance, tAccount::STATUS_ACCOUNT_SUCCEED, tAccount::TYPE_ACCOUNT_FREEZE, '冻结成功');
                //恢复求P状态为常态
                sAsk::setTradeAskStatus($ask);

            });
            //设置延迟3天检查解冻
            //Queue::later(Carbon::now()->addDays(3), new CheckAskHasReply($ask->id, $amount));
            Queue::later(Carbon::now()->addMinutes(3), new CheckAskHasReply($ask->id, $amount));
        } catch (\Exception $e) {
            Log::error('AsksSaveHandle', array($e->getLine().'------'.$e->getMessage()));
        }
    }
}

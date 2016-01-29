<?php

namespace App\Handles\Trade;

use App\Events\Event;
use App\Jobs\CheckAskHasReply;
use App\Services\Ask as sAsk;
use App\Models\Ask as mAsk;
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
            $ask    = $event->arguments['ask'];

            // 获取用户余额
            $balance= tUser::getBalance($ask->uid); 

            // 获取商品金额
            $amount = sProduct::getProductById(1);
            $amount = $amount['price'];

            // 保存价格到ask amount 字段
            $ask->amount = $amount;
            if ($balance < $amount) {
                //$ask->status = mAsk::STATUS_FROZEN;
            }
            $ask->save();

            //操作psgod_trade库
            DB::connection('db_trade')->transaction(function () use ($ask, $amount) {
                tUser::reduceBalance($ask->uid, $amount, '求p扣款');
            });

            //设置延迟3天检查解冻
            //Queue::later(Carbon::now()->addDays(3), new CheckAskHasReply($ask->id, $amount, $ask->uid));
            Queue::later(Carbon::now()->addMinutes(3), new CheckAskHasReply($ask->id, $amount, $ask->uid));

        } catch (\Exception $e) {
            Log::error('AsksSaveHandle', array($e->getLine() . '------' . $e->getMessage()));
        }
    }
}

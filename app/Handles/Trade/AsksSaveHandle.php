<?php

namespace App\Handles\Trade;

use App\Events\Event;
use App\Models\Ask as mAsk;
use App\Services\User as sUser;
use App\Trades\Account as tAccount;
use App\Trades\User as tUser;
use Illuminate\Support\Facades\DB;

class AsksSaveHandle extends Trade
{
    public function handle(Event $event)
    {
        $ask = $event->arguments['ask'];

        //获取商品金额
        $amount = $this->getGoodsAmount(1);

        //检查扣除商品费用后,用户余额是否充足
        $checkUserBalance = $this->checkUserBalance($ask->uid,$amount);
        if(!$checkUserBalance) {
            //写流水交易失败,余额不足
            $this->freezeAccount($ask->uid, $amount, tAccount::ACCOUNT_FAIL_STATUS, '余额不足');
            return error('');
        }

        //操作psgod_trade库
        DB::connection('db_trade')->transaction(function() use($ask,$amount){
            //冻结(求P用户)金额
            $this->freeze($ask->uid,$amount);
            //写冻结流水
            $this->freezeAccount($ask->uid, $amount, tAccount::ACCOUNT_SUCCEED_STATUS);
            //恢复求P状态为常态
            $this->setAskStatus($ask);
        });



    }
}
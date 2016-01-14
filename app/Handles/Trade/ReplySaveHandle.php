<?php


namespace App\Handles\Trade;


use App\Events\Event;
use App\Services\Ask as sAsk;
use App\Services\ThreadCategory as sThreadCategory;
use App\Trades\Order as tOrder;

class ReplySaveHandle
{
    public function handle(Event $event)
    {
        $reply = $event->arguments['reply'];

       // dd($reply);
        //求助ID
        $askId = $reply->ask_id;
        //获取ask
        $ask = sAsk::getAskById($askId);
        dd($ask->create_time);
        //创建订单
        $order = $this->createOrder($reply->uid,$sellerUid);
        sThreadCategory::getAsksByCategoryId();
        $ask =
        //获取订单
        $orderId = $this->getOrderId($asks->id);
        //提交支付
        $this->pay($orderId);
        //写流水
        $this->transaction($order);


        //获取商品金额
        $amount = $this->getGoodsAmount(1);

        //检查扣除商品费用后,用户余额是否充足
        $checkUserBalance = $this->checkUserBalance($ask->uid,$amount);
        if(!$checkUserBalance) {
            //写流水交易失败,余额不足
            $this->freezeAccount($ask->uid, $amount, tUser::getBalance($ask->uid),tAccount::ACCOUNT_FAIL_STATUS, '余额不足');
            return error('TRADE_USER_BALANCE_ERROR');
        }

        //操作psgod_trade库
        DB::connection('db_trade')->transaction(function() use($ask,$amount){
            //冻结(求P用户)金额
            $this->freeze($ask->uid,$amount);
            //写冻结流水
            $userGoodsBalance = tUser::getBalance($ask->uid);
            $this->freezeAccount($ask->uid, $amount, $userGoodsBalance,tAccount::ACCOUNT_SUCCEED_STATUS);
            //恢复求P状态为常态
            $this->setAskStatus($ask);
        });



    }
    /*
     * 创建订单
     */
    public function createOrder($uid, $sellerUid)
    {
        $order = new tOrder($uid);
        $order->order_type = tOrder::ORDER_ORDER_TYPE_INSIDE;
        $order->payment_type = tOrder::ORDER_PAYMENT_TYPE_INSIDE;
        $order->status = tOrder::ORDER_STATUS_PAY_WAITING;
        $order->seller_uid = $sellerUid;
        return $order;
    }

}
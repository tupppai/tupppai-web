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
<?php


namespace App\Handles\Trade;


class ReplySaveHandle
{
    public function handle(Event $event)
    {
        $asks = $event->arguments['ask'];
        //1,创建订单
        $orderId = $this->createOrderId($asks->id);
        //2,获取订单
        $orderId = $this->getOrderId($asks->id);
        //3,提交支付
        $this->pay($orderId);
        //4,写流水
        $this->transaction($order);



    }
    /*
     * 判断是否在三天以内
     * 三天以内由发起求P用户支付
     * 超过三天由默认uid支付
     * */
    public function checkPayUser($ask)
    {

    }
    /*
 * 生成订单
 * */
    public function createOrder($asks)
    {
        return true;
    }
    /*
     * 获得订单
     * return orderId
     * */
    public function getOrderId($asksId)
    {
        $orderId = 0;
        return $orderId;
    }
    /*
     * 支付订单
     * */
    public function pay($orderId)
    {
        //支付状态
        $status = 0;
        return $status;
    }
    /*
     * 写流水
     * */
    public function transaction($order)
    {
        return true;
    }
}
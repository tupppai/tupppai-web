<?php


namespace App\Handles\Trade;


class AsksSaveHandle
{
    public function handle(Event $event)
    {
            $asks = $event->arguments['asks'];
            //1,创建订单
            $orderId = $this->getOrderId($asks->id);
            //2,冻结(求P用户)金额
            $this->freeze($uid,$amount);
            //3,提交支付
            $this->pay($orderId);
            //4,写流水
            $this->transaction($order);
        


    }
    /*
     * 创建订单
     * return orderId
     * */
    public function getOrderId($asksId)
    {
        $orderId = 0;
        return $orderId;
    }
    /*
     * 支付
     * */
    public function pay($orderId)
    {
        //支付状态
        $status = 0;
        return $status;
    }
    /*
     * 冻结金额
     * */
    public function freeze($uid,$amount)
    {
        $trade->serFreeze($uid,$amount);
    }
    /*
     * 写流水
     * */
    public function transaction($order)
    {
        return true;
    }
}
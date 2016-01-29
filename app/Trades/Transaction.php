<?php namespace App\Trades;

class Transaction extends TradeBase
{
    protected $connection = 'db_trade';
    public $table = 'transactions';

    public $keys = array(
        'trade_no',
        'out_trade_no',
        'order_id',
        'partner_id',
        'payment_type',
        'currency_type',
        'amount',
        'trade_status',
        'trade_start_time',
        'trade_finish_time',
        'callback_status',
        'callback_finish_time',
        'refund_status',
        'refund_start_time',
        'refund_finish_time',
        'time_start',
        'time_expire',
        'return_url',
        'fail_url',
        'notify_url',
        'subject',
        'body',
        'client_ip',
        'attach',
        'operator',
        'op_remark',
        'status'
    );

    private function create_trade_no($uid, $order_id)
    {
        //更新交易单号规则
        return md5($uid . $order_id . rand());
    }

    /**
     * 创建交易流水
     */
    public static function writeLog($uid, $order_id, $partner_id, $payment_type, $amount, $status, $subject = '', $body = '', $currency = 'cny')
    {
        //生成订单号
        $trade      = new self;
        $trade_no   = $trade->create_trade_no($uid, $order_id);
        
        $datetime   = date("Y-m-d H:i:s");
        $ip         = \Request::ip();

        $trade->setTradeNo($trade_no)
            ->setOrderId($order_id)
            ->setPartnerId($partner_id)
            ->setPaymentType($payment_type)
            ->setAmount($amount)
            ->setTradeStatus(self::STATUS_PAYING)
            ->setTradeStartTime($datetime)
            ->setTimeStart($datetime)
            ->setSubject($subject)
            ->setBody($body)
            ->setCurrencyType($currency)
            ->setClientIp($ip)
            ->setOperator(1)
            ->save();

        return $trade;
    }

    public static function updateTrade($trade_id, $status) {
        $trade = self::find($trade_id);

        $datetime   = date("Y-m-d H:i:s");
        $trade->trade_finish_time   = $datetime;
        $trade->callback_finish_time= $datetime;

        $trade->status = $status;
        $trade->save();

        return $trade;
    }
}

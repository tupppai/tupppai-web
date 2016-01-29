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
        'refund_url',
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
    
    public function setAttachAttribute($value)
    {
        $this->attributes['attach'] = json_encode($value);
    }

    public function getAttachAttribute($value)
    {
        return json_decode($value);
    }

    /**
     * 创建交易流水
     */
    public static function writeLog($uid, $order_id, $partner_id, $payment_type, $amount, $status, $subject = '', $body = '', $currency = 'cny', $attach = '')
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
            ->setOperator($uid)
            ->setAttach($attach)
            ->save();

        return $trade;
    }

    public static function updateTrade($trade_id, $out_trade_no, $status, $amount, $refund_url = '', $time_paid = null, $time_expire = null) {
        $trade = self::find($trade_id);
        if(!$trade) {
            return error('TRADE_NOT_EXIST');
        }

        $datetime   = date("Y-m-d H:i:s");
        if($amount != $trade->amount) {
            return error('AMOUNT_ERROR');
        }

        $trade->setOutTradeNo($out_trade_no)
            ->setRefundUrl($refund_url)
            ->setTradeFinishTime($time_paid)
            ->setCallbackFinishTime($datetime)
            ->setTimeExpire($time_expire)
            ->setStatus($status)
            ->save();

        return $trade;
    }
}

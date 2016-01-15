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

    /**
     * 获取属性的时候获取正直
     */
    public function getAmountAttribute($value)
    {
        return ($value / 1000);
    }

    /**
     * 设置的时候需要校验属性
     */
    public function setAmountAttribute($value)
    {
        $this->attributes['amount'] = $value * 1000;
    }

    /**
     * 设置交易金额的时候判断是否为浮点数
     */
    public function setAmount($value)
    {
        return $this;
    }

    /**
     * 生成订单
     */
    public function __construct($uid, $order_id = '') 
    {
        parent::__construct($uid);
        //生成订单号
        $this->trade_no = $this->create_order_no($uid, $order_id);

        return $this;
    }

    private function create_trade_no($uid, $order_id)
    {
        //更新交易单号规则
        return md5($order_id . rand());
    }

}

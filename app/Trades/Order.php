<?php namespace App\Trades;

class Order extends TradeBase {
    protected $connection   = 'db_trade';
    public $table           = 'orders';
    const ORDER_ORDER_TYPE_INSIDE = 1; //站内订单
    const ORDER_ORDER_TYPE_OUTSIDE = 2; //站外订单订单

    const ORDER_PAYMENT_TYPE_INSIDE = 1; //站内余额
    const ORDER_PAYMENT_TYPE_WX = 2; //微信
    const ORDER_PAYMENT_TYPE_ALIPAY = 3; //支付宝
    const ORDER_PAYMENT_TYPE_UNION = 4; //银联
    const ORDER_PAYMENT_TYPE_CREDIT = 5; //信用卡

    const ORDER_STATUS_PAY_WAITING = 1; //待支付
    const ORDER_STATUS__PAY_SUCCEED = 2; //支付成功
    const ORDER_STATUS_PAY_FAIL = 3; //支付失败
    const ORDER_STATUS_PAY_TIMEOUT = 4; //支付超时
    const ORDER_STATUS_REFUND_WAITING = 5; //待退款
    const ORDER_STATUS_REFUND_SUCCEED = 6; //退款成功
    const ORDER_STATUS_REFUND_FAIL = 7; //退款失败
    const ORDER_STATUS_REFUND_TIMEOUT = 8; //退款超时

    public $keys = array(
        'order_no',
        'order_type',
        'trade_type',
        'sale_type',
        'payment_type',
        'total_amount',
        'discount_id',
        'discount_amount',
        'handling_fee',
        'order_info',
        'operator',
        'op_remark'
    );
 
    /**
     * 设置的时候需要校验属性
     */
    public function setTotalAmountAttribute( $value )
    {
        $this->attributes['total_amount'] = $value/1000;
    }

    /**
     * 获取属性的时候获取正直
     */
    public function getTotalAmountAttribute( )
    {
        $this->attributes['total_amount'] *= 1000;
    }
    
    /**
     * 设置交易金额的时候判断是否为浮点数
     */
    public function setTotalAmount($value) {
        if(!is_double($value)) {
            return error('WRONG_ARGUMENTS', '收入需要为浮点数');
        }
    }

    public function __construct($uid) {
        parent::__construct($uid);
        //生成订单号
        $this->order_no = $this->create_order_no($uid);

        return $this;
    }

    private function create_order_no($uid, $type = '1') {
        //重新定义订单号规则
        return $type.$uid.date("YmdHis");
    }

    public function get_order_by_id($id) {
        return $this->find($id);
    }

    /*
     * 创建订单
     */
    public function createOrder($sellerUid,$amount)
    {
        $this->order_type = self::ORDER_ORDER_TYPE_INSIDE;
        $this->payment_type = self::ORDER_PAYMENT_TYPE_INSIDE;
        $this->status = self::ORDER_STATUS_PAY_WAITING;
        $this->seller_uid = $sellerUid;
        $this->total_amount = $amount;
        $this->save();
        return $this;
    }

}

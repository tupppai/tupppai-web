<?php namespace App\Trades;

class Order extends TradeBase
{
    protected $connection = 'db_trade';
    public $table = 'orders';
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
        'op_remark',
        'status',
        'seller_uid'
    );

    /**
     * 设置的时候需要校验属性
     */
    public function setTotalAmountAttribute($value)
    {
        $this->attributes['total_amount'] = $value * 1000;
    }

    /**
     * 获取属性的时候获取正直
     */
    public function getTotalAmountAttribute($value)
    {
        return ($value / 1000);
    }

    public function setOrderInfoAttribute($value)
    {
        $this->attributes['order_info'] = json_encode($value);
    }

    public function getOrderInfoAttribute($value)
    {
        return json_decode($value);
    }

    private function create_order_no($uid, $type = '1')
    {
        //重新定义订单号规则
        return $type . $uid . date("YmdHis");
    }

    /**
     * 创建订单
     */
    public static function createOrder($uid, $seller_uid, $amount, $order_info)
    {
        //生成订单号
        $order    = new self;
        $order_no = $order->create_order_no($uid);
        $order->setOrderType(self::ORDER_ORDER_TYPE_INSIDE)
            ->setPaymentType(self::ORDER_PAYMENT_TYPE_INSIDE)
            ->setStatus(self::ORDER_STATUS_PAY_WAITING)
            ->setSellerUid($seller_uid)
            ->setTotalAmount($amount)
            ->setOrderInfo($order_info)
            ->setOrderNo($order_no)
            ->save();

        return $order;
    }

}

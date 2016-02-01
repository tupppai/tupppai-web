<?php namespace App\Trades;

class Order extends TradeBase
{
    protected $connection = 'db_trade';
    public $table = 'orders';

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
        'seller_uid',
        'uid'
    );

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
        return $uid . 'O' . $type . date("YmdHis") . rand(0, 1000);
    }

    /**
     * 创建订单
     */
    public static function writeLog($uid, $seller_uid, $amount, $order_info)
    {
        //生成订单号
        $order    = new self;
        $order_no = $order->create_order_no($uid);

        $order->setOrderType(self::ORDER_ORDER_TYPE_INSIDE)
            ->setPaymentType(self::ORDER_PAYMENT_TYPE_INSIDE)
            ->setStatus(self::ORDER_STATUS_PAY_WAITING)
            ->setUid($uid)
            ->setSellerUid($seller_uid)
            ->setTotalAmount($amount)
            ->setOrderInfo($order_info)
            ->setOrderNo($order_no)
            ->save();

        return $order;
    }

}

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
        'seller_uid'
    );

    public function setOrderInfoAttribute($value)
    {
        $this->attributes['order_info'] = json_encode($value);
    }

    public function getOrderInfoAttribute($value)
    {
        return \json_encode($value);
    }

    /**
     * 设置交易金额的时候判断是否为浮点数
     */
    public function setTotalAmount($value)
    {
        return $this;
    }

    public function __construct($uid)
    {
        parent::__construct($uid);
        //生成订单号
        $this->order_no = $this->create_order_no($uid);

        return $this;
    }

    private function create_order_no($uid, $type = '1')
    {
        //重新定义订单号规则
        return $type . $uid . date("YmdHis");
    }

    public function get_order_by_id($id)
    {
        return $this->find($id);
    }

    /**
     * 生成订单
     */
    public static function writeLog($uid, $seller_uid, $amount, $order_info)
    {
        $this->setOrderType(self::ORDER_ORDER_TYPE_INSIDE)
            ->setPaymentType(self::ORDER_PAYMENT_TYPE_INSIDE)
            ->setStatus(self::ORDER_STATUS_PAY_WAITING)
            ->setSellerUid($sellerUid)
            ->setTotalAmount($amount)
            ->setOrderInfo($orderInfo)
            ->save();
    }

}

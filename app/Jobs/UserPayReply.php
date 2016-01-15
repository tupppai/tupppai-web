<?php namespace App\Jobs;

use App\Services\Ask as sAsk;
use App\Trades\Order as tOrder;
use App\Trades\User as tUser;
use Illuminate\Contracts\Bus\SelfHandling;
use Log;

class UserPayReply extends Job
{
    public $askId;
    public $replyId;
    public $uid;
    public $sellerUid;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($askId, $replyId, $uid, $sellerUid)
    {
        $this->askId = $askId;
        $this->replyId = $replyId;
        $this->uid = $uid;
        $this->sellerUid = $sellerUid;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //获取商品金额
        //$amount  = $this->getGoodsAmount(1);
        $amount    = 0.5;

        $isUserPay = $this->isUserPay();
        if ($isUserPay) {
            $uid = $this->uid;
            //解除冻结
            tUser::unFreezeBalance($uid, $amount);
        } else {
            $uid = tUser::SYSTEM_USER_ID;
        }
        //用户支付 传入购买用户ID
        $order = new tOrder($uid);
        //生成订单 传入卖家ID
        $this->createOrder($order, $this->sellerUid, $amount);
        //支付订单
        tUser::pay($uid, $this->sellerUid, $amount);
    }

    /*
     *
     */
    public function createOrder($order, $sellerUid, $amount)
    {
        $order->setOrderType(self::ORDER_ORDER_TYPE_INSIDE)
            ->setPaymentType(self::ORDER_PAYMENT_TYPE_INSIDE)
            ->setStatus(self::ORDER_STATUS_PAY_WAITING)
            ->setSellerUid($sellerUid)
            ->setTotalAmount($amount)
            ->save();
    }

    /*
     * 是否是用户支付
     */
    public function isUserPay()
    {
        return sAsk::isAskFirstReplyXDay($this->askId, 3);
    }
}

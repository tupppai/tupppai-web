<?php namespace App\Jobs;

use App\Services\Ask as sAsk;
use App\Services\Reply as sReply;
use App\Trades\Account as tAccount;
use App\Trades\Order as tOrder;
use App\Trades\User as tUser;
use Illuminate\Contracts\Bus\SelfHandling;
use Log;

use App\Facades\Alidayu;
use App\Services\Sms as sSms;

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
        //$amount = $this->getGoodsAmount(1);
        $amount = 0.5;

        $isUserPay = $this->isUserPay();
        if ($isUserPay) {
            //用户支付 传入购买用户ID
            $order   = new tOrder($this->uid);
            //生成订单 传入卖家ID
            $orderId = $order->createOrder($this->sellerUid, $amount);
            //解除冻结
            tUser::unFreezeBalance($this->uid,$amount);
            //支付订单
            tUser::pay($this->uid, $this->sellerUid, $amount);
        } else {
            //官方支付
            $order   = new tOrder(tUser::SYSTEM_USER_ID);
            //生成订单 传入卖家ID
            $orderId = $order->createOrder($this->sellerUid, $amount);
            //支付订单
            tUser::pay($this->uid, $this->sellerUid, $amount);
        }
        /*
         * if(判断(三天)是否有一个作品)
         *实际扣款0.5  freezing - 0.5
         *else
         *无任何操作
         * */

    }



    /*
     * 是否是用户支付
     */
    public function isUserPay()
    {
        return sAsk::isAskFirstReplyXDay($this->askId,3);
    }
}

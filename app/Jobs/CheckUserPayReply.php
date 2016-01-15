<?php namespace App\Jobs;

use App\Services\Ask as sAsk;
use App\Services\Product as sProduct;
use App\Services\Reply as sReply;
use App\Trades\Order as tOrder;
use App\Trades\User as tUser;
use Illuminate\Contracts\Bus\SelfHandling;
use Log;

class CheckUserPayReply extends Job
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
    public function __construct($askId, $replyId, $uid)
    {
        $this->askId = $askId;
        $this->replyId = $replyId;
        $this->uid = $uid;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //获取7天内作品点赞数最高的用户
        $sellerUid = sReply::getAskForReplyMaxLike($this->askId);
        //获取ask保存amount金额
        $ask = sAsk::getAskById($this->askId);
        $amount = $ask->amount;

        //获取商品信息
        $orderInfo = sProduct::getProductById(1);
        $orderInfo['price'] = $amount;

        //是否是用户支付
        if (sAsk::isAskHasFirstReplyXDay($this->askId, 3)) {
            $uid = $this->uid;
            //解除冻结
            tUser::unFreezeBalance($uid, $amount);
        } else {
            $uid = tUser::SYSTEM_USER_ID;
        }
        //用户支付 传入购买用户ID
        $order = new tOrder($uid);
        //生成订单 传入卖家ID
        $order->createOrder($sellerUid, $amount, $orderInfo);
        //支付订单
        tUser::pay($uid, $sellerUid, $amount);
    }

}

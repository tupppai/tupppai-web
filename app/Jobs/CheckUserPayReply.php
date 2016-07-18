<?php namespace App\Jobs;

use App\Services\Ask as sAsk;
use App\Services\Product as sProduct;
use App\Services\Reply as sReply;
use App\Trades\Order as tOrder;
use App\Trades\User as tUser;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Support\Facades\DB;
use Log;

class CheckUserPayReply extends Job
{
    public $ask_id;
    public $reply_id;
    public $uid;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($ask_id, $reply_id)
    {
        $this->ask_id   = $ask_id;
        $this->reply_id = $reply_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{
            //获取7天内点赞数最高的作品
            $reply      = sReply::getMaxLikeReplyForAsk($this->ask_id);
            $reply_uid  = $reply->uid;
            $ask        = sAsk::getAskById($this->ask_id);
            $amount     = $ask->amount;

            //获取商品信息
            $orderInfo = sProduct::getProductById(1);
            $orderInfo['price'] = $amount;

            //检查Ask第一个作品是否是3天以内发送
            if(sAsk::isAskHasFirstReplyXDay($this->ask_id, 3)) {
                $uid = $ask->uid;
            }
            else {
                $uid = tUser::SYSTEM_USER_ID;
            }
            if( $amount > 0 ){
                DB::connection('db_trade')->transaction(function () use ($reply_uid, $orderInfo, $amount ,$uid) {
                    //生成订单 传入卖家ID
                    tOrder::writeLog($uid, $reply_uid, $amount, $orderInfo);

                    tUser::addBalance($reply_uid, $amount, '作品收入'); //支付订单
                });
            }
        } catch (\Exception $e) {
            Log::error('CheckUserPayReply', array($e->getLine().'------'.$e->getMessage()));
        }
    }

}

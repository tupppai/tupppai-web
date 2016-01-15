<?php


namespace App\Handles\Trade;


use App\Events\Event;
use App\Jobs\UserPayReply;
use App\Services\Ask as sAsk;
use App\Services\Reply as sReply;
use App\Services\ThreadCategory as sThreadCategory;
use App\Trades\Order as tOrder;
use Carbon\Carbon;

class ReplySaveHandle
{
    public function handle(Event $event)
    {
        $reply = $event->arguments['reply'];
        $replyId = $reply->id;
        //求助ID
        $askId = $reply->ask_id;
        //获取ask
        $ask = sAsk::getAskById($askId);
        //获取求P发起人 user id
        $uid = $ask->uid;
        //获取作品人 user id
        $sellerUid = $reply->uid;
        $first = $this->isReplyForFirstAsk($askId);
        if ($first) {
            //设置延迟7天执行付款
            $laterSevenPay = Carbon::now()->addDays(7);
            Queue::later($laterSevenPay, new UserPayReply($askId, $replyId, $uid,$sellerUid));
        }
    }

    public function isReplyForFirstAsk($askID)
    {
        $count = sReply::getRepliesCountByAskId($askID);
        return $count < 2 ? true : false;
    }


}
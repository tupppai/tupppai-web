<?php


namespace App\Handles\Trade;


use App\Events\Event;
use App\Jobs\CheckUserPayReply;
use App\Services\Ask as sAsk;
use App\Services\Reply as sReply;
use Carbon\Carbon;
use Log;
use Queue;

class ReplySaveHandle
{
    public function handle(Event $event)
    {
        try {
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
            //是否是Ask对应的第一个作品,是才执行queue
            if ($first) {
                //设置延迟7天执行付款
                $laterSevenPay = Carbon::now()->addDays(7);
                Queue::later($laterSevenPay, new CheckUserPayReply($askId, $replyId, $uid));
            }
        }catch(\Exception $e){
            Log::error('ReplySaveHandle', $e);
        }
    }

    public function isReplyForFirstAsk($askID)
    {
        $count = sReply::getRepliesCountByAskId($askID);
        return $count < 2 ? true : false;
    }


}
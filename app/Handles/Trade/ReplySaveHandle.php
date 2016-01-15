<?php


namespace App\Handles\Trade;


use App\Events\Event;
use App\Jobs\UserPayReply;
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
            $reply      = $event->arguments['reply'];
            $replyId    = $reply->id;
            //获取作品人 user id
            $sellerUid  = $reply->uid;

            //求助ID
            $askId  = $reply->ask_id;
            //获取ask
            $ask    = sAsk::getAskById($askId);
            //获取求P发起人 user id
            $uid    = $ask->uid;

            //是否是Ask对应的第一个作品,是才执行queue
            //todo 这里应该改成，判断当前replyID的作品是否createtime排第一位，不过问题不大，只是判断时间而已
            $count = sReply::getRepliesCountByAskId($askId);
            if($count == 1) {
                //设置延迟7天执行付款
                $laterSevenPay = Carbon::now()->addDays(7);
                Queue::later($laterSevenPay, new UserPayReply($askId, $replyId, $uid));
            }
        }catch(\Exception $e){
            Log::error('ReplySaveHandle', $e);
        }
    }

}

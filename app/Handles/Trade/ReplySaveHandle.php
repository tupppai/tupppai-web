<?php namespace App\Handles\Trade;

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
            $reply      = $event->arguments['reply'];
            $reply_id   = $reply->id;
            $ask_id     = $reply->ask_id;

            if (sReply::getRepliesCountByAskId($ask_id) == 1) {
                //设置延迟7天执行付款
                //Queue::later(Carbon::now()->addDays(7), new CheckUserPayReply($ask_id, $reply_id));
                Queue::later(Carbon::now()->addMinutes(2), new CheckUserPayReply($ask_id, $reply_id));
            }
        } catch(\Exception $e){
            Log::error('ReplySaveHandle', array($e->getLine().'------'.$e->getMessage()));
        }
    }

}

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

            //此版本上线以后的求P开始计费
            $ask = sAsk::getAskById($ask_id);
            $start_time = Carbon::create(2016,1,18,13);
            $ask_time = Carbon::createFromTimestamp($ask->create_time);
            if($ask_time->lt($start_time)){
                return false;
            }

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

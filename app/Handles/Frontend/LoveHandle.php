<?php namespace App\Handles\Frontend;

use App\Events\Event;
use App\Jobs\Push;
use App\Models\Count;
use App\Services\ActionLog;
use Queue;

class LoveHandle
{
    public function __construct()
    {
        
    }

    public function handle(Event $handle)
    {
        $reply = $handle->arguments['reply'];
        $count = $handle->arguments['count'];

        if($count->status == Count::STATUS_NORMAL) {
            //todo 推送一次，尝试做取消推送
            if(_uid() != $reply->uid)
                Queue::push(new Push(array(
                    'uid'=>_uid(),
                    'target_uid'=>$reply->uid,
                    //前期统一点赞,不区分类型
                    'type'=>'like_reply',
                    'target_id'=>$reply->id,
                )));

            ActionLog::init( 'TYPE_UP_REPLY', $reply);
        }
    }
}

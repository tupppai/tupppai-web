<?php namespace App\Handles\Frontend;

use App\Events\Event;
use App\Services\Reply;

class LoveHandle
{
    public function __construct()
    {
        
    }

    public function handle(Event $event)
    {
        list($id, $num, $status) = $event->arguments;
        Reply::loveReply($id, $num, $status);
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: zhiyong
 * Date: 16/1/6
 * Time: 下午7:59
 */

namespace App\Listeners;

use App\Events\Event;
use App\Handles\Handle;

class HandleEventListener
{
    public function __construct()
    {
        
    }

    public function handle(Event $event)
    {
        Handle::listen($event);
    }
}

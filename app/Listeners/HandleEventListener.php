<?php
/**
 * Created by PhpStorm.
 * User: zhiyong
 * Date: 16/1/6
 * Time: 下午7:59
 */

namespace App\Listeners;

use App\Events\HandleEvent;
use App\Handles\Handle;

class HandleEventListener
{
    public function __construct()
    {
        
    }

    public function handle(HandleEvent $event)
    {
        Handle::listen($event);
    }
}
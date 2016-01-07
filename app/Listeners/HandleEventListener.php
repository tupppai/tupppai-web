<?php
/**
 * Created by PhpStorm.
 * User: zhiyong
 * Date: 16/1/6
 * Time: 下午7:59
 */

namespace App\Listeners;

use App\Events\HandleEvent;
use App\Handles\AppHandle;

class HandleEventListener
{
    public function __construct()
    {
        
    }

    public function handle(HandleEvent $event)
    {
        AppHandle::listen($event);
    }
}
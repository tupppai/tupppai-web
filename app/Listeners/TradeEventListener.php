<?php namespace App\Listeners;

use App\Events\TradeEvent;
use App\Trades\Trade;

class TradeEventListener
{
    public function __construct()
    {
        
    }

    public function handle(TradeEvent $event)
    {
        Trade::listen($event);
    }
}

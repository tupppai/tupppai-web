<?php namespace App\Handles\Trade;

use App\Events\Event;

class Trade
{
    public function __construct()
    {
        
    }

    public function handle(Event $handle)
    {
        //This is Logic
        return 2;
    }
}

<?php namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldBeQueued;
#use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class QueryLogEvent extends Event implements ShouldBeQueued
{
    use SerializesModels;
    
    public $host;
    public $message;
    public $context;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($host, $message, $context)
    {
        $this->host = $host;
        $this->message = $message;
        $this->context = $context;
    }
}

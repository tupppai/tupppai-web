<?php namespace App\Events;

use Log;
use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldBeQueued;
#use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class QueueLogEvent extends Event 
{
    use SerializesModels;

    public $app_host;
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
        $this->app_host = env('APP_HOST', 'sys');
        $this->host = $host;
        $this->message = $message;
        $this->context = $context;
    }
}

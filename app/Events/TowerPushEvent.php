<?php namespace App\Events;

use Log;
use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldBeQueued;
#use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TowerPushEvent extends Event implements ShouldBeQueued
{
    use SerializesModels;

    public $message;
    public $context;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($message, $context)
    {
        $this->message = $message;
        $this->context = $context;
    }
}

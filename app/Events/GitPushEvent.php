<?php namespace App\Events;

use Log;
use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldBeQueued;
#use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class GitPushEvent extends Event implements ShouldBeQueued
{
    use SerializesModels;

    public $password;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($password)
    {
        $this->password = $password;
    }
}

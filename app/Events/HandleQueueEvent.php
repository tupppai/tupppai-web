<?php namespace App\Events;

use Illuminate\Contracts\Queue\ShouldQueue;

class HandleQueueEvent extends Event implements ShouldQueue
{
    public $arguments;
    public $listenCode;

    public function __construct($listenCode, array $arguments)
    {
        $this->arguments = $arguments;
        $this->listenCode = $listenCode;
    }
}

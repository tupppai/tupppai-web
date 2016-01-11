<?php namespace App\Events;


use Illuminate\Contracts\Queue\ShouldQueue;

class HandleEvent extends Event implements ShouldQueue
{
    public $arguments;
    public $listenCode;

    public function __construct($listenCode, array $arguments)
    {
        $this->arguments = $arguments;
        $this->listenCode = $listenCode;
    }
}

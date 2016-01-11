<?php namespace App\Events;

class HandleSyncEvent extends Event 
{
    public $arguments;
    public $listenCode;

    public function __construct($listenCode, array $arguments)
    {
        $this->arguments = $arguments;
        $this->listenCode = $listenCode;
    }
}

<?php namespace App\Console\Scripts;

class Tower extends Script
{
    private $users ;
    public function __construct() {
        $this->users = array(
            '逍遥'=>1
        );
    }

    public function sendPush() {
        
    }
}

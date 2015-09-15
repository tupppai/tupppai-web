<?php namespace App;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

class Application extends \Laravel\Lumen\Application 
{

    public function __construct($basePath = null) 
    {
        parent::__construct($basePath);
    }

} 

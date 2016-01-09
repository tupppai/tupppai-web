<?php namespace App;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

class Application extends \Laravel\Lumen\Application 
{

    public function __construct($basePath = null) 
    {
        parent::__construct($basePath);
        $this->configure('global');
        $this->configure('handle');
    }
    
    /**
     * Get the storage path for the application.
     *
     * @param  string|null  $path
     * @return string
     */
    public function storagePath($path = null)
    {
        if ($this->storagePath) {
            return $this->storagePath.($path ? '/'.$path : $path);
        }

        // move storage to data
        return '/data/storage'.($path ? '/'.$path : $path);
        //return $this->basePath().'/storage'.($path ? '/'.$path : $path);
    }

} 

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

    /**
     * Register container bindings for the application.
     *
     * @return void
     */
    protected function registerLogBindings()
    {
        $host       = $this->request->getHost();
        $hostname   = hostmaps($host);

        $this->singleton('Psr\Log\LoggerInterface', function () {
            return new Logger('lumen', [$this->getMonologHandler()]);
        });
    }

    /**
     * Get the Monolog handler for the application.
     *
     * @return \Monolog\Handler\AbstractHandler
     */
    protected function getMonologHandler()
    {
        $host       = $this->request->getHost();
        $hostname   = hostmaps($host);
        $filename   = $hostname.'_'.date("Ymd");

        return (new StreamHandler(
                storage_path("logs/$filename.log"), 
                Logger::DEBUG)
            )->setFormatter(
                new LineFormatter(null, null, true, true)
            );
    }

} 

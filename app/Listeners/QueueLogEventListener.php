<?php namespace App\Listeners;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;
use App\Events\QueueLogEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class QueueLogEventListener implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  SomeEvent  $event
     * @return void
     */
    public function handle(QueueLogEvent $event)
    {
        $logger = new Logger($event->app_host, [$this->getMonologHandler($event->host)]);
        $logger->info($event->message, $event->context);
    }
    
    protected function getMonologHandler($host)
    {
        $filename   = $host.'_'.date("Ymd");

        return (new StreamHandler(
                storage_path("logs/$filename.log"), 
                Logger::DEBUG)
            )->setFormatter(
                new LineFormatter(null, null, true, true)
            );
    }
}    

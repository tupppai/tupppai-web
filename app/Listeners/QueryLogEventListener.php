<?php namespace App\Listeners;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;
use App\Events\QueryLogEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class QueryLogEventListener 
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
    public function handle(QueryLogEvent $event)
    {
        $logger = new Logger('sql', [$this->getMonologHandler($event->host)]);
        $logger->info($event->message, $event->context);
    }
    
    protected function getMonologHandler($host)
    {
        $filename   = $host.'_'.date("Ymd");

        return (new StreamHandler(
                storage_path("sqls/$filename.log"), 
                Logger::DEBUG)
            )->setFormatter(
                new LineFormatter(null, null, true, true)
            );
    }
}

<?php namespace App\Listeners;

use App\Events\TowerPushEvent;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class TowerPushEventListener 
{
    private $tower_path ;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->tower_path = storage_path('app/tower.log');
        //
    }

    /**
     * Handle the event.
     *
     * @param  SomeEvent  $event
     * @return void
     */
    public function handle(TowerPushEvent $event)
    {
        $fp      = file($this->tower_path);
        $lastRow = $fp[count($fp)-1];

        $arr = explode(" ", $lastRow);
        if(sizeof($arr) > 0 && $arr[0] < $event->message) {
            $logger = new Logger('', [$this->getMonologHandler()]);
            $logger->info($event->message, $event->context);
        }
    }
    
    protected function getMonologHandler()
    {
        return (new StreamHandler($this->tower_path))->setFormatter(new LineFormatter("%message% %context% \n"));
    }
}

<?php namespace App\Listeners;

use App\Events\GitPushEvent;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class GitPushEventListener 
{
    private $gitlog_path ;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->gitlog_path = storage_path('app/git.log');
        //
    }

    /**
     * Handle the event.
     *
     * @param  SomeEvent  $event
     * @return void
     */
    public function handle(GitPushEvent $event)
    {
        $logger = new Logger('', [$this->getMonologHandler()]);

        $process = new Process('cd /var/www/ps; git pull');
        $process->run();

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            $logger->info('error', [$process->getErrorOutput()]);
            return false;
        }

        $logger->info('success', [$process->getOutput()]);
        return true;
    }
    
    protected function getMonologHandler()
    {
        return (new StreamHandler($this->gitlog_path))->setFormatter(new LineFormatter("%message% %context% \n"));
    }
}

<?php namespace App\Jobs;

use Illuminate\Contracts\Bus\SelfHandling;
use Log;

class SendEmail extends Job 
{
    public $message = '';
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($message)
    {
        $this->message = $message;
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        Log::info('test'.$this->message);
    }
}

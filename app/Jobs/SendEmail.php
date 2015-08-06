<?php namespace App\Jobs;

use Illuminate\Contracts\Bus\SelfHandling;
use App\Facades\Sms;
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
        /*
        $phone = '15018749436';
        $active_code = '123456';
        $send = Sms::phone( $phone )
                     -> content( str_replace('::code::', $active_code, VERIFY_MSG) )
                     -> send();
         */
    }
}

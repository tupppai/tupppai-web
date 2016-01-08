<?php namespace App\Jobs;

use Illuminate\Contracts\Bus\SelfHandling;
use Log;

use App\Facades\Alidayu;
use App\Services\Sms as sSms;

class SendSms extends Job 
{
    public $phone   = '';
    public $code    = '';
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($phone, $code)
    {
        $this->phone    = $phone;
        $this->code     = $code;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //todo: 检索成功率, schedule
        $count = sSms::countMiss();

        Alidayu::send($this->phone, $this->code);

        sSms::addNewSms($this->phone, $this->code);
    }
}

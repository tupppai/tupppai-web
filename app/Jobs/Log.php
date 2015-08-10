<?php namespace App\Jobs;

use Illuminate\Contracts\Bus\SelfHandling;
use App\Facades\Cache;

use App\Services\UserDeivce as sUserDevice,
    App\Services\Push as sPush,
    App\Services\Message as sMessage;

class Push extends Job 
{
    public $platform = '';
    public $type    = array();
    public $str     = array();

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($platform, $type, $str='')
    {
        $this->platform = $platform;
        $this->type = $type;
        $this->str  = $str;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        
    }
}

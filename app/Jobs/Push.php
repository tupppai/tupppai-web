<?php namespace App\Jobs;

use Illuminate\Contracts\Bus\SelfHandling;
use App\Facades\Umeng;

use App\Services\UserDeivce as sUserDevice,
    App\Services\Push as sPush,
    App\Services\Message as sMessage;

class Push extends Job 
{
    public $cond   = array();

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($cond)
    {
        #参数
        $this->cond     = $cond;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        #todo push switch
        #todo switch type token list
        $data = sPush::getPushDataTokensByType($this->cond);
        if( empty($data) ){
            return false;
        }
        $type = $this->cond['type'];

        $custom = array(
            'type'=>$type,
            'count'=>1
        );

        //umeng push
        Umeng::push($data, $custom);
        //record push message
        $data = array_merge($this->cond, $data);
        sPush::addNewPush($type, json_encode($data));
    }
}

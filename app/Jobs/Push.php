<?php namespace App\Jobs;

use Illuminate\Contracts\Bus\SelfHandling;
use App\Facades\Umeng;

use App\Services\UserDeivce as sUserDevice,
    App\Services\Push as sPush,
    App\Services\Message as sMessage;

class Push extends Job 
{
    public $text   = '';
    public $uid    = '';
    public $custom = array();

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($uid, $custom, $data=null)
    {
        #todo: i18n
        $this->text     = '';
        #参数
        $this->uid      = $uid;
        $custom['data'] = $data;
        $this->custom   = $custom;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if( empty($this->custom) && isset($this->custom['type']) ){
            #todo: record error data
            return false;
        }
        $type       = $this->custom['type'];
        #todo push switch
        #todo switch type token list
        $data = sPush::getPushDataTokensByType($this->uid, $type);
        if( empty($data) ){
            return false;
        }

        //umeng push
        Umeng::push($data['text'], $this->custom, $data['token']);
        //record push message
        $data = array_merge($this->custom, $data);
        sPush::addNewPush($type, json_encode($data));
    }
}

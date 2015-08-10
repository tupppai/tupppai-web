<?php namespace App\Jobs;

use Illuminate\Contracts\Bus\SelfHandling;
use App\Facades\Umeng;

use App\Services\UserDeivce as sUserDevice,
    App\Services\Push as sPush,
    App\Services\Message as sMessage;

class Push extends Job 
{
    public $text   = '';
    public $tokens = array();
    public $custom = array();

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($uid, $custom, $data=null)
    {
        parent::__construct();
        #todo: i18n
        $this->text     = '';
        #参数
        $this->uid      = $uid;
        $custom['data'] = $data;
        $this->custom   = $custom;
    }

    /**
     * set the text for push
     */
    public function text($text='') {
        $this->text = $text;

        return $this;
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
        $data = sPush::getPushDataByType($uid, $type);
        if( empty($data) ){
            return false;
        }

        //record push message
        $data = array_merge($this->custom, $data);
        sPush::addNewPush($type, json_encode($data));
    }
}

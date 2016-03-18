<?php

namespace App\Jobs;

use App\Jobs\Job;

use \Log;
use Overtrue\Wechat\Notice as WXNotice;

class SendWxMsg extends Job
{
    protected $openid;
    protected $tplId;
    protected $url;
    protected $vars;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($tplId, $vars, $openid, $url)
    {
        $this->openid = $openid;
        $this->tplId  = $tplId;
        $this->url    = $url;
        $this->vars   = $vars;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $notice = new WXNotice(env('WX_APPID'), env('WX_APPSECRET'));
            $notice = $notice->data( $this->vars );

            $result[] = $notice->send(
                $this->openid,
                $this->tplId,
                array(),
                $this->url
            );
        } catch (\Exception $e) {
            Log::info('exception', array($e));
        }
    }
}

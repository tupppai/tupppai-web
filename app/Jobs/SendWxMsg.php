<?php

namespace App\Jobs;

use App\Jobs\Job;

use \Log;
// use Redis;
// use Doctrine\Common\Cache\RedisCache;
use EasyWeChat\Core\AccessToken as WXAccessToken;
use EasyWeChat\Notice\Notice as WXNotice;

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
            $accessToken = new WXAccessToken(env('WX_APPID'), env('WX_APPSECRET'));//, $cache);
            $notice = new WXNotice( $accessToken );

            $result[] = $notice->send([
                'touser' => $this->openid,
                'template_id' => $this->tplId,
                'data' => $this->vars,
                'url' => $this->url
            ]);
        } catch (\Exception $e) {
            Log::info('exception', array($e));
        }
    }
}

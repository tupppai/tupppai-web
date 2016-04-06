<?php namespace App\Providers;

use EasyWeChat\Foundation\Application;
use Illuminate\Support\ServiceProvider;

use Qiniu;
use Umeng;
use Alidayu;

class LibraryServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //qiniu cdn
        $this->app->singleton('CloudCDN', function($app) {
            //默认为七牛
            return new Qiniu(
                env('QINIU_AK'),
                env('QINIU_SK'),
                env('QINIU_BUCKET'),
                env('QINIU_DOMAIN')
            );
            //Youpai
        });

        //Umeng push
        $this->app->singleton('Umeng', function($app) {
            $umeng = new Umeng;
            return $umeng;
        });

        //Alidayu
        $this->app->singleton('Alidayu', function($app) {
            $alidayu = new Alidayu(env('SMS_APPKEY_TOP'), env('SMS_SECRET_TOP'));
            return $alidayu;
        });

        //EasyWeChat
        $this->app->singleton('EasyWeChat',function($app){
            return new Application(config('wechat'));
        });


        /*
        //Xss Html filter
        $this->app->singleton('XssHtml', function($app) {
            $xss = new XssHtml;
            return $xss;
        });

        /*
        //System Logger 
        $this->app->singleton('Logger', function($app) {
            $logger = new Logger;
            return $logger;
        });

         */
        $this->app->singleton('PingppLog', function($app){
            return new PingppLog;
        });
    }
}

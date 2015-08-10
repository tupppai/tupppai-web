<?php namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Qiniu;
use Umeng;

class CacheServiceProvider extends ServiceProvider
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

        //xuanwu sms
        /*
        $this->app->singleton('Sms2', function($app) {
            $send = $Msg -> phone( $phone )
                         -> content( str_replace('::code::', $active_code, VERIFY_MSG) )
                         -> send();
            return new Sms2();
        });
        */
    }
}

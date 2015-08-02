<?php namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Qiniu;

class CloudCDNServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
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
    }
}

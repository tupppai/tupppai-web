<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Toplan\Sms\SmsManager;

class SmsManagerServiceProvider extends ServiceProvider
{
    /**
     * bootstrap, add routes
     */
    public function boot()
    {
        class_alias('Toplan\Sms\Facades\SmsManager', 'SmsManager');
    }

    /**
     * register the service provider
     */
    public function register()
    {
        $this->app->configure('laravel-sms');

        $this->app->singleton('SmsManager', function($app){
            return new SmsManager($app);
        });
    }

    public function provides()
    {
        return array('SmsManager');
    }
}

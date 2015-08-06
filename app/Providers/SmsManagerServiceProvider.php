<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Toplan\Sms\SmsManager;

class SmsManagerServiceProvider extends ServiceProvider
{
    protected static $aliasesRegistered = false;

    /**
     * bootstrap, add routes
     */
    public function boot()
    {
        if (! static::$aliasesRegistered) {
            static::$aliasesRegistered = true;
            class_alias('Toplan\Sms\Facades\SmsManager', 'SmsManager');
        }
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

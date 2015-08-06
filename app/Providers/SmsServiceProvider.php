<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Toplan\Sms\Sms;

class SmsServiceProvider extends ServiceProvider
{
    /**
     * register the service provider
     */
    public function register()
    {
        $this->app->configure('laravel-sms');

        $this->app->singleton('Sms', function($app){
            return new Sms();
        });
    }
}

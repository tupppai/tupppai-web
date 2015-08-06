<?php namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Sms extends Facade {
    
    /**
     * usage :
     * Sms::make([
              'YunPian'    => '1',
              'SubMail'    => '123'
          ])
          ->to('15018749436')
          ->data(['皮埃斯网络科技', '123456'])
          ->content('【皮埃斯网络科技】您的验证码是123456')
          ->send();
     */
    protected static function getFacadeAccessor() { 
        return 'Sms'; 
    }

}

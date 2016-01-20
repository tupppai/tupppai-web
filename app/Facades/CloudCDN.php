<?php namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class CloudCDN extends Facade {
    
    protected static function getFacadeAccessor() { 
        return 'CloudCDN'; 
    }

}

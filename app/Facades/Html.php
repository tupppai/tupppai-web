<?php namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Html extends Facade {
    
    /**
     * usage:
     */
    protected static function getFacadeAccessor() { 
        return 'html'; 
    }

}

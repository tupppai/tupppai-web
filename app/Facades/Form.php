<?php namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Form extends Facade {
    
    /**
     * usage:
     */
    protected static function getFacadeAccessor() { 
        return 'form'; 
    }

}

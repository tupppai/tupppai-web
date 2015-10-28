<?php namespace App\Services;

use App\Models\IException as mIException;

class IException extends ServiceBase{
    
    public static function addNewException($message)
    {
        $obj = new mIException();
        $obj->messages = $message;
        $obj->create_time = time();
        return $obj->save();
    }
}

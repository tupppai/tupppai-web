<?php namespace App\Services;

use App\Models\Usermeta as mUsermeta,
    App\Models\Config as mConfig;

class Config extends ServiceBase
{

    public static function data() {
        return array(
            Usermeta::KEY_STAFF_TIME_PRICE_RATE
        );
    }

    public static function setConfig($id, $name, $value)
    {
        //todo: name in data array
        $config = mConfig::find($id);
        sActionLog::init( 'SET_CONFIG', $config ); 
        $config->value= $value;
        $config->save();

        sActionLog::save();
        return true;
    }

    public static function getConfig($key){
        #sky 在model里面写一个get_model_by_name
        $config = mConfig::where('name', $key)
            ->first();
        return $config->value;
    }
}

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
        $config->value= $value;

        return $config->save();
    }

    public static function getConfig($key){
        $config = mConfig::where('name', $key)
            ->first();
        return $config->value;
    }
}

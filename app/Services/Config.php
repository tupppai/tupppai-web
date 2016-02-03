<?php namespace App\Services;

use App\Models\Usermeta as mUsermeta,
    App\Models\Config as mConfig;

use App\Services\ActionLog as sActionLog;

class Config extends ServiceBase
{

    public static function data() {
        return array(
            mConfig::KEY_STAFF_TIME_PRICE_RATE
        );
    }

    public static function setConfig( $name, $value, $remark = '' )
    {
        //todo: name in data array
        $config = (new mConfig)->get_config($name);
        if(!$config){
            $config = new mConfig();
            $config->name = $name;
            $config->remark = $remark;
        }

        sActionLog::init( 'SET_CONFIG', $config );
        $config->set_config( $value, $remark );
        sActionLog::save( $config );

        return $config;
    }

    public static function getConfig($key){
        return (new mConfig)->get_config( $key );
    }
}

<?php

namespace App\Models;

use \App\Models\Usermeta;

class Config extends ModelBase
{

    protected $table = 'configs';
    public static function data() {
        return array(
            Usermeta::KEY_STAFF_TIME_PRICE_RATE
        );
    }

    public static function setConfig($id, $name, $value)
    {
        //todo: name in data array
        $config = self::findFirst("id = $id");
        $config->value= $value;
        if($config->create_time == 0)
            $config->create_time = time();
        $config->update_time = time();

        return $config->save_and_return($config, true);
    }

    public static function getConfig($key){
        $config = self::findFirst("name='$key'");
        return $config->value;
    }

}

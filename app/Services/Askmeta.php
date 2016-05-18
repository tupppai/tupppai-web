<?php

namespace App\Services;

use App\Models\Askmeta as mAskmeta;
use App\Services\ActionLog as sActionLog;

class Askmeta extends ServiceBase{

    public static function get( $ask_id, $key, $default_value = NULL ){
        $mAskmeta = new mAskmeta();
        $meta = $mAskmeta->get( $ask_id, $key );

        return $meta ? $meta->ameta_value : $default_value;
    }

    public static function set( $ask_id, $key, $value ){
        return (new mAskmeta)->set( $ask_id, $key, $value );
    }
}

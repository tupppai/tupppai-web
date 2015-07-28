<?php

namespace App\Models;

class Push extends ModelBase
{
    const TYPE_ASK      = 1;
    const TYPE_REPLY    = 2;
    const TYPE_COMMENT  = 4;


    public function getSource()
    {
        return 'pushes';
    }

    //public static function addNewPush($type, $data)
    //public static function lastPushTime($type){
}

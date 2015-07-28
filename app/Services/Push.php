<?php

namespace Psgod\Service;
use \Psgod\Models\Push as mPush;

class Push extends ServiceBase
{

    public static function addNewPush($type, $data)
    {
        $obj = new mPush();
        $obj->type      = $type;
        $obj->data      = $data;
        $obj->create_time   = time();
        
        return $obj->save_and_return($obj);
    }

    public static function lastPushTime($type){
        $push = mPush::findFirst(array(
            'type='.$type,
            'order'=>'create_time desc'
        ));
        if($push) {
            return $push->create_time;
        }
        return 0;
    }
}

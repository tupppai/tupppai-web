<?php

namespace App\Service;
use \App\Models\Push as mPush;

class Push extends ServiceBase
{

    public static function addNewPush($type, $data)
    {
        $push = new mPush();
        $push->assign(array(
            'type' => $type,
            'data' => $data
        ));

        return $push->save();
    }
}

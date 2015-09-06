<?php namespace App\Services;

use App\Models\Push as mPush,
    App\Models\Comment as mComment,
    App\Models\Message as mMessage;

use App\Services\UserDevice as sUserDevice,
    App\Services\Focus as sFocus, 
    App\Services\ActionLog as sActionLog, 
    App\Services\Ask as sAsk;

class Push extends ServiceBase
{
    public static function addNewPush($type, $data)
    {
        sActionLog::init('ADD_NEW_PUSH');
        $push = new mPush();
        $push->assign(array(
            'type' => $type,
            'data' => $data
        ));
        sActionLog::save($push);

        return $push->save();
    } 
}

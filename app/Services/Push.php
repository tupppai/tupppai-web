<?php namespace App\Service;
use App\Models\Push as mPush,
    App\Models\Message as mMessage;

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
    
    public static function getPushDataTokensByType($uid, $type) {
        $data = array();
        switch($type){
        case self::TYPE_COMMENT:
            $data['text'] = "收到一条评论消息";
            $data['token']= sPush::getUserDeviceToken($uid);
            break;
        case self::TYPE_REPLY:
            $data['text'] = "收到一条作品消息";
            $data['token']= sPush::getUserDeviceToken($uid);
            break;
        case self::TYPE_FOLLOW:
            $data['text'] = "有新的朋友关注你";
            $data['token']= sPush::getUserDeviceToken($uid);
            break;
        case self::TYPE_INVITE:
            $data['text'] = "有朋友邀请你帮忙P图";
            $data['token']= sPush::getUserDeviceToken($uid);
            break;
        case self::TYPE_SYSTEM:
            $data['text'] = "收到一条系统消息";
            $data['token']= sPush::getUserDeviceToken($uid);
            break;
        default:
            break;
        }

        return $data;
    }
}

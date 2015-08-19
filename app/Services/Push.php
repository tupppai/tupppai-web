<?php namespace App\Services;

use App\Models\Push as mPush,
    App\Models\Message as mMessage;

use App\Services\UserDevice as sUserDevice,
    App\Services\Focus as sFocus, 
    App\Services\Ask as sAsk;

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
    
    public static function getPushTextByType($type) {
        $types = array(
                 'comment_comment'=>'刘金平回复了你一条评论',
                 'comment_reply'=>'刘金平评论了你的作品',
                 'comment_ask'=>'刘金平评论了你的求助',
                 'like_comment'=>'刘金平赞了你的评论',
                 'like_reply'=>'刘金平赞了你的作品',
                 'like_ask'=>'刘金平赞了你的求助',
                 'inform_comment'=>'你发的评论被举报了',
                 'inform_reply'=>'你发的作品被举报了',
                 'inform_ask'=>'你发的求助被举报了',
                 'focus_ask'=>'关注求助',
                 'collect_reply'=>'收藏作品',
                 'follow'=>'刘金平关注了你',
                 'unfollow'=>'有好友取消了对你的关注',
                 'post_ask'=>'你的关注刘金平发布了新的求助',
                 'post_reply'=>'你的关注刘金平发布了新的作品',
                 'invite'=>'刘金平向你发送了求助邀请'
        );

        /*
        $types = array(
            'comment_comment'=>'收到一条评论消息',
            'comment_reply'=>'收到一条评论消息',
            'comment_ask'=>'收到一条评论消息',
            'like_comment'=>'收到朋友点赞',
            'like_reply'=>'收到朋友点赞',
            'like_ask'=>'收到朋友点赞',
            'inform_comment'=>'您发的评论被举报了',
            'inform_reply'=>'您发的作品被举报了',
            'inform_ask'=>'你发的求助被举报了',
            'focus_ask'=>'关注求助',
            'collect_reply'=>'收藏作品',
            'follow'=>'有新朋友关注了你',
            'unfollow'=>'有好友取消了对您的关注',
            'post_ask'=>'您有好友发布了新的求助',
            'post_reply'=>'您有好友发布了新的作品',
            'invite'=>'您有一条新的求p邀请'
        );
        */

        return $types[$type];
    }
    
    public static function getPushDataTokensByType($cond) {
        $type = $cond['type'];

        $data = array();
        $data['text'] = self::getPushTextByType($type);
        switch($type){
        case 'comment_comment':
            $comment_id = $cond['comment_id'];
            $mComment   = new mComment;
            $target     = $mComment->get_comment_by_id($for_comment);
            break;
        case 'invite':
            $uid = $cond['uid'];
            $data['token']  = sUserDevice::getUserDeviceToken($uid);
            $data['type']   = mMessage::TYPE_INVITE;
            break;
        case 'follow':
            break;
        case 'post_reply':
            $uids   = array();
            $ask_id = $cond['ask_id'];
            $ask    = sAsk::getAskById($ask_id);
            $uids[] = $ask->uid;

            $focuses        = sFocus::getFocusesByAskId($ask_id);
            foreach($focuses as $focus) {
                $uids[] = $focus->uid;
            }
            $data['token']  = sUserDevice::getUsersDeviceTokens($uids);

            break;
        default:
            break;
        }

        return $data;
    }
}

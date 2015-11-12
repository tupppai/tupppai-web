<?php namespace App\Jobs;

use Illuminate\Contracts\Bus\SelfHandling;
use App\Facades\Umeng;

use App\Services\UserDevice as sUserDevice,
    App\Services\Push as sPush,
    App\Services\Follow as sFollow,
    App\Services\Focus as sFocus,
    App\Services\User as sUser,
    App\Services\Ask as sAsk,
    App\Services\Message as sMessage;

use App\Models\Message as mMessage;

class Push extends Job
{
    public $condition   = array();

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($condition)
    {
        #参数
        #condition: [uid, target_uid, type, xxxx_id]
        $this->condition     = $condition;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        #todo push switch
        #todo switch type token list
        $data = self::getPushDataTokensByType($this->condition);
        if( !$this->condition['uid'] ) {
            $this->delete();
            return false;
        }
        //pr($data);

        if( empty($data) ){
            return false;
        }

        $custom = array(
            'type'=>$data['type'],
            'count'=>1
        );

        //umeng push
        $ret = Umeng::push($data, $custom);
        if($ret !== true) {
            sPush::addNewPush($data['type'], 'error:'.$ret);
            $this->delete();
        }

        //record push message
        $data = array_merge($this->condition, $data);
        sPush::addNewPush($data['type'], json_encode($data));
    }

    public static function getPushDataTokensByType($cond) {
        $data = array();
        $type = $cond['type'];

        switch($type){
        case 'like_ask':
        case 'like_reply':
            $data['token']  = sUserDevice::getUserDeviceToken($cond['target_uid']);
            $data['type']   = mMessage::MSG_LIKE;
            break;
        case 'comment_comment':
        case 'comment_ask':
        case 'comment_reply':
            #$comment_id = $cond['comment_id'];
            #$target     = sComment::getCommentById($cond['for_comment']);
            #$data['token']  = sUserDevice::getUserDeviceToken($target->uid);

            $data['token']  = sUserDevice::getUserDeviceToken($cond['target_uid']);
            $data['type']   = mMessage::MSG_COMMENT;
            break;
        case 'invite':

            $data['token']  = sUserDevice::getUserDeviceToken($cond['target_uid']);
            $data['type']   = mMessage::MSG_INVITE;
            break;
        case 'follow':

            $data['token']  = sUserDevice::getUserDeviceToken($cond['target_uid']);
            $data['type'] = mMessage::MSG_FOLLOW;
            break;
        case 'post_reply':
            $uid = $cond['uid'];

            $ask_id = $cond['ask_id'];
            $ask    = sAsk::getAskById($ask_id);
            #$uids[] = $ask->uid;

            $uids   = array();
            $focuses        = sFocus::getFocusesByAskId($ask_id);
            foreach($focuses as $focus) {
                $uids[] = $focus->uid;
            }
            $fans = sFollow::getUserFansByUid($uid);
            foreach($fans as $u) {
                $uids[] = $u->uid;
            }

            $data['token']  = sUserDevice::getUsersDeviceTokens($uids, $uid);
            $data['type']   = mMessage::MSG_REPLY;
            break;
        case 'post_ask':
            #todo:关注的人收到
            $uid    = $cond['uid'];

            $uids       = array();
            $fans       = sFollow::getUserFansByUid($uid);
            foreach($fans as $u) {
                $uids[] = $u->uid;
            }

            $data['token']  = sUserDevice::getUsersDeviceTokens($uids, $uid);
            $data['type']   = mMessage::MSG_ASK;
        case 'new_to_app':
            $data['token']  = sUserDevice::getUserDeviceToken($cond['uid']);
            $data['type']   = mMessage::MSG_SYSTEM;
        default:
            break;
        }
        $data['text'] = self::getPushTextByType($cond['uid'], $type);

        return $data;
    }

    public static function getPushTextByType($uid, $type) {
        $user = sUser::getUserByUid($uid);
        if(!$user) return '';
        $name = $user->nickname;

        $types = array(
             'comment_comment'=>':username:回复了你一条评论',
             'comment_reply'=>':username:评论了你的作品',
             'comment_ask'=>':username:评论了你的求助',
             'like_comment'=>':username:赞了你的评论',
             'like_reply'=>':username:赞了你的作品',
             'like_ask'=>':username:赞了你的求助',
             'inform_comment'=>'你发的评论被举报了',
             'inform_reply'=>'你发的作品被举报了',
             'inform_ask'=>'你发的求助被举报了',
             'focus_ask'=>'关注求助',
             'collect_reply'=>'收藏作品',
             'follow'=>':username:关注了你',
             'unfollow'=>'有好友取消了对你的关注',
             'post_ask'=>'你的关注:username:发布了新的求助',
             'post_reply'=>'你的关注:username:发布了新的作品',
             'ask_reply'=>'你发布的求助有新的作品',
             'invite'=>':username:向你发送了求助邀请',
             'new_to_app' => '欢迎:username:使用图派app'
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
        $str = $types[$type];
        return str_replace(':username:', $name, $str);
    }
}

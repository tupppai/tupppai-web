<?php namespace App\Jobs;

use Illuminate\Contracts\Bus\SelfHandling;
use App\Facades\Umeng;

use App\Services\UserDevice as sUserDevice,
    App\Services\Push as sPush,
    App\Services\Follow as sFollow,
    App\Services\Focus as sFocus,
    App\Services\SysMsg as sSysMsg,
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
        echo " begin push -----\n";
        if( !$this->condition['uid'] ) {
            $this->delete();
            return false;
        }
        $data = self::getPushDataTokensByType($this->condition);

        if( empty($data) ){
            $this->delete();
            return false;
        }

        $custom = array(
            'type'=>$data['type'],
            'count'=>1
        );

        foreach($this->condition as $key=>$val) {
            echo "\t $key: ".json_encode($val)." \n";
        }
        echo "\t ".$data['text']."\n";
        echo "\t ".json_encode($data['token'])."\n";
        echo " end push =======\n\n\n";
        //umeng push
        $ret = Umeng::push($data, $custom);
        if($ret !== true) {
            echo "\t error: ".json_encode($ret)."\n";
            sPush::addNewPush($data['type'], 'error:'.$ret);
            $this->delete();
            return false;
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
            $data['token']  = sUserDevice::getUserDeviceToken($cond['target_uid'], mMessage::PUSH_TYPE_LIKE);
            $data['type']   = mMessage::MSG_LIKE;
            break;
        case 'comment_comment':
        case 'comment_ask':
        case 'comment_reply':
            #$comment_id = $cond['comment_id'];
            #$target     = sComment::getCommentById($cond['for_comment']);
            #$data['token']  = sUserDevice::getUserDeviceToken($target->uid);

            $data['token']  = sUserDevice::getUserDeviceToken($cond['target_uid'], mMessage::PUSH_TYPE_COMMENT);
            $data['type']   = mMessage::MSG_COMMENT;
            break;
        case 'invite':

            $data['token']  = sUserDevice::getUserDeviceToken($cond['target_uid'], mMessage::PUSH_TYPE_INVITE);
            $data['type']   = mMessage::MSG_INVITE;
            break;
        case 'follow':

            $data['token']  = sUserDevice::getUserDeviceToken($cond['target_uid'], mMessage::PUSH_TYPE_FOLLOW);
            $data['type'] = mMessage::MSG_FOLLOW;
            break;
        case 'ask_reply':

            $ask_id = $cond['ask_id'];
            $ask    = sAsk::getAskById($ask_id);
            $data['token']  = sUserDevice::getUserDeviceToken($ask->uid, mMessage::PUSH_TYPE_REPLY);

            $data['type']   = mMessage::MSG_REPLY;
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
            break;
        case 'new_to_app':
            $data['token']  = sUserDevice::getUserDeviceToken($cond['uid']);
            $data['type']   = mMessage::MSG_SYSTEM;
            break;
        case 'sys_msg':
            $sys_msg = sSysMsg::getSystemMessageById($cond['sys_msg_id']);
            if( !$sys_msg && $sys_msg->status > mMessage::STATUS_DELETED )
                return array();
            //todo: 确定传入参数
            $data['token']  = sUserDevice::getUsersDeviceTokens($cond['uids'],1);
            $data['type']   = mMessage::MSG_SYSTEM;
            break;
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
             'comment_comment' => ':username:回复了你。',
             'comment_reply'   => ':username:评论了你的作品。',
             'comment_ask'     => ':username:评论了你的求P。',

             'like_comment'    => ':username:赞了你的评论。',
             'like_reply'      => ':username:赞了你的作品。',
             'like_ask'        => ':username:赞了你的求助。',

             'inform_comment'  => '你发的评论被举报了。',
             'inform_reply'    => '你发的作品被举报了。',
             'inform_ask'      => '你发的求助被举报了。',

             'focus_ask'       => '关注求助',
             'collect_reply'   => '收藏作品',

             'follow'          => ':username:关注了你。',
             'unfollow'        => '有好友取消了对你的关注。',

             'post_ask'        => '你关注的:username:发布了新的求助。',
             'post_reply'      => '你关注的:username:发布了新的作品。',

             'ask_reply'       => '你发布的求助有新的作品。',

             'invite'          => ':username:向你发送了求助邀请。',

             'new_to_app'      => '欢迎:username:使用图派app。',
             'sys_msg'         => '您有新系统消息。'
        );

        $str = $types[$type];
        return str_replace(':username:', $name, $str);
    }
}

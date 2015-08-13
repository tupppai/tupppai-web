<?php namespace App\Jobs;

use Illuminate\Contracts\Bus\SelfHandling;
use App\Facades\Umeng;

use App\Services\UserDeivce as sUserDevice,
    App\Services\Push as sPush,
    App\Services\Message as sMessage;

class Push extends Job 
{
    public $cond   = array();

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($cond)
    {
        #参数
        $this->cond     = $cond;
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
        $data = sPush::getPushDataTokensByType($this->cond);
        if( empty($data) ){
            return false;
        }
        $type = $this->cond['type'];

        $custom = array(
            'type'=>$type,
            'count'=>1
        );

        //umeng push
        Umeng::push($data, $custom);
        //record push message
        $data = array_merge($this->cond, $data);
        sPush::addNewPush($type, json_encode($data));
    }
    
    public function getPushTextByType($type) {
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

        return $types[$type];
    }
    
    public function getPushDataTokensByType($cond) {
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

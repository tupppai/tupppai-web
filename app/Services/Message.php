<?php namespace App\Services;

use App\Services\Ask as sAsk;
use App\Services\User as sUser;
use App\Services\Reply as sReply;
use App\Services\Follow as sFollow;
use App\Services\Invitation as sInvitation;
use App\Services\SysMsg as sSysMsg;
use App\Services\Comment as sComment;
use App\Services\Usermeta as sUsermeta;
use App\Services\ActionLog as sActionLog;
use App\Services\Count as sCount;
use App\Services\Upload as sUpload;

use App\Models\Message as mMessage;
use App\Models\Usermeta as mUsermeta;

use Log;

class Message extends ServiceBase
{
    protected static $msgtype = array(
        'system' => mMessage::MSG_SYSTEM,
        'comment' => mMessage::MSG_COMMENT,
        'follow' => mMessage::MSG_FOLLOW,
        'reply' => mMessage::MSG_REPLY,
        'invite' => mMessage::MSG_INVITE,
        'like' => mMessage::MSG_LIKE
    );

    protected static function newMsg( $sender, $receiver, $content, $msg_type, $target_type = NULL, $target_id = NULL, $update_time = NULL){
        if( $sender == $receiver ){
            return true;
            //return error('RECEIVER_SAME_AS_SENDER');
		}
        sActionLog::init( 'NEW_MESSAGE' );
		$msg = new mMessage();
		$msg->sender    = $sender;
		$msg->receiver  = $receiver;
		$msg->content   = $content;
		$msg->msg_type  = $msg_type;
		$msg->status    = mMessage::STATUS_NORMAL;
		$msg->target_id = $target_id;
        $msg->target_type = $target_type;
        if($update_time) {
            $msg->create_time = $update_time;
            $msg->update_time = $update_time;
        }
		$m =  $msg->save();

        sActionLog::save( $m );
        return $m;
    }

    /**
     * return the amount of each type of message.
     */
    public static function fetchNewMessages( $uid ) {
        $msgAmounts = array();

        $msgAmounts['comment'] = self::fetchNewCommentMessages( $uid );
        $msgAmounts['reply']  = self::fetchNewReplyMessages( $uid );
        $msgAmounts['follow'] = self::fetchNewFollowMessages( $uid );
        $msgAmounts['invite'] = self::fetchNewInviteMessages( $uid );
        $msgAmounts['system'] = self::fetchNewSystemMessages( $uid );
        $msgAmounts['like']   = self::fetchNewLikeMessages( $uid );

        return $msgAmounts;
    }

    public static function fetchNewLikeMessages( $uid ) {
        $last_fetch_msg_time = sUsermeta::get( $uid, mUsermeta::KEY_LAST_READ_LIKE, 0 );
        $unreadLike = sCount::getUnreadLikes( $uid, $last_fetch_msg_time );

        //insert
        foreach( $unreadLike as $like ){
            if( $like->uid != $uid ){
                self::newReplyLike( $like->uid, $uid, '', $like->target_id, $like->update_time);
            }
        }
        $amount = count( $unreadLike );

        //update time
        sUsermeta::writeUserMeta( $uid, mUsermeta::KEY_LAST_READ_LIKE, time() );

        return $amount;
    }

    public static function fetchNewCommentMessages( $uid ) {
        $last_fetch_msg_time = sUsermeta::get( $uid, mUsermeta::KEY_LAST_READ_COMMENT, 0 );
        $unreadComment = sComment::getUnreadComments( $uid, $last_fetch_msg_time );

        //insert
        foreach( $unreadComment as $comment ){
            if( $comment->uid != $uid ){
                self::newComment( $comment->uid, $uid, $comment->content, $comment->id, $comment->update_time);
            }
        }
        $amount = count( $unreadComment );

        //update time
        sUsermeta::writeUserMeta( $uid, mUsermeta::KEY_LAST_READ_COMMENT, time() );

        return $amount;
    }

    public static function fetchNewReplyMessages( $uid ){
        $amount = 0;
        $last_fetch_msg_time = sUsermeta::get( $uid, mUsermeta::KEY_LAST_READ_REPLY , 0 );
        $newReplies = sReply::getNewReplies( $uid, $last_fetch_msg_time );

        //insert
        foreach( $newReplies as $reply ) {
            if( $reply->uid != $uid ) {
                self::newReply( $reply->uid, $uid, $reply->uid.'has replied.', $reply->id, $reply->update_time);
            }
        }
        $amount = count( $newReplies );

        //update
        sUsermeta::writeUserMeta( $uid, mUsermeta::KEY_LAST_READ_REPLY, time() );

        return  $amount;
    }

    public static function fetchNewFollowMessages( $uid ){
        $amount = 0;
        $last_fetch_msg_time = sUsermeta::get( $uid, mUsermeta::KEY_LAST_READ_FOLLOW, 0 );
        $newFollowers = sFollow::getNewFollowers( $uid, $last_fetch_msg_time );

        foreach( $newFollowers as $follower ){
            if( $follower->uid != $uid ){
                self::newFollower( $follower->uid, $uid, $follower->uid.' has followed you.', $follower->uid, $follower->update_time);
            }
        }

        $amount = count( $newFollowers );
        //update
        sUsermeta::writeUserMeta( $uid, mUsermeta::KEY_LAST_READ_FOLLOW, time() );

        return $amount;

    }
    public static function fetchNewInviteMessages( $uid ){
        $amount = 0;
        $last_fetch_msg_time = sUsermeta::get( $uid, mUsermeta::KEY_LAST_READ_INVITE, 0 );
        $newInvitations = sInvitation::getNewInvitations( $uid, $last_fetch_msg_time );

        foreach( $newInvitations as $invitation ){
            if( $invitation->asker->uid != $uid ){
                self::newInvitation( $invitation->asker->uid, $uid, $invitation->asker->uid.'has invited you.', $invitation->ask_id );
            }
        }

        $amount = count( $newInvitations );
        //update
        sUsermeta::writeUserMeta( $uid, mUsermeta::KEY_LAST_READ_INVITE, time() );
        return $amount;
    }
    public static function fetchNewSystemMessages( $uid ){
        $amount = 0;
        $last_fetch_msg_time = sUsermeta::get( $uid, mUsermeta::KEY_LAST_READ_NOTICE, 0);
        $newSystemMessages = sSysMsg::getNewSysMsg( $uid, $last_fetch_msg_time );

        foreach( $newSystemMessages as $sysmsg ){
            $target_type = mMessage::MSG_SYSTEM ;// !$sysmsg->target_type ? mMessage::MSG_SYSTEM : $sysmsg->target_type;
            $target_id = $sysmsg->id ;//!$sysmsg->target_id ? $sysmsg->id : $sysmsg->target_id;
            self::newSystemMsg( $sysmsg->update_by, $uid, '', $target_type, $target_id, $sysmsg->update_time);
        }

        $amount = count( $newSystemMessages );
        sUsermeta::writeUserMeta( $uid, mUsermeta::KEY_LAST_READ_NOTICE, time() );
        return $amount;
    }

    public static function getMessages( $uid, $type, $page, $size) {
        self::fetchNewMessages( $uid );
        $messages = array();

        $mMsg = new mMessage();
        $msgs = $mMsg->get_messages( $uid, $type, $page, $size);

        foreach($msgs as $msg) {
            $messages[] = self::brief($msg);
        }

        return $messages;
    }

    public static function brief($msg){
        $user = sUser::getUserByUid($msg->sender);

        switch( $msg->msg_type ){
        case mMessage::MSG_COMMENT:
            $t = self::detail($msg);

            $comment = sComment::getCommentById($msg->target_id);
            $t['content']    = shortname_to_unicode($comment->content);
            $t['comment_id'] = $comment->id;
            $t['for_comment']= $comment->for_comment;

            $t['target_type']= $comment->type;
            $t['target_id']  = $comment->target_id;

            if($comment->type == mMessage::TYPE_ASK) {
                $model = sAsk::getAskById($comment->target_id);
                $upload_ids = explode(',', $model->upload_ids);
                $t['pic_url'] = sUpload::getImageUrlById($upload_ids[0]);

                $t['target_ask_id'] = $model->id;
            }
            else if($comment->type == mMessage::TYPE_REPLY) {
                $model = sReply::getReplyById($comment->target_id);
                $t['pic_url'] = sUpload::getImageUrlById($model->upload_id);
                
                $t['target_ask_id'] = $model->ask_id;
            }
            $t['desc']    = shortname_to_unicode($model->desc);

            if($comment->for_comment) {
                //$t['pic_url'] = null;
                $for_comment  = sComment::getCommentById($comment->for_comment);
                $t['desc']    = shortname_to_unicode($for_comment->content);
            }
            break;
        case mMessage::MSG_FOLLOW:
            $t = self::detail($msg);
            $t['content'] = '关注了你';
            break;
        case mMessage::MSG_LIKE:
            $t = self::detail($msg);

            $reply = sReply::getReplyById($msg->target_id);
            $t['target_ask_id'] = $reply->ask_id;
            $t['pic_url'] = sUpload::getImageUrlById($reply->upload_id);

            $t['content'] = '点赞了你的照片';
            break;
        case mMessage::MSG_REPLY:
            $t = self::detail($msg);
            $reply = sReply::getReplyById($msg->target_id);
            $t['pic_url'] = sUpload::getImageUrlById($reply->upload_id);
            $t['reply_id']  = $reply->id;
            $t['ask_id']    = $reply->ask_id;
            $t['content'] = '处理了你的照片';
            //$t['reply'] = sReply::detail($reply);
            break;
        case mMessage::MSG_INVITE:
            $t = self::detail($msg);
            $ask = sAsk::getAskById($msg->target_id);
            $t['pic_url'] = sUpload::getImageUrlById($ask->upload_id);
            $t['ask_id']    = $ask->id;
            $t['reply_id']  = '';
            $t['content'] = '邀请你帮忙p图';
            //$t['ask'] = sAsk::detail($ask);
            break;
        case mMessage::MSG_SYSTEM:
            $t = self::systemDetail( $msg );
            $t['msg_type'] = mMessage::MSG_SYSTEM;
            break;
        default:
            return error('WRONG_MESSAGE_TYPE','cuo wu de xiaoxi leixing');
        }
        $t['type'] = $t['msg_type'];
        unset($t['msg_type']);

        return $t;
    }

    public static function detail( $msg ){
        $data = array();
        $data['id'] = $msg->id;
        //$data['receiver'] = $msg->receiver;
        $data['sender']     = $msg->sender;
        $data['msg_type']   = $msg->msg_type;
        $data['content']    = $msg->content;
        $data['update_time']= $msg->update_time;
        $data['pic_url']    = '';

        $sender = sUser::brief( sUser::getUserByUid( $msg->sender ) );
        $data['nickname'] = shortname_to_unicode($sender['nickname']);
        $data['avatar']   = $sender['avatar'];
        $data['sex']      = $sender['sex'];

        $data['target_type'] = $msg->target_type;
        $data['target_id']   = $msg->target_id;
        $data['target_ask_id']  = 0;
        return $data;
    }

    public static function commentDetail( $msg ){
        $sender = sUser::brief( sUser::getUserByUid( $msg->sender ));
        $temp['comment']   = array_merge( self::detail( $msg ), $sender );

		if( $msg->comment->type == mMessage::TYPE_ASK ) {
            $ask_id = $msg->comment->target_id;
        }
        else if( $msg->comment->type == mMessage::TYPE_REPLY ) {
	    	$reply = sReply::getReplyById( $msg->comment->target_id );
			$ask_id = $reply['ask_id'];
        }

        $ask = sAsk::detail( sAsk::getAskById( $ask_id ) );
        $publisher = sUser::getUserByUid( $ask['uid'] );
        $user = [
            'avatar' => $publisher['avatar'],
            'nickname' => shortname_to_unicode($publisher['nickname']),
            'sex' => $publisher['sex']
        ];
        $temp['ask'] = array_merge( $ask, $user );

        return $temp;
    }

    public static function followDetail( $msg ){
        return self::detail( $msg );
    }

    public static function replyDetail( $msg ){
        $temp = array();
        $temp['reply'] = self::detail( $msg );
        $temp['ask'] = sAsk::detail( sAsk::getAskById( $msg->reply->ask_id));
        return $temp;
    }

    public static function inviteDetail( $msg ){
        $temp =array();
        $temp['ask'] = sAsk::detail( $msg->invite );
        $inviter = sUser::getUserByUid( $temp['ask']['uid'] );
        $temp['inviter'] = [
            'uid' => $inviter['uid'],
            'nickname' => $inviter['nickname'],
            'avatar' => $inviter['avatar'],
            'sex' => $inviter['sex'],
            'create_time' => $msg->create_time,
            'update_time' => $msg->update_time,
            'id' => $msg->id
        ];
        return $temp;
    }

    public static function systemDetail( $msg ){
        $temp = array();
        $temp['id'] = $msg->id;
        $temp['sender'] = $msg->sender;
        $temp['update_time'] = $msg->update_time;
        if( $msg->sender == 0 ){
            $temp['username'] = '图派小助手';
            //$temp['avatar'] = 'http://'.env('ANDROID_HOST').'/theme/img/avatar.jpg';
            $temp['avatar'] = 'http://7u2spr.com1.z0.glb.clouddn.com/20151111-1134205642b73c02a82.png';
        }
        else{
            $sender = sUser::getUserByUid( $msg->sender );
            $temp['username'] = $sender['username'];
            $temp['avatar'] = $sender['avatar'];
        }

        switch( $msg->msg_type ){
            case mMessage::MSG_ASK:
                $ask = sAsk::getAskById( $msg->target_id );
                $temp['pic_url'] = $ask['image_url'];
                break;
            case mMessage::MSG_REPLY:
                $reply =sReply::getReplyById( $msg->target_id );
                $temp['pic_url'] = $reply['image_url'];
                break;
            case mMessage::MSG_SYSTEM:
                $sysmsg = sSysMsg::getSystemMessageById( $msg->target_id );
                $temp['jump_url'] = $sysmsg->jump_url;
                $temp['target_type'] = $sysmsg->target_type;
                $temp['target_id'] = $sysmsg->target_id;
                $temp['pic_url'] = $sysmsg->pic_url;
                $content = [];
                switch( $sysmsg->target_type ){
                    case mMessage::TYPE_ASK:
                        $ask = sAsk::getAskById( $sysmsg->target_id );
                        $content = sAsk::detail( $ask );
                        $temp['pic_url'] = $content['ask_uploads'][0]['image_url'];
                        break;
                    case mMessage::TYPE_REPLY:
                        $reply = sReply::getReplyById( $sysmsg->target_id );
                        $content = sReply::detail( $reply );
                        $temp['pic_url'] = $content['ask_uploads'][0]['image_url'];
                        break;
                    case  mMessage::TYPE_COMMENT:
                        $comment = sComment::getCommentById( $sysmsg->target_id );
                        $content = sComment::detail( $comment );
                        break;
                    case mMessage::TYPE_USER:
                        $user = sUser::getUserByUid( $sysmsg->target_id );
                        $content = sUser::detail( $user );
                        $temp['pic_url'] = $content['avatar'];
                        break;
                    default:
                        $content = [];
                        break;
                }
                $temp['target_content'] = $content;

                $temp['content'] = shortname_to_unicode($sysmsg->title);
                break;
            default:
               break;
        }
        return $temp;
    }


    public static function deleteMessagesByType( $uid, $type ){
        //todo::actionlog
        $mMsg = new mMessage();
        $type = self::$msgtype[$type];
        return $mMsg->delete_messages_by_type( $uid, $type );
    }

    public static function deleteMessagesByMessageIds( $uid, $mids ){
        //todo::actionlog
        $mMsg = new mMessage();
        return $mMsg->delete_messages_by_mids( $uid, $mids );
    }

    /**
     * deprecated
     * delete messages
     */
    public static function delMsgs( $uid, $mids ){
        $mids = implode(',',array_filter(explode(',', $mids)));
        if( empty($mids) ){
            return error('EMPTY_MESSAGE_ID');
        }

        $msgs = mMessage::find('receiver='.$uid.' AND id IN('.$mids.')');
        return $msgs->delete();
    }

	public static function newReply( $sender, $receiver, $content, $target_id , $update_time = null){
        return self::newMsg(
            $sender,
            $receiver,
            $content,
            mMessage::MSG_REPLY,
            mMessage::TYPE_REPLY,
            $target_id,
            $update_time
        );
    }

	public static function newSystemMsg( $sender, $receiver, $content, $target_type = '', $target_id = '', $update_time = null ){
        return self::newMsg(
            $sender,
            $receiver,
            $content,
            mMessage::MSG_SYSTEM,
            $target_type,
            $target_id,
            $update_time
        );
    }

	public static function newFollower( $sender, $receiver, $content, $target_id, $update_time ){
        return self::newMsg(
            $sender,
            $receiver,
            $content,
            mMessage::MSG_FOLLOW,
            mMessage::TYPE_USER,
            $target_id,
            $update_time
        );
    }

	public static function newComment( $sender, $receiver, $content, $target_id, $update_time ){
        return self::newMsg(
            $sender,
            $receiver,
            $content,
            mMessage::MSG_COMMENT,
            mMessage::TYPE_COMMENT,
            $target_id,
            $update_time
        );
    }

	public static function newInvitation( $sender, $receiver, $content, $ask_id, $update_time ){
        return self::newMsg(
            $sender,
            $receiver,
            $content,
            mMessage::MSG_INVITE,
            mMessage::TYPE_ASK,
            $ask_id,
            $update_time
        );
    }

    /**
     * new like
     */
    public static function newReplyLike( $sender, $receiver, $content, $target_id, $update_time){
        return self::newMsg(
            $sender,
            $receiver,
            $content,
            mMessage::MSG_LIKE,
            mMessage::TYPE_REPLY,
            $target_id,
            $update_time
        );
    }
}

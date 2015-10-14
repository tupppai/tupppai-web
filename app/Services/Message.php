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
use App\Services\Upload as sUpload;

use App\Models\Message as mMessage;
use App\Models\Usermeta as mUsermeta;

use Log;

class Message extends ServiceBase
{
    protected static $msgtype = array(
        'system' => mMessage::TYPE_SYSTEM,
        'comment' => mMessage::TYPE_COMMENT,
        'follow' => mMessage::TYPE_FOLLOW,
        'reply' => mMessage::TYPE_REPLY,
        'invite' => mMessage::TYPE_INVITE,
        'like' => mMessage::TYPE_LIKE
    );
    
    protected static function newMsg( $sender, $receiver, $content, $msg_type, $target_type = NULL, $target_id = NULL ){
        if( $sender == $receiver ){
            return error('RECEIVER_SAME_AS_SENDER');
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
		$msg->create_time = time();
		$msg->update_time = time();
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
        $msgAmounts['reply'] = self::fetchNewReplyMessages( $uid );
        $msgAmounts['follow'] = self::fetchNewFollowMessages( $uid );
        $msgAmounts['invite'] = self::fetchNewInviteMessages( $uid );
        $msgAmounts['system'] = self::fetchNewSystemMessages( $uid );

        return $msgAmounts;
    }

    public static function fetchNewCommentMessages( $uid ) {
        $amount = 0;
        $last_fetch_msg_time = sUsermeta::get( $uid, mUsermeta::KEY_LAST_READ_COMMENT, 0 );
        $unreadComment = sComment::getUnreadComments( $uid, $last_fetch_msg_time );

        //insert
        foreach( $unreadComment as $comment ){
            if( $comment->uid != $uid ){
                self::newComment( $comment->uid, $uid, $comment->content, $comment->id );
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
        foreach( $newReplies as $reply ){
            if( $reply->uid != $uid ){
                self::newReply( $reply->uid, $uid, $reply->uid.'has replied.', $reply->id );
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
                self::newFollower( $follower->uid, $uid, $follower->uid.' has followed you.', $follower->uid );
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
            $target_type = !$sysmsg->target_type ? mMessage::TYPE_SYSTEM : $sysmsg->target_type;
            $target_id = !$sysmsg->target_id ? $sysmsg->id : $sysmsg->target_id;
            self::newSystemMsg( $sysmsg->update_by, $uid, 'u has an system message.', $target_type, $target_id );
        }

        $amount = count( $newSystemMessages );
        sUsermeta::writeUserMeta( $uid, mUsermeta::KEY_LAST_READ_NOTICE, time() );
        return $amount;
    }

    public static function getMessages( $uid, $page, $size, $last_updated) {
        self::fetchNewMessages( $uid );

        $mMsg = new mMessage();
        $msgs = $mMsg->get_messages( $uid, $page, $size, $last_updated);

        foreach($msgs as $msg) {
            $messages[] = self::brief($msg); 
        }

        return $messages;
    }

    public static function brief($msg){ 
        $user = sUser::getUserByUid($msg->sender);

        switch( $msg->msg_type ){
        case mMessage::TYPE_COMMENT:
            $t = self::detail($msg);
            $comment = sComment::getCommentById($msg->target_id);
            if($comment->type == mMessage::TYPE_ASK) {
                $ask = sAsk::getAskById($comment->target_id);
                $t['pic_url'] = sUpload::getImageUrlById($ask->upload_id);
            }
            else if($comment->type == mMessage::TYPE_REPLY) {
                $reply = sReply::getReplyById($comment->target_id);
                $t['pic_url'] = sUpload::getImageUrlById($reply->upload_id);
            }
            break;
        case mMessage::TYPE_FOLLOW:
            $t = self::detail($msg);
            break;
        case mMessage::TYPE_LIKE:
            $t = self::detail($msg);
            break;
        case mMessage::TYPE_REPLY:
            $t = self::detail($msg);
            $reply = sReply::getReplyById($msg->target_id);
            $t['pic_url'] = sUpload::getImageUrlById($reply->upload_id);
            //$t['reply'] = sReply::detail($reply);
            break;
        case mMessage::TYPE_INVITE:
            $t = self::detail($msg);
            $ask = sAsk::getAskById($msg->target_id);
            $t['pic_url'] = sUpload::getImageUrlById($ask->upload_id);
            //$t['ask'] = sAsk::detail($ask);
            break;
        case mMessage::TYPE_SYSTEM:
            $t = self::systemDetail( $msg, $uid );
            break;
        default:
            return error('WRONG_MESSAGE_TYPE','cuo wu de xiaoxi leixing');
        }

        return $t;
    }

    public static function getMessagesByType( $uid, $type, $page, $size, $last_updated ){
        self::fetchNewMessages( $uid );
        $mMsg = new mMessage();
        $msgs = array();
        $messages = array();
        $type = self::$msgtype[$type];
        switch( $type ){
            case mMessage::TYPE_COMMENT:
                $msgs = $mMsg->get_comment_messages( $uid, $page, $size, $last_updated );
                foreach( $msgs as $msg ){
                    $messages[] = self::commentDetail( $msg, $uid );
                }
                break;
            case mMessage::TYPE_FOLLOW:
                $msgs = $mMsg->get_follow_messages( $uid, $page, $size, $last_updated );
                foreach( $msgs as $msg ){
                    $messages[] = self::followDetail( $msg, $uid );
                }
                break;
            case mMessage::TYPE_REPLY:
                $msgs = $mMsg->get_reply_message( $uid, $page, $size, $last_updated );
                foreach( $msgs as $msg ){
                    $messages[] = self::replyDetail( $msg, $uid );
                }
                break;
            case mMessage::TYPE_INVITE:
                $msgs = $mMsg->get_invite_message( $uid, $page, $size, $last_updated );
                foreach( $msgs as $msg ){
                    $messages[] = self::inviteDetail( $msg, $uid );
                }
                break;
            case mMessage::TYPE_SYSTEM:
                $msgs = $mMsg->get_system_message( $uid, $page, $size, $last_updated );
                foreach( $msgs as $msg ){
                    $messages[] = self::systemDetail( $msg, $uid );
                }

                break;
            default:
                return error('WRONG_MESSAGE_TYPE','cuo wu de xiaoxi leixing');
        }

        return $messages;
    }
    
    public static function detail( $msg ){
        $data = array();
        $data['id'] = $msg->id;
        $data['receiver'] = $msg->receiver;
        $data['sender'] = $msg->sender;
        $data['msg_type'] = $msg->msg_type;
        $data['content'] = $msg->content;
        $data['update_time'] = $msg->update_time;
        $data['target_type'] = $msg->target_type;
        $data['target_id'] = $msg->target_id;
        $data['pic_url']   = ''; 

        $sender = sUser::brief( sUser::getUserByUid( $msg->sender ) );
        $data['nickname'] = $sender['nickname'];
        $data['avatar']   = $sender['avatar'];
        $data['sex']      = $sender['sex'];    

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
            'nickname' => $publisher['nickname'],
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
        if( $msg->sender === '0' ){
            $temp['username'] = '系统消息';
            $temp['avatar'] = 'http://'.env('PC_HOST').'/img/avatar.jpg';
        }
        else{
            $sender = sUser::getUserByUid( $msg->sender );
            $temp['username'] = $sender['username'];
            $temp['avatar'] = $sender['avatar'];
        }

        switch( $msg->msg_type ){
            case mMessage::TYPE_ASK:
                $ask = sAsk::getAskById( $msg->target_id );
                $temp['pic_url'] = $ask['image_url'];
                break;
            case mMessage::TYPE_REPLY:
                $reply =sReply::getReplyById( $msg->target_id );
                $temp['pic_url'] = $replt['image_url'];
                break;
            case mMessage::TYPE_SYSTEM:
                $sysmsg = sSysMsg::getSystemMessageById( $msg->target_id );
                $temp['jump_url'] = $sysmsg->jump_url;
                $temp['target_type'] = $sysmsg->target_type;
                $temp['target_id'] = $sysmsg->target_id;
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

    /**
     * new messages
     */
	public static function newReply( $sender, $receiver, $content, $target_id ){
        return self::newMsg(
            $sender,
            $receiver,
            $content,
            mMessage::TYPE_REPLY,
            mMessage::TYPE_ASK,
            $target_id
        );
    }

	public static function newSystemMsg( $sender, $receiver, $content, $target_type = '', $target_id = '' ){
        return self::newMsg(
            $sender,
            $receiver,
            $content,
            mMessage::TYPE_SYSTEM,
            $target_type,
            $target_id
        );
    }

	public static function newFollower( $sender, $receiver, $content, $target_id ){
        return self::newMsg(
            $sender,
            $receiver,
            $content,
            mMessage::TYPE_FOLLOW,
            mMessage::TYPE_USER,
            $target_id
        );
    }

	public static function newComment( $sender, $receiver, $content, $target_id ){
        return self::newMsg(
            $sender,
            $receiver,
            $content,
            mMessage::TYPE_COMMENT,
            mMessage::TYPE_COMMENT,
            $target_id
        );
    }

	public static function newInvitation( $sender, $receiver, $content, $ask_id ){
        return self::newMsg(
            $sender,
            $receiver,
            $content,
            mMessage::TYPE_INVITE,
            mMessage::TYPE_ASK,
            $ask_id
        );
    }
}

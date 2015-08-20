<?php namespace App\Services;

use App\Services\Ask as sAsk;
use App\Services\User as sUser;
use App\Services\Reply as sReply;
use App\Models\Message as mMessage;

class Message extends ServiceBase
{
    protected static $msgtype = array(
        'comment' => mMessage::TYPE_COMMENT,
        'follow' => mMessage::TYPE_FOLLOW,
        'reply' => mMessage::TYPE_REPLY,
        'invite' => mMessage::TYPE_INVITE,
        'system' => mMessage::TYPE_SYSTEM
    );


    public static function getMessagesByType( $uid, $type, $page, $size, $last_updated ){
        $mMsg = new mMessage();
        $msgs = array();
        $messages = array();
        $type = self::$msgtype[$type];
        switch( $type ){
            case mMessage::TYPE_COMMENT:
                $msgs = $mMsg->get_comment_messages( $uid, $type, $page, $size, $last_updated );
                foreach( $msgs as $msg ){
                    $messages[] = self::commentDetail( $msg, $uid );
                }
                break;
            case mMessage::TYPE_FOLLOW:
                $msgs = $mMsg->get_follow_messages( $uid, $type, $page, $size, $last_updated );
                foreach( $msgs as $msg ){
                    $messages[] = self::followDetail( $msg, $uid );
                }
                break;
            case mMessage::TYPE_REPLY:
                $msgs = $mMsg->get_messages_by_type( $uid, $type, $page, $size, $last_updated );
                foreach( $msgs as $msg ){
                    $messages[] = self::replyDetail( $msg, $uid );
                }
                break;
            case mMessage::TYPE_INVITE:
                $msgs = $mMsg->get_messages_by_type( $uid, $type, $page, $size, $last_updated );
                foreach( $msgs as $msg ){
                    $messages[] = self::inviteDetail( $msg, $uid );
                }
                break;
            case mMessage::TYPE_SYSTEM:
                $msgs = $mMsg->get_messages_by_type( $uid, $type, $page, $size, $last_updated );
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

        return $data;
    }

    public static function commentDetail( $msg ){
        $temp   = array();
        $sender = sUser::brief( sUser::getUserByUid( $msg->sender ));
	    $temp['comment']   = array_merge( self::detail( $msg ), $sender );

		if( $msg->comment->type == mMessage::TARGET_ASK ) {
            $ask_id = $msg->comment->target_id;
        }
        else if( $msg->comment->type == mMessage::TARGET_REPLY ) {
	    	$reply = sReply::getReplyById( $msg->comment->target_id );
			$ask_id = $reply['ask_id'];
        }

        $temp['ask'] = sAsk::getAskById( $ask_id );

        return $temp;
    }

    public static function followDetail( $msg ){
        $temp = array();
        $sender = sUser::brief( sUser::getUserByUid( $msg->sender ) );
        $temp = [
            'id' => $msg->id,
            'update_time' => $msg->update_time,
            'uid' => $sender['uid'],
            'nickname' => $sender['nickname'],
            'avatar' => $sender['avatar'],
            'sex'=> $sender['sex']    
        ];

        return $temp;
    }





	protected static function newMsg( $sender, $receiver, $content, $msg_type, $target_type = NULL, $target_id = NULL ){
        if( $sender == $receiver ){
            return error('MESSAGE_NOT_EXIST');
		}
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
		return $msg->save();
    }

    public static function delMsgs( $uid, $mids ){
        $mids = implode(',',array_filter(explode(',', $mids)));
        if( empty($mids) ){
            return error('EMPTY_MESSAGE_ID');
        }

        $msgs = mMessage::find('receiver='.$uid.' AND id IN('.$mids.')');
        return $msgs->delete();
    }

	public static function newReply( $sender, $receiver, $content, $target_id ){
        return self::newMsg(
            $sender,
            $receiver,
            $content,
            mMessage::TYPE_REPLY,
            mMessage::TARGET_ASK,
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
            mMessage::TARGET_USER,
            $target_id
        );
    }

	public static function newComment( $sender, $receiver, $content, $target_id ){
        return self::newMsg(
            $sender,
            $receiver,
            $content,
            mMessage::TYPE_COMMENT,
            mMessage::TARGET_COMMENT,
            $target_id
        );
    }

	public static function newInvitation( $sender, $receiver, $content, $target_id ){
        return self::newMsg(
            $sender,
            $receiver,
            $content,
            mMessage::TYPE_INVITE,
            mMessage::TARGET_USER,
            $target_id
        );
    }
}

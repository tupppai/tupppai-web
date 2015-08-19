<?php namespace App\Services;

use App\Models\Message as mMessage;

class Message extends ServiceBase
{
    protected $table = 'messages'; 


    public static function getMessagesByType( $uid, $type, $page, $size, $last_updated ){
        $mMsg = new mMessage();
        $msgs = $mMsg->get_messages_by_type( $uid, $type, $page, $size, $last_updated );
        $messages = array();
        switch( $type ){
            case mMessage::TYPE_COMMENT:
                foreach( $msgs as $msg ){
                    $messages[] = self::commentDetail( $msg, $uid );
                }
                break;
            case mMessage::TYPE_FOLLOW:
                foreach( $msgs as $msg ){
                    $messages[] = self::followDetail( $msg, $uid );
                }
                break;
            case mMessage::TYPE_REPLY:
                foreach( $msgs as $msg ){
                    $messages[] = self::replyDetail( $msg, $uid );
                }

                break;
            case mMessage::TYPE_INVITE:
                foreach( $msgs as $msg ){
                    $messages[] = self::inviteDetail( $msg, $uid );
                }

                break;
            case mMessage::TYPE_SYSTEM:
                foreach( $msgs as $msg ){
                    $messages[] = self::systemDetail( $msg, $uid );
                }

                break;
            default:
                return error('WRONG_MESSAGE_TYPE','cuo wu de xiaoxi leixing');
        }

        return $messages;
    }


    public static function commentDetail( $msg ){
        dd($msg);
        $temp   = array();
	    $temp['comment']   = $msg->toArray();

		if($msg['type']==Message::TARGET_ASK) {
            $ask_id = $msg['target_id'];
        }
        else if($msg['type']==Message::TARGET_REPLY) {
	    	$reply = Reply::findFirst($msg['target_id']);
			$ask_id = $reply->ask_id;
        }

		$ask = Ask::findFirst($ask_id);
        $temp['ask'] = $ask->toStandardArray($uid, $width);

        $data[] = $temp;

    
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

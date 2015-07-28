<?php

namespace Psgod\Services;

use \Psgod\Models\Message as mMessage;

class Message extends ServicesBase
{
    /**
     * 获取推送提示语
     */
    public static function getPushMessage($type = null, $target_type = null) {
        switch($type){
        case self::TYPE_COMMENT:
            $text = "收到一条评论消息";
            break;
        case self::TYPE_REPLY:
            $text = "收到一条作品消息";
            break;
        case self::TYPE_FOLLOW:
            $text = "有新的朋友关注你";
            break;
        case self::TYPE_INVITE:
            $text = "有朋友邀请你帮忙P图";
            break;
        case self::TYPE_SYSTEM:
            $text = "收到一条系统消息";
            break;
        //todo: 缺少相同求助被处理的提醒
        default:
            break;
        }

        return $text;
    }

    public static function delMsgs( $uid, $mids ){
        if( !$uid ){
            return error('EMPTY_UID');
        }
        if( !$mids ){
            return error('EMPTY_MESSAGE_ID');
        }

        $mids = implode(',',array_filter(explode(',', $mids)));
        if( empty($mids) ){
            return error('EMPTY_MESSAGE_ID');
        }

        $msgs = mMessage::find('receiver='.$uid.' AND id IN('.$mids.')');
        return $msgs->delete();
    }

	protected static function newMsg( $sender, $receiver, $content, $msg_type, $target_type = NULL, $target_id = NULL ){
		if( $sender == $receiver ){
			return false;
		}
		$msg = new mMessage();
		$msg -> sender = $sender;
		$msg -> receiver = $receiver;
		$msg -> content = $content;
		$msg -> msg_type = $msg_type;
		$msg -> status = mMessage::STATUS_NORMAL;
		$msg -> target_type = $target_type;
		$msg -> target_id = $target_id;
		$msg -> create_time = time();
		$msg -> update_time = time();
		return $msg -> save_and_return($msg, true);
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

    public function getSource()
    {
        return 'messages';
    }
}

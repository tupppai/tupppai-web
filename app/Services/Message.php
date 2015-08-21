<?php namespace App\Services;

use App\Services\Ask as sAsk;
use App\Services\User as sUser;
use App\Services\Reply as sReply;
use App\Services\SysMsg as sSysMsg;
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

        $sender = sUser::brief( sUser::getUserByUid( $msg->sender ) );
        $data['nickname'] = $sender['nickname'];
        $data['avatar']   = $sender['avatar'];
        $data['sex']      = $sender['sex'];    

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
            'sex' => $inviter['sex']
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

        switch( $msg->target_type ){
            case mMessage::TARGET_ASK:
                $ask = sAsk::getAskById( $msg->target_id );
                $temp['pic_url'] = $ask['image_url'];
                break;
            case mMessage::TARGET_REPLY:
                $reply =sReply::getReplyById( $msg->target_id );
                $temp['pic_url'] = $replt['image_url'];
                break;
            case mMessage::TARGET_SYSTEM:
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

	public static function newInvitation( $sender, $receiver, $content, $ask_id ){
        return self::newMsg(
            $sender,
            $receiver,
            $content,
            mMessage::TYPE_INVITE,
            mMessage::TARGET_ASK,
            $ask_id
        );
    }
}

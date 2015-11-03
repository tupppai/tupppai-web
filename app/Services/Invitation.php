<?php

namespace App\Services;

use App\Services\ActionLog as sActionLog;
use App\Models\Invitation as mInvitation,
    App\Models\Usermeta   as mUsermeta,
    App\Models\Message  as mMessage,
    App\Models\Ask        as mAsk;

use Queue, App\Jobs\Push;

class Invitation extends ServiceBase
{

    public static function checkInvitationOf( $ask_id, $invite_uid ){
        $mInvitation = new mInvitation();
        $inv = $mInvitation->where( [ 'ask_id' => $ask_id, 'invite_uid'=> $invite_uid ] )->first();

        return (bool)$inv;
    }

    private static function sendInvitation( $ask_id, $invite_uid ){
        $invitation = new mInvitation();
        sActionLog::init( 'INVITE_FOR_ASK', $invitation );
        $invitation->assign(array(
            'ask_id'        => $ask_id,
            'invite_uid'    => $invite_uid,
        ));
        $invitation->save();
        sActionLog::save($invitation);
        return $invitation;
    }

    public static function setInvitation($uid, $ask_id, $invite_uid, $status = mInvitation::STATUS_NORMAL) {
        #$invitation->setInvitation( $ask_id, $invite_uid, mInvitation::STATUS_READY );
        $mAsk= new mAsk;

        $ask = $mAsk->get_ask_by_id($ask_id);
        if( !$ask ) {
            return error('ASK_NOT_EXIST');
        }
        //if( $uid != $ask->uid ) {
        //    return error('PERMISSION_DENY');
        //}

        $action_name = 'INVITE_FOR_ASK';
        if( $status == mInvitation::STATUS_DELETED ){
            $action_name = 'CANCEL_'.$action_name;
        }


        $mInvitation = new mInvitation();

        $cond = [
            'ask_id' => $ask_id,
            'invite_uid' => $invite_uid
        ];
        #todo: remove first or new
        $invitation = $mInvitation->firstOrNew( $cond );

        $data = $cond;
        if( !$invitation->id ){
            if( $status == mInvitation::STATUS_DELETED ){
                return true;
            }
            $data['create_time'] = time();
        }

        $data['update_time'] = time();
        $data['status'] = $status;
        $invitation->assign($data)->save();
        #todo: jq 有点看不懂
        sActionLog::init( $action_name, $invitation );
        sActionLog::save($invitation);

        #邀请推送
        Queue::push(new Push(array(
            'uid'=>$uid,
            'target_uid'=>$invite_uid,
            'type'=>'invite'
        )));

        return $invitation;
    }
    
    public static function getNewInvitations( $uid, $last_fetch_msg_time ){
       return (new mInvitation)->get_new_invitations( $uid, $last_fetch_msg_time );
    }

}

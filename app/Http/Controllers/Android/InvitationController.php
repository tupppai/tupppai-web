<?php namespace App\Http\Controllers\Android;

use App\Services\ActionLog as sActionLog,
    App\Services\Invitation as sInvitation;

use App\Models\Message as mMessage;

use App\Jobs\Push as Push;

class InvitationController extends ControllerBase{

    public function inviteAction( ){
        $ask_id     = $this->get('ask_id','int');
        $invite_uid = $this->get('invite_uid', 'int');

        if ( empty( $ask_id) || empty( $invite_uid) ){
            return error('WRONG_ARGUMENTS');
        }
        
        #邀请推送
        $this->dispatch(new Push($invite_uid, array(
            'type'=>mMessage::TYPE_INVITE,
            'count'=>1
        )));

        $invitation = sInvitation::setInvitation( _uid(), $ask_id, $invite_uid );
        return $this->output($invitation,'success');
    }
}


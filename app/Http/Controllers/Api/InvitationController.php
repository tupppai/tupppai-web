<?php namespace App\Http\Controllers\Api;

use App\Services\ActionLog as sActionLog,
    App\Services\Invitation as sInvitation;

use App\Models\Message as mMessage;

class InvitationController extends ControllerBase{

    public function inviteAction( ){
        $ask_id     = $this->get('ask_id','int');
        $invite_uid = $this->get('invite_uid', 'int');

        if ( empty( $ask_id) || empty( $invite_uid) ){
            return error('WRONG_ARGUMENTS');
        }
 
        $invitation = sInvitation::setInvitation( _uid(), $ask_id, $invite_uid );
        return $this->output($invitation,'success');
    }
}


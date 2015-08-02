<?php
namespace Psgod\Android\Controllers;

//use Psgod\Controllers\MasterController;
use Psgod\Services\ActionLog as sActionLog;
use Psgod\Services\Invitation as sInvitation;

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


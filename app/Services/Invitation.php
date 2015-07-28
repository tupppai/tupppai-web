<?php

namespace App\Services;
use \App\Services\ActionLog as sActionLog;

use \App\Models\Invitation as mInvitation;
use \App\Models\Usermeta   as mUsermeta;
use \App\Models\Ask        as mAsk;

class Invitation extends ServiceBase
{
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

    public static function setInvitation($uid, $ask_id, $invite_uid, $status = mInvitation::STATUS_READY) {
        #$invitation->setInvitation( $ask_id, $invite_uid, mInvitation::STATUS_READY );

        $ask = mAsk::findFirst($ask_id);
        if( !$ask ) {
            return error('ASK_NOT_EXIST');
        }
        if( $uid != $ask->uid ) {
            return error('PERMISSION_DENY');
        }

        $action_name = 'INVITE_FOR_ASK';
        if( $status == mInvitation::STATUS_DELETED ){
            $action_name = 'CANCEL_'.$action_name;
        }

        $invModel = new mInvitation();
        $inv = $invModel->getInvitation( $ask_id, $invite_uid, $status);

        if ( $inv && $inv->status == $status ){
            //重复邀请
            return $inv->toArray();
        }

        //状态相同不更改db
        if ( $inv ){
            sActionLog::init( $action_name, $inv );
            $inv->status = $status;
            $inv->save();
            sActionLog::save($inv);
        }
        else {
            $inv = self::sendInvitation( $ask_id, $invite_uid );
        }

        return $inv->toArray();
    }

    /**
     * 通过id获取求助
     */
    public static function getInvitation($ask_id, $invite_id) {
        $inv = mInvitation::findFirst("ask_id=$ask_id and invite_id=$invite_id");
        if( !$inv ){
            return error('INVITATION_NOT_EXIST');
        }

        return $inv;
    }

    public static function updateMsg( $uid, $last_updated ){
        $lasttime = Usermeta::readUserMeta( $uid, Usermeta::KEY_LAST_READ_INVITE);
        $lasttime = $lasttime?$lasttime[Usermeta::KEY_LAST_READ_INVITE]: 0;
        define('INVITATION_MSG_TEXT', 'uid::uid: invites you to help him/her.');


        $invitation = new Invitation();
        $invites = $invitation->get_unread_invitation($uid, $last_updated, $lasttime );

        foreach( $invites as $row){
            Message::newInvitation(
                $row->uid,
                $uid,
                str_replace(array(':uid:'), array($row->uid), INVITATION_MSG_TEXT),
                $row->id);
        }

        if(isset($row)){
            Usermeta::refresh_read_notify(
                $uid,
                Usermeta::KEY_LAST_READ_INVITE,
                $lasttime
            );
        }
        return $invites;
    }

    public static function count_new_invitation($uid){
        $lasttime = Usermeta::readUserMeta( $uid, Usermeta::KEY_LAST_READ_INVITE );
        if( $lasttime ){
            $lasttime = $lasttime[Usermeta::KEY_LAST_READ_INVITE];
        }
        else{
            $lasttime = 0;
        }

        return Invitation::count( array(
            'create_time>'.$lasttime,
            'status='.Invitation::STATUS_NORMAL,
            'invite_uid='.$uid
        ) );
    }

    public static function list_unread_invites( $lasttime, $page = 1, $size = 500 ){

        $invite = new mInvitation;
        $sql = 'select i.invite_uid, count(1) as num'.
            ' FROM invitations i'.
            ' WHERE i.status='.mInvitation::STATUS_NORMAL.
            ' AND i.create_time>'.$lasttime.
            ' GROUP BY i.invite_uid';
        return new Resultset(null, $invite, $invite->getReadConnection()->query($sql));
    }

}

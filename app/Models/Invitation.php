<?php

namespace App\Models;
use \App\Models\Usermeta;
use Phalcon\Mvc\Model\Resultset\Simple as Resultset;

class Invitation extends ModelBase
{
    protected $table = 'invitations';

    public function asker(){
        return $this->belongsTo('\App\Models\Ask','ask_id');
    }

    public function beforeCreate(){
        $this->status      = self::STATUS_NORMAL;
        return $this;
    }

    public function getInvitation( $ask_id, $invite_uid, $status = self::STATUS_READY ){
        $invite = self::where('ask_id', $ask_id)
            ->where('invite_uid', $invite_uid)
            ->where('status', $status)
            ->first();
        return $invite;
    }


    public function updateMsg( $uid, $last_updated ){
        $lasttime = Usermeta::readUserMeta( $uid, Usermeta::KEY_LAST_READ_INVITE);
        $lasttime = $lasttime?$lasttime[Usermeta::KEY_LAST_READ_INVITE]: 0;

        $builder = Invitation::query_builder('i');
        $where = array(
            'i.create_time < '.$last_updated,
            'i.create_time > '.$lasttime,
            'i.status='.Invitation::STATUS_NORMAL,
            'i.invite_uid='.$uid
        );

		$ask = 'App\Models\Ask';
        $res = $builder -> where( implode(' AND ',$where) )
                        -> join($ask, 'a.id=i.ask_id', 'a', 'LEFT')
                        -> columns('a.uid, i.invite_uid, i.id')
                        -> getQuery()
                        -> execute();
        $invites = self::query_page($builder)->items;

        foreach( $invites as $row){
            Message::newInvitation(
                $row->uid,
                $uid,
                'uid:'.$row->uid.' invites you to help him/her.',
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

    public function count_new_invitation($uid){
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

    public function get_new_invitations( $uid, $last_fetch_msg_time ){
        return self::with('asker')
            ->where([
                'invite_uid' => $uid,
                'status' => self::STATUS_NORMAL
            ])
            ->where('update_time','>', $last_fetch_msg_time )
            ->get();
    }

    public function list_unread_invites( $lasttime, $page = 1, $size = 500 ){

        $invite = new self;
        $sql = 'select i.invite_uid, count(1) as num'.
            ' FROM invitations i'.
            ' WHERE i.status='.self::STATUS_NORMAL.
            ' AND i.create_time>'.$lasttime.
            ' GROUP BY i.invite_uid';
        return new Resultset(null, $invite, $invite->getReadConnection()->query($sql));
    }

    public function get_unread_invitation($uid, $last_fetch_time, $last_read_msg_time){
        $builder = this::query_builder('i');
        $where = array(
            'i.create_time < '.$last_fetch_time,
            'i.create_time > '.$last_read_msg_time,
            'i.status='.this::STATUS_NORMAL,
            'i.invite_uid='.$uid
        );

        $res = $builder -> where( implode(' AND ',$where) )
                        -> columns('a.uid, i.invite_uid, i.id')
                        -> getQuery()
                        -> execute();
        return mInvitation::query_page($builder)->items;
    }

}

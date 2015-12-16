<?php

namespace App\Models;
use \App\Models\Usermeta;
use Phalcon\Mvc\Model\Resultset\Simple as Resultset;

class Invitation extends ModelBase
{
    protected $table = 'invitations';
    protected $guarded = ['id'];

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

 
    public function get_new_invitations( $uid, $last_fetch_msg_time ){
        return self::with('asker')
            ->where([
                'invite_uid' => $uid,
                'status' => self::STATUS_NORMAL
            ])
            ->where('create_time','>', $last_fetch_msg_time )
            ->get();
    }
}

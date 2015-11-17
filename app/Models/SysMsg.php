<?php

namespace App\Models;

class SysMsg extends ModelBase{
    protected $table = 'sys_msgs';

    /**
    * 分页方法
    *
    * @param int 加数
    * @param int 被加数
    * @return integer
    */
    public function page($keys = array(), $page=1, $limit=10, $type='new')
    {
        $builder = self::query_builder();
        $conditions = 'TRUE';
        foreach ($keys as $k => $v) {
            $conditions .= " AND $k = :$k:";
        }

        $builder->where($conditions, $keys);
        $builder->andWhere('status = :status:', array('status' => self::STATUS_NORMAL));
        return self::query_page($builder, $page, $limit);
    }

    public function get_sysmsg_by_id( $id ){
        return $this->where('id', $id)->first();
    }

    public function get_new_messages( $uid, $last_fetch_msg_time ){
        $sys = $this->where(['status' => self::STATUS_NORMAL])
        ->where(function($query) use ( $uid ){
            $query->where('receiver_uids', 0)//for all
                  ->orWhereRaw('FIND_IN_SET(\''.$uid.'\', receiver_uids)');
        })
        //->where('receiver_uids', 0)//for all
        //->orWhereRaw('FIND_IN_SET(\''.$uid.'\', receiver_uids)')
        ->where('update_time','>', $last_fetch_msg_time )
        ->orderBy('update_time','ASC')
        ->get();

        return $sys;
    }

    public static function updateMsg($uid, $last_updated, $page=1, $limit=10) {
        $lasttime = Usermeta::readUserMeta( $uid, Usermeta::KEY_LAST_READ_NOTICE );
        $lasttime = $lasttime?$lasttime[Usermeta::KEY_LAST_READ_NOTICE]: 0;

        $builder = self::query_builder('s');
        $where = array(
            's.post_time < '.$last_updated,
            's.post_time > '.$lasttime,
            's.status='.SysMsg::STATUS_NORMAL,
            '(FIND_IN_SET('.$uid .', s.receiver_uids) OR s.receiver_uids=0)'
        );

        $res = $builder -> where( implode(' AND ',$where) );
        $sysmsgs = self::query_page($builder, $page, $limit)->items;
        foreach ($sysmsgs as $row) {
            Message::newSystemMsg(
                $row->create_by,
                $uid,
                'xxx您有一条消息xxx',
                Message::TYPE_SYSTEM,
                $row->id
            );
        }

        if(isset($row)){
            Usermeta::refresh_read_notify(
                $uid,
                Usermeta::KEY_LAST_READ_NOTICE,
                $last_updated
            );
        }

        return $sysmsgs;
    }

    public function send_msg( $options ){
        $this->pic_url       = $options['pic_url'];
        $this->receiver_uids = $options['receiver_uids'];
        $this->target_id     = $options['target_id'];
        $this->target_type   = $options['target_type'];
        $this->jump_url      = $options['jump_url'];
        $this->status        = self::STATUS_NORMAL;
        $this->msg_type      = $options['msg_type'];
        $this->create_by     = $options['create_by'];
        $this->update_by     = $options['update_by'];
        return $this->save();
    }

    public function change_status( $uid, $status ){
        $this->update_by = $uid;
        $this->status = $status;
        return $this->save();
    }
}

<?php

namespace App\Models;

class SysMsg extends ModelBase{
    //消息类型
    const MSG_TYPE_NOTICE   = 1; //普通
    const MSG_TYPE_ACTIVITY = 2; //活动

    //Target类型
    const TARGET_TYPE_URL = 0; //跳转URL
    const TARGET_TYPE_ASK = 1;
    const TARGET_TYPE_REPLY = 2;
    const TARGET_TYPE_COMMENT = 3;
    const TARGET_TYPE_USER = 4;

    public function getSource(){
        return 'sys_msgs';
    }

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

    public function get_new_messages( $uid, $last_fetch_msg_time ){
        return $this->where(['status' => self::STATUS_NORMAL])
        ->where(function($query) use ( $uid ){
            $query->where('receiver_uids', 0)//for all
                  ->orWhereRaw('FIND_IN_SET(\''.$uid.'\', receiver_uids)');
        })
        ->where('update_time','>', $last_fetch_msg_time )
        ->orderBy('update_time','ASC')
        ->get();
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

    public static function count_unread_sysmsgs( $uid ){
        $lasttime = Usermeta::readUserMeta( $uid, Usermeta::KEY_LAST_READ_NOTICE );
        if( $lasttime ){
            $lasttime = $lasttime[Usermeta::KEY_LAST_READ_NOTICE];
        }
        else{
            $lasttime = 0;
        }

        return SysMsg::count(array(
            'post_time>'.$lasttime,
            'status='.SysMsg::STATUS_NORMAL,
            '(FIND_IN_SET('.$uid .', receiver_uids) OR receiver_uids=0)'
        ));
    }

    public static function get_unread_sysmsg(){

    }
}

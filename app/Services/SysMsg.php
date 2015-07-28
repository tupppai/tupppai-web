<?php
namespace App\Services;

use \App\Models\SysMsg as mSysMsg;

class SysMsg extends ServiceBase{

    public static function post_msg( $uid,  $title, $target_type, $target_id, $jump_url, $post_time, $receiver_uids, $msg_type, $pic_url ){
        $sysmsg = new mSysMsg();

        $title = trim($title);
        if( empty($title) ){
            return error('EMPTY_TITLE');
        }
        $sysmsg->title = $title;

        if( $target_type == mSysMsg::TARGET_TYPE_URL ){
            $target_id = 0;
            if( empty($jump_url) && !match_url_format($jump_url)){
                return error('EMPTY_JUMP_URL');
            }
        }
        else{
            if( empty( $target_id ) ){
                return error('EMPTY_ID');
            }
            if( empty($jump_url) ){
                $jump_url = '-';
            }
        }
        if( !$sysmsg->post_time = strtotime( $post_time ) ){
            return error('EMPTY_POST_TIME');
        }

        if( is_string( $receiver_uids ) ){
            $receiver_uids = explode(',', $receiver_uids);
        }
        if( !is_array($receiver_uids) ){
            return error('EMPTY_UID');
        }
        $receiver_uids = array_unique( $receiver_uids );
        if( empty( $receiver_uids ) ){
            return error('EMPTY_UID');
        }

        if( !empty($pic_url) ){
            if( !match_url_format($pic_url) ){
                return error('EMPTY_LOGO');
            }
        }
        else{
            $pic_url = '-';
        }

        $sysmsg -> pic_url = $pic_url;
        $sysmsg -> receiver_uids = implode(',', $receiver_uids);
        $sysmsg -> target_id = $target_id;
        $sysmsg -> target_type = $target_type;
        $sysmsg -> jump_url = $jump_url;
        $sysmsg -> status = mSysMsg::STATUS_NORMAL;
        $sysmsg -> msg_type = $msg_type;
        $sysmsg -> create_time = time();
        $sysmsg -> update_time = time();
        $sysmsg -> create_by = $uid;
        $sysmsg -> update_by = $uid;

        return $sysmsg->save_and_return($sysmsg);
    }

    public static function updateMsg($uid, $last_updated, $page=1, $limit=10) {
        $lasttime = Usermeta::readUserMeta( $uid, Usermeta::KEY_LAST_READ_NOTICE );
        $lasttime = $lasttime?$lasttime[Usermeta::KEY_LAST_READ_NOTICE]: 0;

        $builder = self::query_builder('s');
        $where = array(
            's.post_time < '.$last_updated,
            's.post_time > '.$lasttime,
            's.status='.mSysMsg::STATUS_NORMAL,
            '(FIND_IN_SET('.$uid .', s.receiver_uids) OR s.receiver_uids=0)'
        );

        $res = $builder -> where( implode(' AND ',$where) );
        $sysmsgs = mSysMsg::query_page($builder, $page, $limit)->items;
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

        return mSysMsg::count(array(
            'post_time>'.$lasttime,
            'status='.mSysMsg::STATUS_NORMAL,
            '(FIND_IN_SET('.$uid .', receiver_uids) OR receiver_uids=0)'
        ));
    }

    public static function get_unread_sysmsg(){

    }
}

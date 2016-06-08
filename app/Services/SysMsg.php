<?php
namespace App\Services;
use Queue;
use App\Jobs\Push;
use \App\Models\SysMsg as mSysMsg;

class SysMsg extends ServiceBase{

    public static function getSystemMessageById( $id ){
        return (new mSysMsg)->where(['id'=>$id])->first();
    }

    public static function postMsg( $uid,  $title, $target_type, $target_id, $jump_url, $post_time, $receiver_uids, $msg_type, $pic_url ){
        $sysmsg = new mSysMsg();
        $sysmsg->title = $title;

        if( $target_type == mSysMsg::TARGET_TYPE_URL ){
            $target_id = 0;
        }
        else{
            if( empty( $target_id ) ){
                return error('EMPTY_ID','目标ID为空');
            }
            if( empty($jump_url) ){
                $jump_url = '';
            }
        }
        if( !$sysmsg->post_time = strtotime( $post_time ) ){
            return error('EMPTY_POST_TIME', '发送时间解析错误');
        }

        if( $receiver_uids != '') {
            if( is_int($receiver_uids) ){
                $receiver_uids = (string)$receiver_uids;
            }
            if( is_string( $receiver_uids ) ){
                $receiver_uids = explode(',', $receiver_uids);
            }
            if( !is_array($receiver_uids) ){
                return error('EMPTY_UID', '接收者id需要为数组');
            }
            $receiver_uids = array_unique( $receiver_uids );
            if( empty( $receiver_uids ) ){
                return error('EMPTY_UID','接收者id不能为空');
            }
        }
        else {
            $receiver_uids = array();
        }

        if( empty($pic_url) ){
            $pic_url = '';
        }

        $data = [];
        $data['pic_url']       = $pic_url;
        $data['receiver_uids'] = implode(',', $receiver_uids);
        $data['target_id']     = $target_id;
        $data['target_type']   = $target_type;
        $data['jump_url']      = $jump_url;
        $data['msg_type']      = $msg_type;
        $data['create_by']     = $uid;
        $data['update_by']     = $uid;

        $msg = $sysmsg->send_msg( $data );
        $delay = strtotime($post_time) - time();
        Queue::later( $delay, new Push([
            'type' => 'sys_msg',
            'sys_msg_id' => $msg->id,
            'uids' => $receiver_uids,
            'uid' => 1 //junk parameter
        ]));
        return $msg;
    }

    public static function deleteSysmsg( $id, $uid ){
        $sysmsg = (new mSysMsg)->get_sysmsg_by_id( $id );
        return $sysmsg->change_status( $uid, mSysMsg::STATUS_DELETED );
    }

    public static function getNewSysMsg( $uid, $last_fetch_msg_time ){
        return (new mSysMsg)->get_new_messages( $uid, $last_fetch_msg_time );
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

}

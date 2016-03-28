<?php namespace App\Http\Controllers\Admin;

use App\Models\SysMsg as mSysMsg;
use App\Models\User as mUser;
use App\Models\Comment;

use App\Services\User as sUser;
use App\Services\SysMsg as sSysMsg;
use App\Services\ActionLog as sActionLog;

use App\Facades\CloudCDN;

class SysMsgController extends ControllerBase{

    public function new_msgAction(){
        return $this->output();
    }

    public function msg_listAction(){
        return $this->output();
    }


    public function get_msg_listAction(){
        $msg_type_text = array('-','通知','活动');
        $target_type_text = array('跳转URL','求助','作品','评论','用户');

        $type = $this->get('type','string');

        $cond = array();
        $cond['title'] = array(
            $this->post('title','string'),
            'LIKE',
            'AND'
        );

        switch( $type ){
            case 'pending':
                $cond['post_time'] = array(time(),'>');
                $cond['status']    = mSysMsg::STATUS_NORMAL;
                break;
            case 'sent':
                $cond['post_time'] = array(time(),'<');
                $cond['status']    = mSysMsg::STATUS_NORMAL;
                break;
            case 'deleted':
                $cond['status'] = mSysMsg::STATUS_DELETED;
                break;
            default:
                break;
        }

        $join = array();
        $order = 'post_time ASC ';
        $msg_list = $this->page(new mSysMsg, $cond, $join, $order);

        //$msg_list = mSysMsg::get_sys_msg_list( $type );

        $i=0;
        foreach( $msg_list['data'] as $msg ){
            $receiver_usernames =array();
            if( $msg->receiver_uids ){
                $receivers = sUser::getUserByUids(explode(',',$msg->receiver_uids)) ;
                $receiver_usernames[] = array_column( $receivers, 'username' );
            }
            else{
                $receiver_usernames[] = array('全体');
            }
            $pc_host = env('MAIN_HOST');
            switch($msg->target_type){
                case mSysMsg::TYPE_URL:
                    $msg_list['data'][$i]->jump = '<a href="'.$msg->jump_url.'">链接</a>';
                    break;
                case mSysMsg::TYPE_ASK:
                    $msg_list['data'][$i]->jump = '<a href="http://'.$pc_host.'/#askdetail/ask/'.$msg->target_id.'" target="_blank">查看原图</a>';
                    break;
                case mSysMsg::TYPE_REPLY:
                    $msg_list['data'][$i]->jump = '<a href="http://'.$pc_host.'/#replydetail/0/'.$msg->target_id.'" target="_blank">查看原图</a>';
                    break;
                case mSysMsg::TYPE_COMMENT:
                    $msg_list['data'][$i]->jump = '评论详情页赞无链接';
                    //$msg_list['data'][$i]->jump = '<a href="http://'.$pc_host.'/ask/show/'.$msg->target_id.'" target="_blank">查看原图</a>';
                break;
                case mSysMsg::TYPE_USER:
                    $msg_list['data'][$i]->jump = '<a href="http://'.$pc_host.'/#homepage/reply/'.$msg->target_id.'" target="_blank">查看用户信息</a>';
                    break;
                default:
                    $msg_list['data'][$i]->jump = '无跳转';
                    break;
            }
            if( $msg->jump_url  ){
                $msg_list['data'][$i]->jump = '<a href="'.$msg->jump_url.'">链接</a>';
            }

            if( $msg->pic_url != '-' ){
                $msg_list['data'][$i]->title .= '<img src="'.$msg->pic_url.'"/>';
            }

            $msg_list['data'][$i]->msg_type = $msg_type_text[$msg->msg_type];
            $msg_list['data'][$i]->target_type = $target_type_text[$msg->target_type];
            $msg_list['data'][$i]->receivers = implode(',', $receiver_usernames[0]);
            $msg_list['data'][$i]->create_time = date('Y-m-d H:i', $msg->create_time);
            $msg_list['data'][$i]->update_time = date('Y-m-d H:i', $msg->update_time);
            $msg_list['data'][$i]->post_time = date('Y-m-d H:i', $msg->post_time);
            if( $msg->create_by >0 ){
                $msg_list['data'][$i]->create_by = sUser::getUserByUid($msg->create_by)->nickname;
            }
            else{
                $msg_list['data'][$i]->create_by = '系统';
            }

            if( strtotime($msg->post_time) >= time() ){
                $msg_list['data'][$i]->oper = '<a href="#" class="del_msg">取消发布</a>';
            }
            else{
                $msg_list['data'][$i]->oper = '无';
            }
            ++$i;
        }


        return $this->output_table($msg_list);
    }

    public function post_msgAction(){
        $uid = $this->_uid;
        $sender = $uid;

        $msg_type = $this->post('msg_type','int');
        $title = $this->post('title', 'string');
        $target_type = $this->post('target_type', 'int');
        $target_id = $this->post('target_id', 'int');
        $pic_url = $this->post('pic_url','string','');
        $jump_url = $this->post('jump_url','url' ,'');
        $post_time = $this->post('post_time', 'string');
        $receiver_uids = $this->post('receiver_uids','string');
        $send_as_system = (bool)$this->post('send_as_system','string', false);
        if( !$msg_type ){
            return error('EMPTY_MSG_TYPE');
        }
        if( !$title ){
            return error('EMPTY_TITLE','标题不能为空');
        }
        if( strtotime($post_time) <time() ){
            return error('INVALID_SEND_TIME');
        }
        if( $send_as_system ){
            $sender = 0;
        }

        $ret = sSysMsg::postMsg($sender,  $title, $target_type, $target_id, $jump_url, $post_time, $receiver_uids, $msg_type, $pic_url );

        if( $ret ){
            sActionLog::init('POST_SYSTEM_MESSAGE');
            sActionLog::save($ret);
            $msg = '发送成功';
            $code = 1;
        }
        else{
            $msg = '发送失败';
        }

        return $this->output(['code' => $code, 'msg' => $msg]   );
    }

    public function getUserListAction(){
        $q = $this->get('q','string');
        if( empty( $q )){
            return error('EMPTY_QUERY_STRING');
        }

        $users = sUser::getFuzzyUsersByIdAndName( $q );

        foreach( $users as $key => $user){
            $user->avatar = CloudCDN::file_url($user->avatar);
        }
        $all = new mUser();
        $all->uid = 0;
        $all->nickname = '全体';
        $all->username = '全体';
        $all->status =  1;
        $all->sex = 1;
        $all->avatar = '/img/logo.jpg';
        $users->prepend( $all );

        return $this->output_json($users);
    }

    public function del_msgAction(){
        $id = $this->post('id', 'int');
        $uid = $this->_uid;
        if( !$id ){
            return error('EMPTY_SYSMSG_ID');
        }

        sSysMsg::deleteSysmsg( $id, $uid, mSysMsg::STATUS_DELETED );

        return $this->output_json(['result'=>'ok']);
    }
}

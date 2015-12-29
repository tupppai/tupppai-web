<?php namespace App\Http\Controllers\Admin;

use App\Models\ActionLog;
use App\Models\User;
use App\Models\Config;
use App\Models\UserScore;
use App\Models\UserSettlement;
use App\Models\UserScheduling;
use App\Models\Usermeta;

use App\Services\UserRole as sUserRole;
use App\Services\User as sUser;
use App\Services\UserSettlement as sUserSettlement;

class UserController extends ControllerBase
{

    public function indexAction()
    {
    }

    public function beatAction(){

        \Heartbeat::init(\Heartbeat::DB_LOGON)->hello($this->_uid, session_id());

        $online_count = \Heartbeat::init(\Heartbeat::DB_LOGON)->online_count();

        $nums = array();
        foreach(\Heartbeat::data() as $row){
            $nums[$row] = sizeof(\Heartbeat::init(\Heartbeat::DB_PROCESS)->fetch($row, $online_count));
        }

        $data = array(
            'notifications'=>array(
                '审核作品'  => $nums[\Heartbeat::CACHE_REPLY],
                '举报数'    => $nums[\Heartbeat::CACHE_INFORM],
                '帖子列表'  => $nums[\Heartbeat::CACHE_ASK] + $nums[\Heartbeat::CACHE_REPLY],
                '评论列表'  => $nums[\Heartbeat::CACHE_COMMENT],
                '用户反馈'  => $nums[\Heartbeat::CACHE_FEEDBACK]
            )
        );
        return $this->output_table($data);
    }


    public function list_rolesAction()
    {

        $user_role = new UserRole;
        // 检索条件
        $cond = array();
        $cond['uid']        = $this->post("uid", "int");

        // 用于遍历修改数据
        $data  = $this->page($user_role, $cond);
        foreach($data['data'] as $row){
        }
        // 输出json
        return $this->output_table($data);
    }
    public function assign_roleAction(){
        $user_id = $this->post('user_id','int');
        $role_ids = $this->post('role_id','int');

        if( empty($user_id) ){
            return error( 'EMPTY_UID', '没有用户id' );
        }

        if( empty($role_ids) ){
            return error('EMPTY_ROLE_ID');
        }

        $role = sUserRole::assignRole( $user_id, $role_ids );
        return $this->output( ['result'=>'ok'] );
    }

    public function parttime_paidAction() {
        $uid = $this->post("uid", "int");

        if(!$uid) {
            return error( 'EMPTY_UID', '用户不存在');
        }

        sUserSettlement::parttimePaid( $uid, $this->_uid );
        return $this->output_json(['result'=>'ok']);
    }

    public function staff_paidAction() {
        $uid = $this->post("uid", "int");

        if(!$uid) {
            return error( 'EMPTY_UID', '用户不存在');
        }

        sUserSettlement::payStaff( $uid, $this->_uid );

        return $this->output_json(['result'=>'ok']);
    }

    /**
     * [forbid_speechAction 禁言用户]
     * @return [type] [description]
     */
    public function forbid_speechAction(){
        $uid = $this->post("uid", "int");
        $value = $this->post("value", "int", '0');       // -1永久禁言,0或者过去的时间为不禁言,将来的时间则为禁言

        if(!$uid) {
            return error('EMPTY_UID', '用户不存在');
        }

        sUser::banUser( $uid, $value );
        return $this->output_json( ['result'=>'ok'] );
    }

    public function block_userAction(){
        $uid = $this->post( 'uid', 'int', 0 );
        $status = $this->post( 'status', 'int' );
        if( !$uid ){
            return error( 'EMPTY_UID' );
        }
        if( !$status ){
            return error( 'EMPTY_STATUS' );
        }

        $user = sUser::blockUser( $uid, $status, $this->_uid );

        return $this->output( ['result'=>'ok'] );
    }

}

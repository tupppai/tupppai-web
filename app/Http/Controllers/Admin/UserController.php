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

class UserController extends ControllerBase
{

    public function indexAction()
    {
    }

    public function beatAction(){
        $this->noview();

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
            return error( 'EMPTY_UID', '没有角色id' );
        }

        if( empty($role_id) ){
            return error('EMPTY_ROLE_ID');
        }

        $role = sUser::assignRole( $user_id, $role_id );
        return $this->output( ['result'=>'ok'] );
    }

    public function parttime_paidAction() {
        $this->noview();

        $uid = $this->post("uid", "int");
        $oper_id = $this->_uid;

        if(!$uid) {
            return ajax_return(0, '用户不存在');
        }
        $user = User::findUserByUID($uid);
        if(!$user) {
            return ajax_return(0, '用户不存在');
        }

        $balance = UserScore::get_balance($uid);
        $current_score = $balance[UserScore::STATUS_NORMAL];
        $paid_score    = $balance[UserScore::STATUS_PAID];

        if( $current_score <= 0 ) {
            return ajax_return(0, '当前未结算资金为0');
        }

        $res = UserSettlement::paid($this->_uid, $uid, $paid_score, $current_score);
        ActionLog::log(ActionLog::TYPE_PARTTIME_PAID, array(), $res);
        return ajax_return(1, 'okay');
    }

    public function staff_paidAction() {
        $this->noview();

        $uid = $this->post("uid", "int");
        $oper_id = $this->_uid;

        if(!$uid) {
            return ajax_return(0, '用户不存在');
        }
        $user = User::findUserByUID($uid);
        if(!$user) {
            return ajax_return(0, '用户不存在');
        }

        $meta = Usermeta::readUserMeta($uid, Usermeta::KEY_STAFF_TIME_PRICE_RATE);
        if($meta) {
            $rate = $meta[Usermeta::KEY_STAFF_TIME_PRICE_RATE];
        }
        else {
            $rate = Config::getConfig(Usermeta::KEY_STAFF_TIME_PRICE_RATE);
        }

        $balance = UserScheduling::get_balance($uid);
        $current_score = get_hour($balance[UserScheduling::STATUS_NORMAL]);
        $paid_score    = get_hour($balance[UserScheduling::STATUS_PAID]);

        if( $current_score <= 0 ) {
            return ajax_return(0, '当前未结算资金为0');
        }

        $res = UserSettlement::staff_paid($this->_uid, $uid, $paid_score, $current_score, $rate);
        ActionLog::log(ActionLog::TYPE_STAFF_PAID, array(), $res);

        return ajax_return(1, 'okay');
    }

    /**
     * [forbid_speechAction 禁言用户]
     * @return [type] [description]
     */
    public function forbid_speechAction(){
        $this->noview();

        $uid = $this->post("uid", "int");
        $value = $this->post("value", "int", '0');       // -1永久禁言,0或者过去的时间为不禁言,将来的时间则为禁言

        if(!$uid) {
            return ajax_return(0, '用户不存在');
        }
        $user = User::findUserByUID($uid);
        if(!$user) {
            return ajax_return(0, '用户不存在');
        }

        $old = Usermeta::read_user_forbid($uid);
        $res = Usermeta::write_user_forbid($uid, $value);
        if( $res ){
            ActionLog::log(ActionLog::TYPE_FORBID_USER, array('fobid'=>$old), array('fobid'=>$res));
        }

        return ajax_return(1, 'okay');
    }

    public function set_statusAction(){
        $uid = $this->post( 'uid', 'int', 0 );
        $status = $this->post( 'status', 'int' );
        if( !$uid ){
            return error( 'EMPTY_UID' );
        }
        if( !$status ){
            return error( 'EMPTY_STATUS' );
        }

        $user = sUser::setUserStatus( $uid, $status, $this->_uid );

        return $this->output( ['result'=>'ok'] );
    }

}

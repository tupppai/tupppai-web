<?php namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Ask;
use App\Models\Reply;
use App\Models\Config;
use App\Models\Usermeta;
use App\Models\Label;
use App\Models\Role;
use App\Models\UserScore;
use App\Models\UserRole;
use App\Models\UserSettlement;
use App\Models\UserScheduling as mUserScheduling;
use App\Models\Evaluation;

use App\Services\UserScheduling as sUserScheduling,
    App\Services\User as sUser,
    App\Services\ActionLog as sActionLog;

class SchedulingController extends ControllerBase
{
    public function initialize(){
        view()->share('is_staff', $this->is_staff);
    }
    public function indexAction() {

        return $this->output(array(
            'paid'=>1,
            'unpaid'=>2,
            'rate'=>3
        ));

        $uid = $this->get('uid', 'int',0);

        $paidCond = array( 'column'=>'score' );
        $unpaidCond = 'us.status='.UserScheduling::STATUS_NORMAL.' AND us.end_time<'.time();
        if( $uid ){
            $paidCond['conditions'] = ' operate_to='.$uid;
            $unpaidCond .= ' AND us.uid='.$uid;
        }
        // 结算金额
        $paid    = sprintf("%0.2f", UserSettlement::sum($paidCond) );
        $sql = 'umeta_key="staff_time_price_rate" ORDER BY u.uid desc';

        $default_rate = Config::getConfig(Usermeta::KEY_STAFF_TIME_PRICE_RATE);
        // 兼职结算的分数
        $get_balance = UserScheduling::query_builder('us')
                        ->columns('u.uid,round( sum(us.end_time - us.start_time) / (60 * 60),2) * ifnull(um.str_value, '.$default_rate.') AS unpaid')
                        ->where($unpaidCond)
                        ->join(get_class(new User), 'us.uid=u.uid', 'u')
                        ->leftjoin(get_class(new Usermeta), 'um.uid=u.uid AND um.key="'.Usermeta::KEY_STAFF_TIME_PRICE_RATE.'"', 'um')
                        ->orderBy('u.uid DESC')
                        ->groupBy('u.uid')
                        ->getQuery()
                        ->execute()
                        ->toArray();
        $unpaid = array_sum(array_column($get_balance,'unpaid') );

        $this->set('paid', floor($paid));
        $this->set('unpaid', floor($unpaid));

        $rate   = Config::getConfig(Usermeta::KEY_STAFF_TIME_PRICE_RATE);
        $this->set('rate', $rate);
    }

    public function end_timeAction() {
        $id = $this->post('id', 'int');
        if(!$id){
            return error( 'EMPTY_SCHEDULE_ID' , '请选择具体的时间安排');
        }

        $scheduling = (new mUserScheduling)->get_scheduling_by_id($id);
        $old = sActionLog::init( 'OFF_DUTY' );
        if(!$scheduling){
            return error( 'EMPTY_SCHEDULE_ID' , '请选择具体的时间安排');
        }
        if(time() < $scheduling->start_time){
            return error( 'SCHEDULE_PENDING' , '该安排未开始');
        }
        if(time() >= $scheduling->end_time){
            return error( 'SCHEDULE_PASSED' , '该安排已经结束');
        }
        if( $this->is_staff && $scheduling->uid != $this->_uid ){
            return error( 'WRONG_OWNER' , '不能帮他人结束时间');
        }

        $scheduling->end_time = time();
        $scheduling->set_by   = $this->_uid;
        $scheduling->update_time = time();

        $saveSchedule = $scheduling->save();
        sActionLog::log($old, $saveSchedule);
        return $this->output_json( ['result' =>  'okay' ] );
    }

    public function delAction() {
        $id = $this->post('id', 'int');
        if(!$id){
            return error('EMPTY_SCHEDULE_ID', '请选择具体的时间安排');
        }

        $scheduling = (new mUserScheduling)->get_scheduling_by_id($id);
        if(!$scheduling){
            return error( 'EMPTY_SCHEDULE_ID' , '请选择具体的时间安排');
        }
        if($scheduling->status == mUserScheduling::STATUS_DELETED){
            return error( 'SCHEDULE_DELETED' , '该安排已经删除');
        }
        $old = sActionLog::init( 'DELETE_SCHEDULE' );

        $scheduling->status   = mUserScheduling::STATUS_DELETED;
        $scheduling->del_by   = $this->_uid;
        $scheduling->del_time = time();
        $scheduling->update_time = time();

        $saveSchedule = $scheduling->save();
        sActionLog::log($old, $saveSchedule);
        return $this->output_json( ['result' => 'okay'] );
    }

    public function recoverAction() {
        $id = $this->post('id', 'int');
        if(!$id){
            return error( '' , '请选择具体的时间安排');
        }

        $scheduling = UserScheduling::findFirst($id);
        if(!$scheduling){
            return error( '' , '请选择具体的时间安排');
        }

        //已结束的，已结算的不能恢复
        if($scheduling->status == UserScheduling::STATUS_PAID || $scheduling->end_time < time() ){
            return error( '' , '该安排已结算或已结束，不能恢复');
        }
        $old = ActionLog::clone_obj($scheduling);

        $scheduling->status   = UserScheduling::STATUS_NORMAL;
        $scheduling->del_by   = 0;
        $scheduling->del_time = 0;
        $scheduling->update_time = time();

        $saveSchedule = $scheduling->save();
        ActionLog::log(ActionLog::TYPE_RECOVER_SCHEDULE, $old, $saveSchedule);
        return error( '' , 'okay');
    }

    public function list_schedulingsAction()
    {
        $scheduling = new mUserScheduling;
        // 检索条件
        $cond = array();
        $uid = $this->post("uid", "int");
        if( $uid ){
            $cond['uid']        =  $uid;
        }
        //$cond['status']     = $this->post("status", "int", mUserScheduling::STATUS_NORMAL);

        $status = $this->post('type','int', 1);
        switch($status){
        case 0:
            $cond['start_time'] = array(
                time(),
                '>'
            );
            $cond['status'] = mUserScheduling::STATUS_NORMAL;
            break;
        case 1:
            $cond['start_time'] = array(
                time(),
                '<='
            );
            $cond['end_time'] = array(
                time(),
                '>='
            );
            $cond['status'] = mUserScheduling::STATUS_NORMAL;
            break;
        case 2:
            $cond['end_time'] = array(
                time(),
                '<'
            );
            $cond['status'] = mUserScheduling::STATUS_NORMAL;
            break;
        case 3:
            $cond['status'] = mUserScheduling::STATUS_PAID;
            break;
        case 4:
            $cond['status'] = mUserScheduling::STATUS_DELETED;
            break;
        case 5:
            $cond['status'] = mUserScheduling::STATUS_COMPLAIN;
            break;
        }

        // 关联表数据结构
        //$join = array();
        //$join['User'] = 'uid';

        // 用于遍历修改数据
        $data  = $this->page($scheduling, $cond);
        $types = sUserScheduling::operTypes();

        foreach($data['data'] as $row){
            $row->set_by = '';
            $row->del_by = '';
            $row->avatar = '';
            if($user1 = sUser::getUserByUid($row->set_by))
                $row->set_by = $user1->nickname;
            if($user2 = sUser::getUserByUid($row->del_by))
                $row->del_by = $user2->nickname;

            $user = sUser::getUserByUid($row->uid);
            $row->username = $user->username;
            $row->nickname = $user->nickname;
            $row->avatar = '<img class="user-portrait" src='.$user->avatar.' alt="头像">';
            $logs   = sActionLog::get_log($row->uid, $row->start_time, $row->end_time);

            foreach($types as $key=>$type){
                if(!isset($type_arr[$key])) $row->$key = 0;
                foreach($logs as $log) {
                    if(in_array($log->oper_type, $type)){
                        $row->$key += $log->num;
                    }
                }
            }
            $row->avg_score = '0(未实现)';
            $row->total_score = '0(未实现)';
            /*
                        $total_score = UserScore::sum(array('column'=>'score','conditions'=>'oper_by='.$row->uid.' AND status!='.UserScore::STATUS_DELETED.' AND action_time>='.$row->start_time.' AND action_time<='.$row->end_time));
                        $row->total_score = number_format($total_score,1);
                        if( $row->pass_count != 0 ){
                            $row->avg_score = number_format($total_score/$row->verify_count,1);
                        }
             */

            $row->oper = '';

            switch( $status ){
                case 4:
                    if( $this->is_admin && ( $row->end_time > time() ) ){
                        $row->oper .= '<a href="#" class="recover" style="color:green" data="'.$row->id.'">恢复</a>';
                    }
                    break;
                case 1:
                    //只能结束自己的时间,管理员可以结束时间
                    if($this->is_admin || $this->_uid == $row->uid)
                        $row->oper .= '<a href="#" class="end_time" data="'.$row->id.'">结束时间</a>&nbsp;';
                default:
                    if( $this->is_admin ){
                        $row->oper .= '<a href="#" class="delete" style="color:red" data="'.$row->id.'">删除</a>';
                    }

            }

            $row->start_time = date("Y-m-d H:i:s", $row->start_time);
            $row->end_time   = date("Y-m-d H:i:s", $row->end_time);
        }
        // 输出json
        return $this->output_table($data);
    }
}

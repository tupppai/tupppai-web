<?php namespace App\Http\Controllers\Admin;
use App\Models\User as mUser,
    App\Models\Master as mMaster,
    App\Models\ActionLog as mActionLog;

use App\Services\Master as sMaster,
    App\Services\User as sUser,
    App\Services\ActionLog as sActionLog;

use App\Counters\UserCounts as cUserCounts;
use Request, Html;

class MasterController extends ControllerBase{

    public function indexAction(){

        return $this->output();
    }

    /**
     * 大神列表
     * @return [type] [description]
     */
    public function master_listAction(){

        return $this->output();
    }

    public function rec_listAction(){

        return $this->output();
    }

    public function list_recsAction(){
        $status = $this->post('status', 'string', '1');

        #todo: 丢到event
        sMaster::updateMasters();
        $data = sMaster::getMasters( $status );
        $masters = [];

        foreach($data as $row){
            $master = [];
            $master['id'] = $row->id;
            $master['username'] = $row->user->username;
            $master['nickname'] = $row->user->nickname;
            $master['uid'] = $row->user->uid;
            $master['sex'] = get_sex_name($row->user->sex);
            $master['start_time']    = date('Y-m-d H:i', $row->start_time);
            $master['end_time']      = date('Y-m-d H:i', $row->end_time);

            $counts = cUserCounts::get( $row->user->id );
            $master['total_inform_count'] = $counts['inform_count'];

            $master['oper'] = Html::link('#', '取消推荐', array(
                'class'=>'cancel',
                'data-id'=>$row->id
            ));
            $masters[] = $master;
        }

        $results =  array(
            'data' => $masters,
            'recordsTotal' => $data->total(),
            'recordsFiltered' => count($masters)
        );

        return $this->output_table($results);
    }

    /**
     * 搜索大神
     * @return [type] [description]
     */
    public function list_mastersAction(){
        $user = new mUser;
        // 检索条件
        $cond = array();
        $cond['role_id']    = $this->get("role_id", "int");
        $cond['uid']        = $this->post("uid", "int");
        $cond['username']   = array(
            $this->post("username", "string"),
            "LIKE",
            "AND"
        );
        $cond['is_god'] = 1;

         // 关联表数据结构
        $join = array();
        //$join['Master'] = 'uid';
        //$join['Role']     = 'role_id';

        // 用于遍历修改数据
        $data  = $this->page($user, $cond, $join);

        foreach($data['data'] as $row){
            $uid = $row->uid;
            $row->sex = get_sex_name($row->sex);
            $row->create_time = date('Y-m-d H:i', $row->create_time);
            $counts = cUserCounts::get( $uid );
            $row->total_inform_count = $counts['inform_count'];

            $row->oper = Html::link('#', '推荐', array(
                'class'=>'recommend',
                'data-target'=>'#recommend',
                'data-toggle'=>'modal',
                'role'=>'dialog'
            ));
        }
        // 输出json
        return $this->output_table($data);
    }

    public function recommendAction(){
        $uid        = $this->post('master_id', 'int', 0);
        $start_time = $this->post('start_time', 'int', 0);
        $end_time   = $this->post('end_time', 'int', 0);
        if( $start_time < time() ){
            return error('INVALID_START_TIME', '不能设置过去的时间');
        }
        if( $start_time > $end_time ){
            return error('INVALID_END_TIME', '开始时间不能晚于结束时间');
        }
        sMaster::addNewMaster($uid, $this->_uid, $start_time, $end_time);

        return $this->output();
    }

    public function cancelAction(){
        if( !Request::ajax() ){
            return error('WRONG_ARGUMENTS');
        }

        $id = $this->post('id', 'int', 0);
        if( !$id ){
            return error('EMPTY_ID');
        }

        sMaster::cancelRecommendMaster($id, $this->_uid);
        return $this->output(['result'=>'ok']);
    }
}

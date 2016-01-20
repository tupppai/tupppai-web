<?php namespace App\Http\Controllers\Admin;

use App\Models\User,
    App\Models\ActionLog as mActionLog;

use App\Services\ActionLog as sActionLog;

class LogController extends ControllerBase
{

    public function indexAction()
    {

        return $this->output();
    }

    public function list_logsAction()
    {
        $log = new mActionLog;
        $uid = '';
        $log->table = $log->get_table(253);
        // 检索条件
        $cond = array();
        $cond['id']             = $this->post("role_id", "int");

        $cond['create_time']        = $this->post("role_created", "string");
        $cond['update_time']        = $this->post("role_updated", "string");
        $cond['nickname']           = array(
            $this->post('nickname'),
            'LIKE'
        );

        // 用于遍历修改数据
        $data  = $this->page($log, $cond);
        dd($data);

        foreach($data['data'] as $row){
            $config_id = $row->id;
            $row->create_time = date('Y-m-d H:i:s', $row->create_time);
            $row->update_time = date('Y-m-d H:i:s', $row->update_time);
            $row->oper = '<a href="#edit_config" data-toggle="modal" class="edit">编辑</a> ';
        }
        // 输出json
        return $this->output_table($data);
	}
}

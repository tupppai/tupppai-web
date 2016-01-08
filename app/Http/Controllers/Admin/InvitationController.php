<?php namespace App\Http\Controllers\Admin;

use App\Models\Ask; 
use App\Models\User;
use App\Models\Usermeta;

class InvitationController extends ControllerBase
{

    public function workAction(){

        return $this->output();
    }

    public function helpAction() {

        return $this->output();
    }

    public function delhelpAction() {

        return $this->output();
    }

    public function delworkAction() {

        return $this->output();
    }

    public function completeAction() {

        return $this->output();
    }

    public function listAction() {
// 获取model
        $asks = new Ask;
        // 检索条件
        $cond = array();
        $join = array();
        $join['User'] = 'uid';
        $data  = $this->page($asks, $cond, $join);

        foreach($data['data'] as $row){
            $row->id =  $row->id;
            $row->uid = "评论用户ID:" . $row->uid;
            $row->total_share = '数据库没这个字段';//$row ->; 
            $row->sex = get_sex_name($row -> sex);
            $row->status = ($row -> status) ? "已处理":"未处理";
            $row->create_time = date('m-d H:i', $row->create_time);
        //     $row->oper = "<button class='edit'>编辑</button>"."<button class='delete'>删除</button>";
        }
        return $this->output_table($data);
    }
}


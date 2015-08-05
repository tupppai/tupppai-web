<?php namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Ask;
use App\Models\Reply;
use App\Models\Usermeta;
use App\Models\Label;
use App\Models\Role;
use App\Models\UserScore;
use App\Models\UserRole;
use App\Models\UserSettlement;
use App\Models\Evaluation;

class ScoreController extends ControllerBase
{
    public function indexAction() {

    }

    public function schedulingAction() {

    }

    // list_scores list_user_settlement 获取结算列表
    public function list_scoresAction()
    {
        $score = new UserSettlement;
        // 检索条件
        $cond = array();
        $cond['operate_to']        = $this->post("operate_to", "int");

        // 关联表数据结构
        $join = array();
        $join['User'] = 'uid';

        // 用于遍历修改数据
        $data  = $this->page($score, $cond ,$join);

        foreach($data['data'] as $row){
            $user = User::findFirst($row->operate_to);
            $row->operate_to  = $user->username;
            $row->create_time = date("Y-m-d H:i:s", $row->create_time);
        }
        // 输出json
        return $this->output_table($data);
    }
}

<?php namespace App\Http\Controllers\Admin;

use App\Models\User,
    App\Models\Usermeta as mUsermeta,
    App\Models\Role,
    App\Models\ActionLog,
    App\Models\Permission,
    App\Models\PermissionRole,
    App\Models\UserRole;

use App\Models\Config as mConfig;
use App\Services\Usermeta as sUsermeta;
use App\Services\Config as sConfig;

class ConfigController extends ControllerBase
{

    public $config = "";

    public function indexAction()
    {

        return $this->output();
    }

    public function list_configsAction()
    {
        $rows = sConfig::data();

        $config = new mConfig;
        // 检索条件
        $cond = array();
        $cond['id']             = $this->post("config_id", "int");
        // $cond['name']           = array(
        //     $rows,
        //     'IN'
        // );

        // 用于遍历修改数据
        $data  = $this->page($config, $cond);
        foreach($data['data'] as $row){
            $config_id = $row->id;
            $row->create_time = date('Y-m-d H:i:s', $row->create_time);
            $row->update_time = date('Y-m-d H:i:s', $row->update_time);
            $row->oper = '<a href="#edit_config" data-toggle="modal" class="edit">编辑</a> ';
        }
        // 输出json
        return $this->output_table($data);
	}

    public function set_configAction(){

        $name   = $this->post("name", "string");
        $value  = $this->post("value", "string");
        $remark  = $this->post("remark", "string");

        $config = sConfig::setConfig($name, $value, $remark);

        return $this->output_json( ['result' => 'ok', 'config' => $config] );
    }

    public function set_person_rateAction(){
        $uid   = $this->post("uid", "int");
        $value = $this->post("value", "float");

        sUsermeta::writeUserMeta($uid, mUsermeta::KEY_STAFF_TIME_PRICE_RATE, $value);
        return $this->output_json( ['result'=>'ok'] );
    }
}

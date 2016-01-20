<?php namespace App\Http\Controllers\Admin;

use App\Services\IException as sException;
use App\Models\IException as mException;

class ExceptionController extends ControllerBase {

    public function indexAction(){
        return $this->output();
    }

    public function list_exceptionsAction(){
        $appModel = new mException;

        $cond = array();

        $data = $this->page($appModel, $cond);
        foreach ($data['data'] as $app) {
            $app->oper = '<a href="#" class="delete">删除</a>';
        }

        return $this->output_table($data);
    }
}

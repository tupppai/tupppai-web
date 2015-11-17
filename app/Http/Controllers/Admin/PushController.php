<?php namespace App\Http\Controllers\Admin;

use Event, Log;

use App\Services\ActionLog as sActionLog;

class PushController extends ControllerBase{

    public $_allow = array(
        'tower',
    );

    public function towerAction() {
        $request_body = array(file_get_contents('php://input'));

        sActionLog::addTowerTaskAction($request_body);
    }

}

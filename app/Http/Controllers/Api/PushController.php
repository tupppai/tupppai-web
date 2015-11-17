<?php namespace App\Http\Controllers\Api;

use Event, Log;
use App\Events\GitPushEvent;

class PushController extends ControllerBase{

    public $_allow = array(
        'index',
    );

    public function indexAction() {
        $request_body = array(file_get_contents('php://input'));
        Log::info('tower push', $request_body);
    }

}

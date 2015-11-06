<?php namespace App\Http\Controllers\Api;

use Event;
use App\Events\GitPushEvent;

class PushController extends ControllerBase{

    public $_allow = array(
        'index',
    );

    public function indexAction() {
        Event::fire(new GitPushEvent(123));
        echo 123;
    }

}

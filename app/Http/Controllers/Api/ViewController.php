<?php namespace App\Http\Controllers\Api;

use App\Services\User as sUser;

class ViewController extends ControllerBase
{

    public function infoAction() {
        $user = sUser::getUserByUid($this->_uid);

        return $this->output(sUser::detail($user));
    }
}

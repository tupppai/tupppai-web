<?php namespace App\Http\Controllers\Admin;

use App\Models\User as mUser;
use App\Models\Role as mRole;
use App\Models\UserScheduling as mUserScheduling;
use App\Models\UserRole as mUserRole;
use App\Models\ActionLog as mActionLog;

use App\Services\User as sUser, 
    App\Services\UserRole as sUserRole,
    App\Services\UserScheduling as sUserScheduling,
    App\Services\ActionLog as sActionLog;

class ManageController extends ControllerBase
{

    public function verifyAction(){

        return $this->output();
	}
}

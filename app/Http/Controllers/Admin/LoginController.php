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

use Request;

class LoginController extends ControllerBase
{

	/**
	 * [indexAction 登录界面]
	 * @return [type] [description]
	 */
    public function indexAction(){
        if ( Request::ajax() ) {
			$username = $this->post('username');
            if (empty($username)){
                return error('EMPTY_USERNAME');
			}

			$password = $this->post('password');
			if (empty($password)){
                return error('EMPTY_PASSWORD');
			}

            $user = sUser::getUserByUsername($username);

            if (!$user || !sUser::verify($password, $user->password)) {
                return error('USER_NOT_EXIST');
            }

            $user->role_id = sUserRole::getRoleStrByUid($user->uid);
            if( !sUserScheduling::checkScheduling($user) ){
                return error('WORKTIME_ERROR');
            }

            session([
                'uid' => $user->uid,
                'nickname' => $user->nickname,
                'username' => $user->username,
                'avatar' => $user->avatar,
                'role_id' => $user->role_id,
                'user' => $user->toArray()
            ]);

            sActionLog::log(sActionLog::TYPE_LOGIN, array(), $user);
            return $this->output();
        }

        $this->layout = '';
        return $this->output();
	}

	/**
	 * [logoutAction 登出界面]
	 * @return [type] [description]
	 */
	public function logoutAction(){
		$this->session->destroy();
		ActionLog::log(ActionLog::TYPE_LOGOUT, array(), $user);

        return $this->response->redirect('login/index');
	}
}

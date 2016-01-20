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

use Request, Session;

class LoginController extends ControllerBase
{
    public $_allow = array('index', 'check');

    public function checkAction() {
        $username = $this->post('username');
        if (empty($username)){
            return error('EMPTY_USERNAME');
        }

        $password = $this->post('password');
        if (empty($password)){
            return error('EMPTY_PASSWORD');
        }
        //todo:: sUser::login
        $user = sUser::getUserByUsername($username);

        if (!$user ) {
            return error('USER_NOT_EXIST');
        }
        if (!sUser::verify($password, $user->password)) {
            return error('PASSWORD_NOT_MATCH');
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

	/**
	 * [indexAction 登录界面]
	 * @return [type] [description]
	 */
    public function indexAction(){

        $this->layout = '';
        return $this->output();
	}

	/**
	 * [logoutAction 登出界面]
	 * @return [type] [description]
	 */
	public function logoutAction(){
		Session::flush();
        sActionLog::init('LOGOUT');
        sActionLog::save();

        return redirect('login/index');
	}
}

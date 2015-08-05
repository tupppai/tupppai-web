<?php namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Role;
use App\Models\UserScheduling;
use App\Models\UserRole;
use App\Models\ActionLog;

class LoginController extends ControllerBase
{

	/**
	 * [indexAction 登录界面]
	 * @return [type] [description]
	 */
    public function indexAction(){

/*
		if ($this->request->isAjax()) {
			$this->noview();
			$username = $this->request->getPost('username');

			if (empty($username)){
				return ajax_return(1, '用户名不能为空');
			}

			$password = $this->request->getPost('password');

			if (empty($password)){
				return ajax_return(2, '密码不能为空');
			}

            $user = User::findUserByUsername($username);

        	if (!$user || !User::verify($password, $user->password)) {
				return ajax_return(3, '用户名或密码错误');
            }
            $role = UserRole::findFirst("uid=".$user->uid);
            if($role && $role->role_id == Role::TYPE_STAFF) {
                //todo time
                $scheduling = UserScheduling::isWorking($user->uid);
                if(!$scheduling){
			        return ajax_return(4, '登录失败，未到上班时间', array('url' => '/Index/index'));
                }
            }

            $this->session->set('uid', $user->uid);
            $this->session->set('nickname', $user->nickname);
            $this->session->set('username', $user->username);
            $this->session->set('avatar', $user->avatar);
            $this->session->set('role_id', $role->role_id);
			ActionLog::log(ActionLog::TYPE_LOGIN, array(), $user);

			return ajax_return(0, '登录成功', array('url' => '/Index/index'));
        }
*/
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

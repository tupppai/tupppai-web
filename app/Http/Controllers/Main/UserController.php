<?php 
namespace App\Http\Controllers\Main; 

use App\Services\User AS sUser;
use Session;

class UserController extends ControllerBase {
    public $_allow = array('home', 'login');

    /**
     * 用户个人页面
     * @params $uid
     * @author brandwang
     */   
    public function homeAction($uid) {

        return $this->output();
    } 

    /**
     * 排行榜页面
     *
     * @author brandwang
     */
    public function rankingAction() {

        return $this->output();
    }
    
    /**
     * 用户登出接口
     * @author brandwang
     */
    public function logoutAction() {
        Session::flush();
        //TODO 登出操作

        //redirect
    }

    /**
     * 用户登录接口
     * @author brandwang
     */
    public function loginAction() {
        $phone    = $this->post('username', 'phone');
        $username = $this->post('username', 'username');
        $password = $this->post('password');
        
        if (empty($username) && empty($phone)) {
            return error('EMPTY_USERNAME');
        }
        if (empty($password)) {
            return error('EMPTY_PASSWORD');
        }
        
        $user = sUser::loginUser($phone, $username, $password);
        
        if (!$user) {
            return error('USER_NOT_EXIST');
        }
        
        Session::put('uid', $user['uid']);
        Session::put('user', $user);
        
        return $this->output();
    }
}
?>

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
    }

    /**
     * 用户登录接口
     * @author brandwang
     */
    public function loginAction() {
        $username = $this->post('username');
        $password = $this->post('password');
        
        if (empty($username)) {
            return error('EMPTY_USERNAME');
        }
        if (empty($password)) {
            return error('EMPTY_PASSWORD');
        }
    
        if (match_username_format($username)) {
            $user = sUser::loginUser(null, $username, $password);
        } else if (match_phone_format($username)) {
            $user = sUser::loginUser($usernma, null, $password);
        }
        
        if (!empty($user)) {
            Session::put('uid', $user['uid']);
            Session::put('user', $user);
            
            return $this->output();
        }
    }
}
?>

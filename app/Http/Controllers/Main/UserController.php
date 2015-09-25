<?php 
namespace App\Http\Controllers\Main; 

use App\Services\User AS sUser;
use App\Services\Download AS sDownload;
use App\Services\Ask AS sAsk;
use App\Services\Reply AS sReply;
use Session;

class UserController extends ControllerBase {
    public $_allow = array('*');

    public function status() {
        $this->isLogin();

        $uid  = $this->_uid;
        $user = sUser::getUserByUid($uid);
        $user = sUser::detail($user);

        return $this->output($user);
    }

    public function index() {
    }

    public function view($uid) {
        $user = sUser::getUserByUid($uid);
        $user = sUser::detail($user);

        return $this->output($user);
    }

    public function add() {

    }

    public function edit() {

    }

    public function delete() {

    }

    /**
     * 用户个人页面
     * @params $uid
     * @author brandwang
     */   
    public function homeAction($uid) {

        $user = sUser::getUserByUid($uid);
        $user = sUser::detail($user);

        return $this->output(array(
            'user'=>$user
        ));
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
        return redirect('/index/index');
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

    public function asksAction(){
        $uid  = $this->post('uid', 'int', $this->_uid);
        $page = $this->post('page', 'int', 1);
        $size = $this->post('size', 'int', 15);
        $width= $this->post('width', 'int', 300);

        $asks = sAsk::getUserAsks( $uid, time(), $page, $size);

        dd($asks);
        return $this->output($asks);
    }

    public function repliesAction(){ 
        $uid  = $this->post('uid', 'int', $this->_uid);
        $page = $this->post('page', 'int', 1);
        $size = $this->post('size', 'int', 15);
        $width= $this->post('width', 'int', 300);

        $replies = sReply::getUserReplies( $uid, $page, $size, time() );

        return $this->output($replies);
    }

    public function inprogressesAction() {
        $uid  = $this->post('uid', 'int', $this->_uid);
        $page = $this->post('page', 'int', 1);
        $size = $this->post('size', 'int', 15);
        $width= $this->post('width', 'int', 300);

        $replies = sDownload::getDownloaded( $uid, $page, $size, time() );

        return $this->output($replies);
    }
}
?>

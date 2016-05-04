<?php namespace App\Http\Controllers\Main2;

use App\Http\Controllers\Controller;
use App\Services\User as sUser;

use Request, Session, Cookie, Config, App;

use App\Facades\CloudCDN;
use Log;

class ControllerBase extends Controller
{
    // allow action for not login
    public $_allow  = array();
    // token for app
    public $_token  = null;
    // session user
    public $_uid    = null;
    public $_user   = null;
    public $_log    = null;
    public $_of     = 'json';

    public $_request = null;

    public function __construct()
    {
        #parent::__construct();
        #header("Access-Control-Allow-Origin: *");

        if( !$this->is_login() ){
            return error('LOGIN_EXPIRED', '登录超时，请重新登录');
        }
        if( env('APP_DEBUG') ){
            $_REQUEST['_of'] = 'json';
        }
        $_REQUEST['_of'] = 'json';
    }
<<<<<<< HEAD
       
    // public function isLogin(){
    //     //重构成userlanding也有登录态
    //     $this->_uid     = 1;
    //     if(!$this->_uid) {
    //     return expire('LOGIN_EXPIRE');
    //         //return expire('LOGIN_EXPIRED', '登录超时，请重新登录哦');
    //     }
    // }
=======

    public function isLogin(){
        //重构成userlanding也有登录态
        $this->_uid = 1;
        if(!$this->_uid) {
        return expire('LOGIN_EXPIRE');
            //return expire('LOGIN_EXPIRED', '登录超时，请重新登录哦');
        }
    }
>>>>>>> ccb126a164e09075479eeab4931c989d156add09
    /**
     * verify login status
     * @return boolean
     */
    private function is_login()
    {
        $method = Request::getMethod();
        $pathInfo = Request::getPathInfo();
        $segments = app()->getRoutes();
        $segments = isset($segments[$method.$pathInfo]['action']) ? $segments[$method.$pathInfo]['action'] : null;
        if(isset($segments['uses'])){
            $segments = explode('@',$segments['uses']);
            $segments = $segments[1];
        }else{
            $segments = null;
        }
        if ($this->_allow == '*') {
            return true;
        }
        else if (in_array($segments, $this->_allow)){
            return true;
        }

        $this->_uid     = _uid('uid',true);
        $this->_token   = Session::getId();
        $this->_uid     = 1;
        if($this->_uid && $this->_user = sUser::getUserByUid($this->_uid)){
            return true;
        }
        else {
            return expire('登录超时，请重新登录');
        }
    }

    public function check_token($token=null)
    {
        $token = $token? $token: Cookie::get('token');
        if($token === Session::getId())
            return true;
        return false;
    }

    protected function check_form_token(){
        if ($this->request->isPost()) {
            if ($this->security->checkToken()) {
                ;
            } else {
                return error('SYSTEM_ERROR', 'operate forbidden');
            }
        }
    }

}

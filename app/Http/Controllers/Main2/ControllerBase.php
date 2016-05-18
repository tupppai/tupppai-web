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
        $this->_uid     = _uid('uid');
        $this->_token   = Session::getId();
        
        if( env('APP_DEBUG') ){
            $_REQUEST['_of'] = 'json';
        }
        $_REQUEST['_of'] = 'json';
    }


    public function isLogin(){
        //重构成userlanding也有登录态
        //$this->_uid = 1;
        if(!$this->_uid) {
            return expire('LOGIN_EXPIRE');
            //return expire('LOGIN_EXPIRED', '登录超时，请重新登录哦');
        }
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

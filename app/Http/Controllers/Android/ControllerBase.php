<?php namespace App\Http\Controllers\Android;

use App\Http\Controllers\Controller;
use App\Services\User as sUser;

use Request, Session, Config, App;

use App\Facades\CloudCDN;

class ControllerBase extends Controller
{
    // allow action for not login
    public $_allow  = array();
    // token for app
    public $_token  = null;
    // session user
    public $_user   = null;
    public $_log    = null;

    public $_request = null;

    public function __construct()
    {
        #parent::__construct();
        #header("Access-Control-Allow-Origin: *");

        if( !$this->is_login() ){
            return error('LOGIN_EXPIRED');
        }
    }
       
    /**
     * verify login status
     * @return boolean
     */
    private function is_login()
    {
        $this->_uid     = session('uid');
        $this->_token   = session_id();

        if(env('APP_DEBUG') && !$this->_uid){
            $this->_uid = 1;
            session(['uid' => '1']);
        }
        return true;
        //todo: middle ware
        $action_name = $this->dispatcher->getActionName();
        if (in_array($action_name, $this->_allow)){
            return true;
        } 
        else if($this->_uid && $this->_user = sUser::getUserByUid($this->_uid)){
            return true;
        } 
        else {
            return false;
        }
    }

    public function check_token($token='')
    {
        if($token === '')
            if($this->cookies->has('token'))
                $token = $this->cookies->get('token')->getValue();
        // phalcon的session机制是只有第一次使用的时候才会调用这个
        @session_start();
        if($token === session_id())
                return true;    
        return false;
    }

    protected function check_form_token(){
        if ($this->request->isPost()) {
            if ($this->security->checkToken()) {
                ;
            } else {
                ajax_return(0, '重复操作！');
            }
        }
    }

}

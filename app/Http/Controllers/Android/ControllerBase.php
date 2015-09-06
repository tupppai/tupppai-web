<?php namespace App\Http\Controllers\Android;

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
            return error('LOGIN_EXPIRED');
        }
        if( env('APP_DEBUG') ){
            $_REQUEST['_of'] = 'json';
        }
        $_REQUEST['_of'] = 'json';
    }
       
    /**
     * verify login status
     * @return boolean
     */
    private function is_login()
    {
        $this->_uid     = session('uid');
        $this->_token   = Session::getId();

        $this->_uid = 253;

        /*
        if(env('APP_DEBUG') && !$this->_uid){
            $this->_uid = 1;
            session(['uid' => '1']);
        }
         */
        return true;

        //todo: move to middle ware
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

<?php namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;

use Request, Session, Config, App, DB;

use App\Services\User as sUser;

class ControllerBase extends Controller
{
    public $_uid = '';
    public $layout      = "master";
    public $_allow      = array();
    private $controller = null;
    private $action     = null;

	public function __construct(Request $request)
    {
        $this->_uid = session('uid');
        $this->user = session('user');
        $this->request      = $request;
        $this->controller   = $request::segment(1);
        $this->action       = $request::segment(2);
        
        if( !$this->is_login() ){
            return error('LOGIN_EXPIRED');
        }
    }
    
    private function is_login() {
        if( !in_array($this->action, $this->_allow) && !$this->_uid ) {
            return false;
        }
        return true;
    }

    public function output_html($data=array(), $info="") {
        $controller = $this->controller;
        $action     = $this->action;
        
        # 统一返回用户信息        
        $data['_user'] = $this->user;
        $data['_uid']  = $this->_uid;
        
        if ($this->layout) {
            $content = view("main.$controller.$action", $data);
            $data['content'] = $content;

            $layout = view("main.layout.".$this->layout, $data);
        } else {
            $layout = view("main.$controller.$action", $data);
        }
        return $layout;
    }    
    
    public function output_json( $data = array(), $info = '' ){
        $data = json_format(1, $this->_code, $data, $info);
        
        # 统一返回用户信息        
        $data['user'] = $this->user;
        $data['_uid'] = $this->_uid;

        #return $data;
        return response()->json( $data );
    }
}

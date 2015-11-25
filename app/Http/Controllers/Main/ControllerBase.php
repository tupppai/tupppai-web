<?php namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;

use Request, Session, Config, App, DB;

use App\Services\User as sUser;

class ControllerBase extends Controller
{
    public $_uid = '';
    public $_of  = 'json';
    public $layout      = "master";
    public $_allow      = array();
    private $controller = null;
    private $action     = null;

	public function __construct(Request $request)
    {
        $this->request      = $request;
        $this->controller   = $request::segment(1);
        $this->action       = $request::segment(2);

        // 获取app的登录状态,目前只有接口地址为record的时候才允许
        $token = $this->get('token', 'string');
        if($this->controller == 'record' && $token) {
            Session::setId($token);
        }

        $this->_uid = session('uid');
        $this->user = session('user');
    }

    public function isLogin(){
        //重构成userlanding也有登录态
        if(!$this->_uid) {
            return error('LOGIN_EXPIRED', '登录超时，请重新登录哦');
        }
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

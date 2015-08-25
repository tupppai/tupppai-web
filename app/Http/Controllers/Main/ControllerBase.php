<?php namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;

use Request, Session, Config, App, DB;

use App\Services\User as sUser;

class ControllerBase extends Controller
{
    public $_uid = '';
    public $layout      = "master";
    private $controller = null;
    private $action     = null;

	public function __construct(Request $request)
    {
        $this->_uid = session('uid');
        $this->user = session('user');
        $this->request      = $request;
        $this->controller   = $request::segment(1);
        $this->action       = $request::segment(2);
    }

    public function output_html($data=array(), $info="") {
        $controller = $this->controller;
        $action     = $this->action;
        
        if ($this->layout) {
            $content = view("main.$controller.$action", $data);

            $layout = view("main.layout.master", array(
                'content' => $content
            ));
        } else {
            $layout = view("main.$controller.$action", $data);
        }

        return $layout;
    }
    
    /**
     * description
     *
     */
}

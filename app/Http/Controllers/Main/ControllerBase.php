<?php namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;

use Request, Session, Config, App, DB;

use App\Services\User as sUser;

class ControllerBase extends Controller
{
    public $_uid = '';
    private $controller = null;
    private $action     = null;

	public function __construct(Request $request)
    {
        $this->_uid = session('uid');
        $this->user = session('user');
        $this->request      = $request;
        $this->controller   = $request::segment(2);
        $this->action       = $request::segment(3);
    }
}

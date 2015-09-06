<?php 
namespace App\Http\Controllers\Main;

use App\Models\App;
use App\Models\ActionLog;

use App\Services\User;

class IndexController extends ControllerBase {

    public $_allow = array('index', 'hot');

    // page index
    public function indexAction(){

        return $this->output();
    }
}

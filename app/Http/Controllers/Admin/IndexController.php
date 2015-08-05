<?php namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Usermeta;

class IndexController extends ControllerBase
{

    public function indexAction() {
        //$this->output_table(User::find());
    }
    public function aaaActin(){
        $this->noview();
        echo 'asdfadsf';
        exit;
    }
}


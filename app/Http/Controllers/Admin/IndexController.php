<?php namespace App\Http\Controllers\Admin;

use App\Services\ThreadCategory as sThreadCategory;

class IndexController extends ControllerBase
{
    public function indexAction() {
        return $this->output();
    }
}



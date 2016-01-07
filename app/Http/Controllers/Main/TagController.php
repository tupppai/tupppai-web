<?php namespace App\Http\Controllers\Main;

use App\Services\Tag as sTag;
use App\Services\IException as sIException;

use App\Models\Tag as mTag;

class TagController extends ControllerBase{

    public function index(){
        $page = $this->get('page', 'int');
        $size = $this->get('size', 'int');
        $cond = array();

        $tags = sTag::getTagsByCond($cond, $page,$size);

        return $this->output( $tags );
    }
}

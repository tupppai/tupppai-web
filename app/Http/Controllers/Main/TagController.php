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

    public function show()
    {
        $tag = $this->get('tag','string',null);
        $tags = sTag::getTagsLikeName($tag);
        if($tags && empty($tags->toArray())){
        //如果不存在,则新增
            $tag = sTag::addNewTag(_uid(),$tag);
            $tags['tag_id'] = $tag->id;
            $tags['name'] = $tag->name;
        }
        return $this->output( $tags );
    }
}

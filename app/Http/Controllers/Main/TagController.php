<?php namespace App\Http\Controllers\Main;

use App\Formats\Tags as fTags;
use App\Services\Tag as sTag;
use App\Services\IException as sIException;

use App\Models\Tag as mTag;
use App\Services\ThreadTag as sThreadTag;

class TagController extends ControllerBase{

    public function index(){
        $page = $this->get('page', 'int');
        $size = $this->get('size', 'int');
        $cond = array();

        $tags = sTag::getTagsByCond($cond, $page,$size);

        return $this->output( $tags );
    }
    //关键字查询  如果不存在 则创建
    public function check()
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
    //根据tag_id 查询reply
    public function show()
    {
        $tag_id = $this->get('tag_id','string',null);
        $page   = $this->get('page','int',0);
        if(empty($tag_id)){
            return error('EMPTY_ARGUMENTS','缺少tag_id');
        }
        $reply_ids = sThreadTag::getRepliesByTagId($tag_id,$page,15);
        if(empty($reply_ids)){
            $this->output();
        }
        $reply_ids = array_column($reply_ids->toArray(),'reply');
        //todo 去重 reply_id?
        return $this->output($reply_ids);


    }

    public function UserHistoryForTag()
    {
        $page = $this->get('page','int',0);
        $size = $this->get('size','int',8);
        $histories    = sThreadTag::searchThreadTag(['user_id' => _uid(), 'order_by' => true], $page ,$size);
        foreach($histories as $history){
            $data       = fTags::UserHistoryForTag($history);
        }
        return $this->output($data);
    }
}

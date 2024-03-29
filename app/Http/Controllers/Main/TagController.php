<?php namespace App\Http\Controllers\Main;

use App\Services\Tag as sTag;
use App\Services\IException as sIException;

use App\Models\Tag as mTag;
use App\Services\ThreadTag as sThreadTag;

class TagController extends ControllerBase{

    public function index(){
        $page = $this->get('page', 'int', 1);
        $size = $this->get('size', 'int', 15);
        $type = $this->get('type', 'string');
        $cond = ['status'=>mTag::STATUS_NORMAL];

        switch( $type ){
            case 'hot':
                $cond['status'] = mTag::STATUS_DONE;
                break;
            default:
                break;
        }
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
    //返回tag标签信息
    public function show($tag_id)
    {
        if(empty($tag_id)){
            return error('EMPTY_ARGUMENTS','缺少tag_id');
        }
        $tag = sTag::getTagById( $tag_id );
        if( !$tag ){
            return error('TAG_NOT_EXIST', '标签不存在');
        }
        $tag = sTag::brief( $tag );
        //todo 去重 reply_id?
        return $this->output($tag);
    }

    public function UserHistoryForTag()
    {
        $page = $this->get('page','int',1);
        $size = $this->get('size','int',5);
        $thread_tags    = sThreadTag::searchThreadTag(['user_id' => _uid(), 'order_by' => true], $page ,$size);
        $data = [];
        foreach($thread_tags as $thread_tag){
            $data[] = sTag::brief(sTag::getTagById( $thread_tag->tag_id ));
        }
        return $this->output($data);
    }
}

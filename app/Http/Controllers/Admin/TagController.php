<?php namespace App\Http\Controllers\Admin;

use App\Models\User,
    App\Models\Tag as mTag,
    App\Models\ThreadTag as mThreadTag,
    App\Models\ActionLog;

use App\Services\Tag as sTag;

class TagController extends ControllerBase{
    public function indexAction(){
        return $this->output();
    }

    public function list_tagsAction(){
        $tag = new mTag;
        $thread_tag = new mThreadTag;
        // 检索条件
        $cond = array();
        $cond['id']             = $this->post("tag_id", "int");
        $cond['name']           = array(
            $this->post("tag_name", "string"),
            'LIKE'
        );

        // 用于遍历修改数据
        $data  = $this->page($tag, $cond);

        foreach($data['data'] as $row){
            $tag_id = $row->id;
            $name   = $row->name;
            $status = $row->status==mTag::STATUS_NORMAL?mTag::STATUS_DELETED: mTag::STATUS_NORMAL;
            $row->name = $row->name." <a href='#edit_tag' data-toggle='modal' data-id='$tag_id' data-name='$name' class='edit'> 编辑</a>";
            $row->create_time = date('Y-m-d H:i:s', $row->create_time);
            $row->update_time = date('Y-m-d H:i:s', $row->update_time);
            if($row->status == mTag::STATUS_NORMAL) {
                $row->oper = "<a href='#' style='color:red' class='status' data-status='$status' data-id='$tag_id'>点击下架</a>";
            }
            else {
                $row->oper = "<a href='#' class='status' data-status='$status' data-id='$tag_id'>上架</a>";
            }
            $row->status = $row->status==mTag::STATUS_NORMAL?'正常':'下架';

            $row->user_count = $thread_tag->get_thread_user_count($row->id);
            $row->thread_count = $thread_tag->get_thread_count($row->id);

        }
        // 输出json
        return $this->output_table($data);
    }

    public function set_tagAction(){
        $tag_id  = $this->post('tag_id', 'int', 0 );
        $tagName = $this->post('tag_name', 'string');

        if(is_null($tagName)){
            return error('EMPTY_TAG_NAME');
        }

        $tag = sTag::updateTag(
            $this->_uid,
            $tag_id,
            $tagName
        );

        return $this->output(['id'=>$tag->id]);
    }

    public function set_statusAction(){
        $tag_id  = $this->post('id', 'int', 0 );
        $status  = $this->post('status', 'int'); //need default?

        if(is_null($tag_id)){
            return error('EMPTY_TAG_ID');
        }

        $tag = mTag::find($tag_id);
        if($tag) {
            $tag->status=$status;
            $tag->save();
        }

        return $this->output();
    }

    public function delete_tagAction(){
        $tag_id  = $this->post("tag_id", "int", 0 );
        $uid  = $this->_uid;
        if( !$tag_id ){
            return error('EMPTY_TAG_ID');
        }

        $tag = sTag::deleteTag( $uid, $tag_id );
        return $this->output( ['id'=>$tag->id],'删除成功' );
    }
}

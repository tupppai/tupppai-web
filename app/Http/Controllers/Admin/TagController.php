<?php namespace App\Http\Controllers\Admin;

use App\Models\User,
    App\Models\Tag as mTag,
    App\Models\ThreadTag as mThreadTag,
    App\Models\ActionLog;

use App\Services\Tag as sTag;
use App\Services\User as sUser;
use App\Services\Ask as sAsk;
use App\Services\Reply as sReply;
use App\Services\Upload as sUpload;

use App\Facades\CloudCDN, Html;

class TagController extends ControllerBase{

    public function indexAction(){
        return $this->output();
    }

    public function usersAction() {
        return $this->output();
    }

    public function threadsAction() {
        return $this->output();
    }

    public function list_threadsAction() {
        $tag_id = $this->post('tag_id', 'int');
        $thread_tag = new mThreadTag;
        // 检索条件
        $cond = array();
        $cond['tag_id']             = $this->post("tag_id", "int");

        // 用于遍历修改数据
        $data  = $this->page($thread_tag, $cond);

        foreach($data['data'] as $row) {
            $model = null;
            if($row->target_type == mTag::TYPE_ASK) {
                $model = sAsk::getAskById($row->target_id);
                $upload_ids = explode(',', $model->upload_ids);
                $upload = sUpload::getUploadById($upload_ids[0]);

                $row->image_url = CloudCDN::file_url($upload->savename);
                $row->image_url= Html::image($row->image_url, 'avatar', array('width'=>50));
            }
            else {
                $model = sReply::getReplyById($row->target_id);
                $row->image_url = CloudCDN::file_url($model->upload->savename);
                $row->image_url= Html::image($row->image_url, 'avatar', array('width'=>50));
            }

            $user = sUser::getUserByUid($model->uid);

            $row->phone     = $user->phone;
            $row->username  = $user->username;
            $row->nickname  = $user->nickname;
            $row->create_by = $user->uid;
            $row->uid       = $user->uid;
            $row->avatar    = Html::image($user->avatar, 'avatar', array('width'=>50));

            $row->status = $row->status==mTag::STATUS_NORMAL?'正常':'删除';
        }
        return $this->output_table($data);
    }

    public function list_usersAction() {
        $thread_tag = new mThreadTag;
        // 检索条件
        $cond = array();
        $cond['tag_id']     = $this->post("tag_id", "int");
        $cond['create_by']  = array(
            "create_by",
            'DISTINCT'
        );

        // 用于遍历修改数据
        $data  = $this->page($thread_tag, $cond);

        foreach($data['data'] as $row) {
            $user = sUser::getUserByUid($row->create_by);

            $row->create_by = $user->uid;
            $row->phone     = $user->phone;
            $row->username  = $user->username;
            $row->nickname  = $user->nickname;
            $row->uid       = $user->uid;
            $row->avatar    = Html::image($user->avatar, 'avatar', array('width'=>50));
        }
        return $this->output_table($data);
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
            // $row->name = $row->name." <a href='#edit_tag' data-toggle='modal' data-id='$tag_id' data-name='$name' class='edit'> 编辑</a>";
            $row->create_time = date('Y-m-d H:i:s', $row->create_time);
            $row->update_time = date('Y-m-d H:i:s', $row->update_time);
            if($row->status == mTag::STATUS_NORMAL) {
                $row->oper = "<a href='#' style='color:red' data-id='$row->id' class='btn offline'>点击下架</a>";
            }
            else {
                $row->oper = "<a href='#upload_tag_cover' data-id='$row->id' class='btn online' data-toggle='modal'>上架</a>";
            }
            if( $row->remark ){
                $row->remark = '<img src="'.$row->remark.'" class="avatar"/>';
            }
            else{
                $row->remark = '无';
            }

            $row->user_count = "<a href='/tag/users?tag_id=$tag_id'>".$thread_tag->get_thread_user_count($row->id)."</a>";
            $row->thread_count = "<a href='/tag/threads?tag_id=$tag_id'>".$thread_tag->get_thread_count($row->id)."</a>";
        }
        // 输出json
        return $this->output_table($data);
    }

    public function set_tagAction(){
        $tag_id  = $this->post('tag_id', 'int', 0 );
        $tagName = $this->post('tag_name', 'string');
        $status  = $this->post('status', 'int', mTag::STATUS_NORMAL);

        if(is_null($tagName)){
            return error('EMPTY_TAG_NAME');
        }

        $tag = sTag::updateTag(
            $this->_uid,
            $tag_id,
            $tagName,
            $status
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

    public function update_statusAction(){
        $tag_id = $this->post( 'tag_id', 'int' );
        $status = $this->post( 'status', 'int' );
        $cover  = $this->post( 'tag_cover', 'url' );
        if( !$tag_id ){
            return error('EMPTY_TAG_ID', '请选择要处理的标签');
        }

        if( $status && !$cover ){
            return error( 'EMPTY_COVER', '请上传封面图。' );
        }

        $tag = sTag::updateStatus( $tag_id, $status, $cover );

        return $this->output(['result'=>'ok']);
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

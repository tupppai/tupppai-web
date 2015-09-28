<?php namespace App\Http\Controllers\Admin;

use App\Models\User as mUser;
use App\Models\Ask as mAsk;
use App\Models\Reply as mReply;
use App\Models\Role as mRole;
use App\Models\UserScheduling as  mUserScheduling;
use App\Models\UserRole as mUserRole;
use App\Models\ActionLog as mActionLog;
use App\Models\Category as mCategory;
use App\Models\ThreadCategory as mThreadCategory;

use App\Services\User as sUser,
    App\Services\Role as sRole,
    App\Services\Ask as sAsk,
    App\Services\Reply as sReply,
    App\Services\UserRole as sUserRole,
    App\Services\Upload as sUpload,
    App\Services\Category as sCategory,
    App\Services\ThreadCategory as sThreadCategory,
    App\Services\UserScheduling as sUserScheduling,
    App\Services\ActionLog as sActionLog;

use App\Facades\CloudCDN;

class VerifyController extends ControllerBase
{

    public function threadAction(){

        return $this->output();
	}

    public function list_threadsAction() {
        $this->rowLength  = 4;

        $beg_time = $this->post('beg_time', 'string');
        $end_time = $this->post('end_time', 'string');
        $role_id  = $this->post('role_id', 'int');

        $type     = $this->post('type', 'string');

        $user   = new mUser;
        $ask    = new mAsk;
        $reply  = new mReply;
        $tcTable = (new mThreadCategory)->getTable();
        // 检索条件
        $cond = array();
        $cond[$user->getTable().'.uid']        = $this->post("uid", "int");
        $cond[$user->getTable().'.username']   = array(
            $this->post("username", "string"),
            "LIKE",
            "AND"
        );

        $join = array();
        $join['User'] = 'uid';
        $askJoin = $join;
        $askCond = $cond;
        $replyJoin = $join;
        $replyCond = $cond;
        if( $type == 'unreviewed' ){
            $askCond[$tcTable.'.status'] = mThreadCategory::STATUS_CHECKED;
            $replyCond[$tcTable.'.status'] = mThreadCategory::STATUS_CHECKED;

            $askCond[$tcTable.'.target_type'] = 1;
            $replyCond[$tcTable.'.target_type'] = 2;
            $askJoin['ThreadCategory'] = array('id', 'target_id');
            $replyJoin['ThreadCategory'] = array('id', 'target_id');
        }


        $arr = array();

        $asks      = $this->get_threads($ask, $askCond, $askJoin);
        $replies   = $this->get_threads($reply, $replyCond, $replyJoin);

        $ask_arr   = $asks['data'];
        $reply_arr = $replies['data'];
        sort($ask_arr);
        sort($reply_arr);

        $data   = array_merge($ask_arr, $reply_arr);
        sort($data);
        //$data   = array_slice($data, 0, sizeof($data)/2);

        $total  = $asks['recordsTotal'] + $replies['recordsTotal'];

        return $this->output_grid($data, $total);
    }

    private function get_threads($model, $cond, $join){
        $cond[$model->getTable().'.id']      = $this->post('id', 'int');
        $cond[$model->getTable().'.desc']    = $this->post('desc', 'string');
        $orderBy = array($model->getTable().'.create_time desc');
        $data    = $this->page($model, $cond, $join, $orderBy);

        $data['data'] = $this->format($data['data']);
        return $data;
    }

    private function format($data, $index = null){
        $arr = array();
        $roles      = array_reverse(sRole::getRoles()->toArray());

        foreach($data as $row) {
            $index = $row->create_time;
            //ask:upload_ids, reply:upload_id
            $row->type    = $row->getTable();
            if( $row->type == 'asks' ){
                $upload_ids = $row->upload_ids;
                $target_type = mAsk::TYPE_ASK;
            }
            else{
                $upload_ids = $row->upload_id;
                $target_type = mAsk::TYPE_REPLY;
            }
            $row->target_type    = $target_type;
            $uploads = sUpload::getUploadByIds(explode(',', $upload_ids));
            foreach($uploads as $upload) {
                $upload->image_url = CloudCDN::file_url($upload->savename);
            }

            $row->is_hot = 0;
            $categories = sThreadCategory::getCategoryIdsByTarget( $target_type, $row->id );
            if( in_array(mThreadCategory::CATEGORY_TYPE_POPULAR, $categories ) ){
                $row->is_hot = 1;
            }

            $desc = json_decode($row->desc);
            $row->desc    = is_array($desc)? $desc[0]->content: $row->desc;
            $row->uploads = $uploads;
            $row->roles   = $roles;
            $role_id      = sUserRole::getFirstRoleIdByUid($row->uid);
            $row->role_id     = $role_id;
            $row->categories  = $categories;
            $row->create_time = date('Y-m-d H:i:s', $row->create_time);
            $row->user_status = sUser::getUserByUid( $row->uid )->status;

            $arr[$index] = $row;
        }

        return $arr;
    }

    public function categoriesAction(){

        return $this->output();
    }


    public function set_thread_statusAction( ){
        $target_id = $this->post( 'target_id', 'int' );
        $target_type = $this->post( 'target_type', 'int' );
        $status = $this->post( 'status', 'int' );
        $reason = $this->post( 'reason', 'int' );

        $tc = sThreadCategory::setThreadStatus( $this->_uid, $target_type, $target_id, $status, $reason );
        if( $target_type == 1 ){
            $ask = sAsk::getAskById( $target_id );
            $thread = sAsk::updateAskStatus( $ask, $status, $this->_uid );
        }
        else{
            $reply = sReply::getReplyById( $target_id );
            $thread = sReply::updateReplyStatus( $reply, $status, $this->_uid );
        }

        return $this->output( ['result'=>'ok'] );
    }

    public function set_thread_categoryAction(){
        $target_id = $this->post( 'target_id', 'int' );
        $target_type = $this->post( 'target_type', 'int' );
        $category_from = $this->post( 'category_from', 'string', 0 );
        $category_id = $this->post( 'category', 'string', mThreadCategory::CATEGORY_TYPE_POPULAR );//热门的

        $tc = sThreadCategory::setCategory( $this->_uid, $target_type, $target_id, $category_from, $category_id );
        return $this->output( ['result'=>'ok'] );
    }

    public function set_thread_as_pouplarAction(){
        $target_id = $this->post( 'target_id', 'int' );
        $target_type = $this->post( 'target_type', 'int' );
        $status = $this->post( 'status', 'string' );
        if( $status ){
            $category_from = 0;
            $category_id = mThreadCategory::CATEGORY_TYPE_POPULAR;
        }
        else{
            $category_from = mThreadCategory::CATEGORY_TYPE_POPULAR;
            $category_id = 0;
        }

        $tc = sThreadCategory::setCategory( $this->_uid, $target_type, $target_id, $category_from, $category_id );
        return $this->output( ['result'=>'ok'] );

    }
}

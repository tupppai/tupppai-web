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
    App\Services\Thread as sThread,
    App\Services\UserScheduling as sUserScheduling,
    App\Services\ActionLog as sActionLog;

use App\Facades\CloudCDN;

class VerifyController extends ControllerBase
{

    public function testAction() {
        return $this->output();
    }

    public function threadAction(){
        return $this->output();
	}

    public function list_threadsAction() {
        $beg_time = $this->post('beg_time', 'string');
        $end_time = $this->post('end_time', 'string');
        $role_id  = $this->post('role_id', 'int');

        $type     = $this->post('type', 'string');
        $page     = $this->post('page', 'int', 1);
        $size     = $this->post('length', 'int', 15);
        $cond = array();
        /*
<<<<<<< HEAD
=======
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
>>>>>>> a57738c950141e322b595694a7675286f0e60739
         */
        switch ($type) {
            case 'unreviewed':
                $cond['status'] = mThreadCategory::STATUS_CHECKED;
                break;
            case 'app':
                $cond['status'] = mThreadCategory::STATUS_NORMAL;
                $cond['category_id'] = mThreadCategory::CATEGORY_TYPE_APP_POPULAR;
                break;
            case 'pc':
                $cond['status'] = mThreadCategory::STATUS_NORMAL;
                $cond['category_id'] = mThreadCategory::CATEGORY_TYPE_PC_POPULAR;
                break;
            default:
                # code...
                break;
        }

        $thread_ids = sThread::getThreadIds($cond, $page, $size);

        $data = $this->format($thread_ids['result']);

        return $this->output_table(array(
            'data'=>$data,
            'recordsTotal'=>$thread_ids['total']
        ));
    }

    private function format($data, $index = null){
        $arr = array();
        $roles      = array_reverse(sRole::getRoles()->toArray());

        foreach($data as $row) {
            if($row->type == mUser::TYPE_ASK) {
                $row = sAsk::getAskById($row->id, false);
                $upload_ids = $row->upload_ids;
                $target_type = mAsk::TYPE_ASK;
            }
            else {
                $row = sReply::getReplyById($row->id);
                $upload_ids = $row->upload_id;
                $target_type = mAsk::TYPE_REPLY;
            }
            $row->target_type    = $target_type;

            $index = $row->create_time;

            $uploads = sUpload::getUploadByIds(explode(',', $upload_ids));
            foreach($uploads as $upload) {
                $upload->image_url = CloudCDN::file_url($upload->savename);
            }
            //$row->is_hot = (bool)sThreadCategory::checkThreadIsPopular( $target_type, $row->id );
            $row->checked_as_hot = (bool)sThreadCategory::checkedThreadAsPopular( $target_type, $row->id );
            $row->checked_as_pchot = (bool)sThreadCategory::checkedThreadAsCategoryType( $target_type, $row->id, mThreadCategory::CATEGORY_TYPE_PC_POPULAR );
            $row->checked_as_apphot = (bool)sThreadCategory::checkedThreadAsCategoryType( $target_type, $row->id, mThreadCategory::CATEGORY_TYPE_APP_POPULAR );

            $desc = json_decode($row->desc);
            $row->desc    = !empty($desc) && is_array($desc)? $desc[0]->content: $row->desc;
            $row->uploads = $uploads;
            $row->roles   = $roles;
            $role_id      = sUserRole::getFirstRoleIdByUid($row->uid);
            $row->role_id     = $role_id;
            $row->create_time = date('Y-m-d H:i:s', $row->create_time);

            $user = sUser::getUserByUid( $row->uid );
            $row->avatar = $user->avatar;
            $row->nickname = $user->nickname;
            $row->username = $user->username;
            $row->user_status = $user->status;
            $row->is_god = $user->is_god;

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
        $status = $this->post( 'status', 'string', 0 );
        $target_id = $this->post( 'target_id', 'int' );
        $target_type = $this->post( 'target_type', 'int' );
        $category_from = $this->post( 'category_from', 'string', 0 );
        $category_id = $this->post( 'category', 'string', mThreadCategory::CATEGORY_TYPE_POPULAR );//热门的

        $tc = sThreadCategory::setCategory( $this->_uid, $target_type, $target_id, $category_from, $status );
        return $this->output( ['result'=>'ok'] );
    }

    public function set_thread_as_pouplarAction(){
        $type   = $this->post( 'type', 'int' );
        $statuses = $this->post( 'status', 'string' );
        $target_ids   = $this->post( 'target_id', 'int' );
        $target_types = $this->post( 'target_type', 'int' );
        $category_id = mThreadCategory::CATEGORY_TYPE_POPULAR;
        switch( $type ){
            case 'pc':
                $category_id = mThreadCategory::CATEGORY_TYPE_PC_POPULAR;
                break;
            case 'app':
                $category_id = mThreadCategory::CATEGORY_TYPE_APP_POPULAR;
                break;
        }
        if( !is_array($target_ids) ){
            $target_ids = [$target_ids];
            $target_types = [$target_types];
            $statuses = [$statuses];
        }
        foreach( $target_ids as $key => $target_id ){
            $target_type = $target_types[$key];
            $tc = sThreadCategory::setCategory( $this->_uid, $target_type, $target_id, $category_id, $statuses[$key] );
        }
        return $this->output( ['result'=>'ok'] );
    }

    public function delete_popularAction(){
        $target_ids   = $this->post( 'target_ids', 'int' );
        $target_types = $this->post( 'target_types', 'int' );
        $category_type = $this->post( 'category', 'string');
        switch( $category_type ){
            case 'app':
                $category_id = mThreadCategory::CATEGORY_TYPE_APP_POPULAR;
                break;
            case 'pc':
                $category_id = mThreadCategory::CATEGORY_TYPE_PC_POPULAR;
                break;
            default:
                $category_id = false;
        }

        foreach( $target_ids as $key => $target_id ){
            sThreadCategory::deleteThread( $this->_uid, $target_types[$key], $target_id,  mThreadCategory::STATUS_DELETED, '', $category_id );
        }

        return $this->output_json(['result'=>'ok']);

    }
}

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
    App\Services\Device as sDevice,
    App\Services\UserRole as sUserRole,
    App\Services\Upload as sUpload,
    App\Services\Category as sCategory,
    App\Services\Download as sDownload,
    App\Services\ThreadCategory as sThreadCategory,
    App\Services\Thread as sThread,
    App\Services\UserScheduling as sUserScheduling,
    App\Services\Recommendation as sRec,
    App\Services\ActionLog as sActionLog;

use App\Facades\CloudCDN;
use App\Jobs\UpReply;
use Queue, Carbon\Carbon;


class VerifyController extends ControllerBase
{

    public function testAction() {
        return $this->output();
    }

    public function threadAction(){

        return $this->output( ['pc_host'=>'http://'.env('MAIN_HOST')] );
	}

    public function list_threadsAction() {
        $beg_time = $this->post('beg_time', 'string');
        $end_time = $this->post('end_time', 'string');

        $user_type   = $this->post('user_type', 'int');
        $user_role   = $this->post('user_role', 'int');
        $thread_type = $this->post('thread_type', 'string');
        $target_type = $this->post('target_type', 'string', 'all');
        $nickname    = $this->post('nickname', 'string');

        $uid = $this->post('uid', 'int');
        $desc = $this->post('desc', 'string');
        $thread_id = $this->post('thread_id', 'int');

        $type     = $this->get('type', 'string');
        $page     = $this->post('page', 'int', 1);
        $size     = $this->post('length', 'int', 15);

        $cond = [
            'target_type' => $target_type,
            'thread_type' => $thread_type,
            'user_type'   => $user_type,
            'user_role'   => $user_role,
            'uid'         => $uid,
            'thread_id'   => $thread_id,
            'desc'        => $desc,
            'nickname'    => $nickname,
            'type'        => $type
        ];

        $thread_ids = sThread::getThreadIds($cond, $page, $size);

        $data = $this->format($thread_ids['result'], null, $type );

        return $this->output_table(array(
            'data'=>$data,
            'recordsTotal'=>$thread_ids['total']
        ));
    }

    private function format($data, $index = null, $type ){
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
                $ask = sAsk::getAskById($row->ask_id, false);
                $upload_ids = $ask->upload_ids;

                $row->image_url = sUpload::getImageUrlById($row->upload_id);
                $target_type = mAsk::TYPE_REPLY;
            }
            $row->target_type    = $target_type;

            $index = $row->create_time;

            $uploads = sUpload::getUploadByIds(explode(',', $upload_ids));
            foreach($uploads as $upload) {
                $upload->image_url = CloudCDN::file_url($upload->savename);
            }
            //$row->is_hot = (bool)sThreadCategory::checkThreadIsPopular( $target_type, $row->id );
            $hot = sThreadCategory::brief( sThreadCategory::getCategoryByTarget( $target_type, $row->id, mThreadCategory::CATEGORY_TYPE_POPULAR ) );
            $pc_hot = sThreadCategory::brief( sThreadCategory::getCategoryByTarget( $target_type, $row->id, mThreadCategory::CATEGORY_TYPE_PC_POPULAR ) );
            $app_hot = sThreadCategory::brief( sThreadCategory::getCategoryByTarget( $target_type, $row->id, mThreadCategory::CATEGORY_TYPE_APP_POPULAR ) );

            $row->is_hot = (bool)($hot['status']!=0);
            $row->is_pchot = (bool)($pc_hot['status']%5!=0);//0 && -5
            $row->is_apphot = (bool)($app_hot['status']%5!=0);//0 && -5
            switch( $type ){
                case 'unreviewed':
                    $thread_status = $hot['status'];
                    break;
                case 'app':
                    $thread_status = $app_hot['status'];
                    break;
                case 'pc':
                    $thread_status = $pc_hot['status'];
                    break;
                default:
                    $thread_status = -1;
            }
            $row->thread_status = $thread_status;
            $row->recRole = sRec::getRecRoleIdByUid( $row->uid );
            $roles = sUserRole::getRoleStrByUid( $row->uid );
            $row->user_roles   = $roles;
            $row->is_star = in_array(mRole::ROLE_STAR, $roles);
            $row->is_in_blacklist = in_array(mRole::ROLE_BLACKLIST, $roles);

            $desc = json_decode($row->desc);
            $row->desc    = !empty($desc) && is_array($desc)? $desc[0]->content: $row->desc;
            $row->uploads = $uploads;
            $row->roles   = sRole::getRoles( );
            $role_id      = sUserRole::getFirstRoleIdByUid($row->uid);
            $row->role_id     = $role_id;
            $row->create_time = date('Y-m-d H:i:s', $row->create_time);
            $row->update_time = date('Y-m-d H:i:s', $row->update_time);

            $user = sUser::getUserByUid( $row->uid );
            $row->avatar = $user->avatar;
            $row->nickname = $user->nickname;
            $row->username = $user->username;
            $row->user_status = $user->status;
            $row->is_god = $user->is_god;

            $row->download_count = sDownload::countDownload($target_type, $row->id);

            $row->device = sDevice::getDeviceById($row->device_id);
            $row->recRoleList = sRole::getRoles( [mRole::ROLE_STAR, mRole::ROLE_BLACKLIST] );

            $arr[] = $row;
        }
        return array_values($arr);
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
        $status = $this->post( 'status', 'int', mThreadCategory::STATUS_DELETED );
        switch( $category_type ){
            case 'app':
                $category_id = mThreadCategory::CATEGORY_TYPE_APP_POPULAR;
                break;
            case 'pc':
                $category_id = mThreadCategory::CATEGORY_TYPE_PC_POPULAR;
                break;
            case 'unreviewed':
                $category_id = mThreadCategory::CATEGORY_TYPE_POPULAR;
                break;
            default:
                $category_id = false;
        }

        foreach( $target_ids as $key => $target_id ){
            sThreadCategory::deleteThread( $this->_uid, $target_types[$key], $target_id, $status, '', $category_id );
        }

        return $this->output_json(['result'=>'ok']);
    }

    public function upAction(){
        $target_id = $this->post( 'target_id', 'int', 0 );
        $target_type = $this->post( 'target_type', 'int', 0 );
        $puppet_uid = $this->post( 'puppetId', 'int',0 );
        $delay = abs( $this->post('delay', 'int', 0 ) ); //>0

        if( $target_type == mReply::TYPE_REPLY ){
            $up_delay = Carbon::now()->addSeconds($delay);
            Queue::later( $delay, new UpReply( $target_id, $puppet_uid ));
        }

        return $this->output_json( ['result'=>'ok'] );
    }
}

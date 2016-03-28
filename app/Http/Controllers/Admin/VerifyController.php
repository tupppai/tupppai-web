<?php namespace App\Http\Controllers\Admin;

use App\Models\User as mUser;
use App\Models\Ask as mAsk;
use App\Models\Reply as mReply;
use App\Models\Role as mRole;
use App\Models\Tag as mTag;
use App\Models\ThreadTag as mThreadTag;
use App\Models\UserScheduling as  mUserScheduling;
use App\Models\UserRole as mUserRole;
use App\Models\ActionLog as mActionLog;
use App\Models\Category as mCategory;
use App\Models\Thread as mThread;
use App\Models\ThreadCategory as mThreadCategory;

use App\Services\User as sUser,
    App\Services\Role as sRole,
    App\Services\Ask as sAsk,
    App\Services\Tag as sTag,
    App\Services\ThreadTag as sThreadTag,
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

use Form, Html, DB;

class VerifyController extends ControllerBase
{

    public function hotAction() {
        return $this->output();
    }

    public function threadAction(){
        $total_count = mAsk::count() + mReply::count();
        $yesterday = Carbon::yesterday()->timestamp;
        $today = Carbon::today()->timestamp;
        $yesterday_count = mAsk::whereBetween('create_time',[$yesterday, $today])->count() + mReply::whereBetween('create_time',[$yesterday, $today])->count();
        return $this->output([
            'pc_host' => 'http://'.env('MAIN_HOST'),
            'total_count' => $total_count,
            'yesterday_count' => $yesterday_count
        ] );
	}

    public function list_threadsAction() {
        $beg_time = $this->post('beg_time', 'string');
        $end_time = $this->post('end_time', 'string');

        $user_type    = $this->post('user_type', 'string');
        $user_role    = $this->post('user_role', 'string');
        $thread_type  = $this->post('thread_type', 'string');
        $target_type  = $this->post('target_type', 'string', 'all');
        $category_ids = $this->get('category_ids', 'int' );
        $category_type = $this->get('category_type', 'string', '');
        $nickname     = $this->post('nickname', 'string');
        $tag_name     = $this->post('tag_name', 'string');

        $uid = $this->post('uid', 'int');
        $desc = $this->post('desc', 'string');
        $thread_id = $this->post('thread_id', 'int');

        $type     = $this->get('type', 'string');
        $page     = $this->post('page', 'int', 1);
        $size     = $this->post('length', 'int', 15);

        switch( $category_type ){
            case 'channels':
                $categories = sCategory::getCategoryBypid( mCategory::CATEGORY_TYPE_CHANNEL );
                break;
            case 'activities':
                $categories = sCategory::getCategoryBypid( mCategory::CATEGORY_TYPE_ACTIVITY );
                break;
        }

        if( $category_type ){
            $category_ids = array_column( $categories->toArray(), 'id' );
        }

        $tag_ids = [];
        if( $tag_name ){
            $tags = sTag::getTagsByName( $tag_name );
            $tag_ids = array_column( $tags->toArray(), 'id' );
        }

        $cond = [
            'category_ids' => $category_ids,
            'tag_ids'      => $tag_ids,
            'target_type'  => $target_type,
            'thread_type'  => $thread_type,
            'user_type'    => $user_type,
            'user_role'    => $user_role,
            'uid'          => $uid,
            'thread_id'    => $thread_id,
            'desc'         => $desc,
            'nickname'     => $nickname,
            'type'         => $type
        ];

        $thread_ids = sThread::getThreadIds($cond, $page, $size, false);

        $data = $this->format($thread_ids['result'], null, $type );

        return $this->output_table(array(
            'data'=>$data,
            'recordsTotal'=>$thread_ids['total']
        ));
    }
    public function list_temp_threadsAction(){
        $page = $this->get('page', 'int',1 );
        $size = $this->get('size', 'int', 15);

        $asks   = DB::table('asks')->selectRaw('asks.id, 1 as type, asks.create_time, asks.update_time, asks.status')
            ->where('status', '!=', mUser::STATUS_DELETED)
            ->where('status', '!=', mUser::STATUS_BLOCKED);
        $replies= DB::table('replies')->selectRaw('replies.id, 2 as type, replies.create_time, replies.update_time, replies.status')
            ->where('status', '!=', mUser::STATUS_DELETED)
            ->where('status', '!=', mUser::STATUS_BLOCKED);
        $threads= $asks->union($replies);

        $threads1 = $threads->orderBy('create_time','DESC')
            ->forPage( $page, $size )
            ->get();

        $threads2 = $threads->orderBy('create_time','DESC')
            ->forPage( $page, $size )
            ->get();

        /*
        $tcTable = 'thread_categories';
        $threads = mThreadCategory::valid()
                    ->select(['target_id', 'target_type'])
                    ->forPage($page, $size)
                    ->orderBy('thread_categories.target_id', 'desc')
                    ->get();
        $ask_ids = array();
        $reply_ids = array();
        foreach($threads as $thread) {
            if($thread->target_type == mAsk::TYPE_ASK) {
                $ask_ids[] = $thread->target_id;
            }
            else {
                $reply_ids[] = $thread->target_id;
            }
        }
         */

        $data = array();
        foreach($threads1 as $thread) {
            if($thread->type == mAsk::TYPE_ASK) {
                $model = sAsk::getAskById($thread->id);
                $model->type = mAsk::TYPE_ASK;
            }
            else {
                $model = sReply::getReplyById($thread->id);
                $model->type = mAsk::TYPE_REPLY;
            }
            $data[$model->create_time] = $model;
        }
        foreach($threads1 as $thread) {
            if($thread->type == mAsk::TYPE_ASK) {
                $model = sAsk::getAskById($thread->id);
                $model->type = mAsk::TYPE_ASK;
            }
            else {
                $model = sReply::getReplyById($thread->id);
                $model->type = mAsk::TYPE_REPLY;
            }
            $data[$model->create_time] = $model;
        }
        $data = array_slice($data, 0, $size);

        $data = $this->format($data, null, '' );
        $total = sThread::getAllThreads(NULL,NULL);

        return $this->output_table(array(
            'data'=>$data,
            'recordsTotal'=>$total//$thread_ids['total']
        ));


    }

    public function list_category_threadsAction(){
        /*
         * replier  作品Id   描述desc  时间查询create_time
         * $this->format nickName
         * id类型   求助ask or 作品replies
         * */
        $category_id = $this->post( 'category_id', 'int' );
        $type = $this->get('category_type', 'string');
        $status = $this->get('status', 'string', 'checked');
        $page = $this->get('page', 'int',1 );
        $size = $this->get('size', 'int', 15);

        //作品ID
//        $type = $this->get('category_type', 'string');
        $category_type_id = $this->post('category_type_id','int',null);
        $id = $this->get('id','int',null);
        $uid = $this->get('uid','int',null);
        $desc = $this->get('desc','string',null);
        $start_time = $this->get('start_time','time',null);
        $end_time = $this->get('end_time','time',null);
        $nickname = $this->get('nickname','string',null);
        $arguments['category_type'] = $category_type_id;
        $arguments['id'] = $id;
        $arguments['uid'] = $uid;
        $arguments['desc'] = $desc;
        $arguments['nickname'] = $nickname;
        $arguments['start_time'] = $start_time;
        $arguments['end_time'] = $end_time;
          if(isset($arguments['start_time']) && !empty($arguments['start_time'])){
              $arguments['start_time'] = strtotime($arguments['start_time']);
          }
        if(isset($arguments['end_time']) && !empty($arguments['end_time'])){
            $arguments['end_time'] = strtotime($arguments['end_time']);
        }

        if( !$category_id ){
            if( $type == 'channels'){
                $categories = sCategory::getCategoryByPid( mCategory::CATEGORY_TYPE_CHANNEL );
                $category_id = array_column( $categories->toArray(), 'id' );
            }
            else if( $type == 'activities' ){
                $categories = sCategory::getCategoryByPid( mCategory::CATEGORY_TYPE_ACTIVITY );
                $category_id = array_column( $categories->toArray(), 'id' );
            }
            else{
                $category_id = 0;
            }
        }
        //待审self::CATEGORY_TYPE_ASKS
        if( $status == 'checked' ){
            $threads = sThreadCategory::getCheckedThreads( $category_id, $page, $size ,$arguments);
            foreach( $threads as $th ){
                $th->id = $th->target_id;
                $th->type = $th->target_type;
            }
        }
        //已审核
        else if( $status == 'valid' ){
            $threads = sThreadCategory::getValidThreadsByCategoryId( $category_id, $page, $size ,true ,$arguments);
            foreach( $threads as $th ){
                $th->id = $th->target_id;
                $th->type = $th->target_type;
            }
        }
        else{
            $threads = sThread::getAllThreads( $page, $size );
        }

        $data = $this->format($threads, null, '' );
        $total = sThread::getAllThreads(NULL,NULL);

        return $this->output_table(array(
            'data'=>$data,
            'recordsTotal'=>$total//$thread_ids['total']
        ));
    }

    private function format($data, $index = null, $type ){
        $arr = array();
        $roles = array_reverse(sRole::getRoles()->toArray());
        $categories = sCategory::getCategories()->toArray();

        foreach($data as $thread) {
            $stac = $categories;

            if($thread->type == mUser::TYPE_ASK) {
                $row = sAsk::getAskById($thread->id, false);
                $upload_ids = $row->upload_ids;

                $target_type = mAsk::TYPE_ASK;
            }
            else {
                $row = sReply::getReplyById($thread->id);
                if( $row->ask_id != 0){
                    $ask = sAsk::getAskById($row->ask_id, false);
                    $upload_ids = $ask->upload_ids;
                }

                $row->image_url = sUpload::getImageUrlById($row->upload_id);
                $target_type = mAsk::TYPE_REPLY;
            }
            $row->target_type    = $target_type;

            $tagCond = [
                'target_type' => $row->type,
                'target_id' => $row->id,
                'status' => mAsk::STATUS_NORMAL
            ];
            $thTags  = sThreadTag::getTagsByTarget( $row->target_type, $row->id );
            if( !$thTags->isEmpty() ){
                $thread_tags = [];
                foreach($thTags as $thTag) {
                    $tag = sTag::getTagById( $thTag->tag_id );
                    $thread_tags[] = '<a href="#">'.$tag->name.'</a>';
                }
                $row->thread_tags = implode('、', $thread_tags);
            }
            else{
                $row->thread_tags = '无';
            }

            $index = $row->create_time;


            //$row->is_hot = (bool)sThreadCategory::checkThreadIsPopular( $target_type, $row->id );
            $hot = sThreadCategory::admin_brief( sThreadCategory::getCategoryByTarget( $target_type, $row->id, mThreadCategory::CATEGORY_TYPE_POPULAR ) );
            $pc_hot = sThreadCategory::admin_brief( sThreadCategory::getCategoryByTarget( $target_type, $row->id, mThreadCategory::CATEGORY_TYPE_PC_POPULAR ) );
            $app_hot = sThreadCategory::admin_brief( sThreadCategory::getCategoryByTarget( $target_type, $row->id, mThreadCategory::CATEGORY_TYPE_APP_POPULAR ) );

            $row->is_hot = (bool)($hot['status']%5!=0);
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
            $row->uploads = [];
            $row->isActivity = false;
            $row->isTimeline = false;
            if( $target_type == mAsk::TYPE_ASK ){
                $row->isActivity = sThreadCategory::checkedThreadAsCategoryType(mAsk::TYPE_ASK, $row->id, mThreadCategory::CATEGORY_TYPE_ACTIVITY);
            }
            else{
                $row->isTimeline = sThreadCategory::checkedThreadAsCategoryType(mAsk::TYPE_REPLY, $row->id, mThreadCategory::CATEGORY_TYPE_TIMELINE);
            }
            if( $row->ask_id ||$target_type == mAsk::TYPE_ASK ){
                $uploads = sUpload::getUploadByIds(explode(',', $upload_ids));
                foreach($uploads as $upload) {
                    $upload->image_url = CloudCDN::file_url($upload->savename);
                }
                $row->uploads = $uploads;
            }

            $row->categories = $stac;
            $rcatids = array_flip( array_column( $stac, 'id' ) );
            $th_cats = sThreadCategory::getCategoriesByTarget( $row->target_type, $row->id );
            if( !$th_cats->isEmpty() ){
                $th_cats = $th_cats->toArray();
                $thread_categories = [];
                foreach( $th_cats as $key => $cat ){
                    $category = sCategory::detail( sCategory::getCategoryById( $cat['category_id'] ) );
                    if( $category['id'] < config( 'global.CATEGORY_BASE' ) ){
                        continue;
                    }
                    switch ( $cat['status'] ){
                        case mCategory::STATUS_CHECKED:
                            $class = 'verifing';
                            break;
                        case mCategory::STATUS_DONE:
                            $class = 'verified';
                            break;
                        case mCategory::STATUS_DELETED:
                            $class = 'deleted';
                            break;
                        case mCategory::STATUS_NORMAL:
                        default:
                            $class = 'normal';
                            break;
                    }
                    $thread_categories[] = '<span class="thread_category '.$class.'">'.$category['display_name'].'</span>';
                    if( isset( $rcatids[$cat['category_id']] )){
                        $idx = $rcatids[$cat['category_id']];
                        $stac[ $idx ]['selected'] = 'selected';
                    }
                }
                $row->categories = $stac;
                $row->thread_categories = implode(',', $thread_categories);
            }
            else{
                $row->thread_categories = '无频道';
            }


            //thread_category
            if( !isset($thread->category_id) ){
                $row->category_id = 0;
                $row->category_name = '';
                $row->category_status = 0;
            }
            else{
                $row->category_id = $thread->category_id;
                $thcat = sThreadCategory::getCategoryByTarget( $row->target_type, $thread->id, $thread->category_id);
                $cate = sCategory::detail( sCategory::getCategoryById( $thread->category_id ) );
                $row->category_name = $cate['display_name'];
                $row->category_status = $thcat->status;
            }

            $row->recRole = sRec::getRecRoleIdByUid( $row->uid );
            $roles = sUserRole::getRolesByUid( $row->uid );
            $row->user_role_ids   = array_column( $roles, 'id' );
            $row->is_star = sUserRole::checkUserIsStar( $row->uid );
            $row->is_in_blacklist = sUserRole::checkUserIsBlacklisted( $row->uid );
            $row->is_puppet= sUserRole::checkUserIsPuppet( $row->uid );


            $desc = json_decode($row->desc);
            $row->desc    = !empty($desc) && is_array($desc)? $desc[0]->content: $row->desc;
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

            $counts = cAskCounts::get( $row->id );
            $row->download_count = $counts['download_count'];

            $row->device = sDevice::getDeviceById($row->device_id);
            $row->recRoleList = sRole::getRoles( [mRole::ROLE_STAR, mRole::ROLE_BLACKLIST] );
            $row->setRoleList = sRole::getRoles( [mRole::ROLE_NEWBIE, mRole::ROLE_GENERAL, mRole::ROLE_TRUSTABLE] );

            $arr[] = $row;
        }
        return array_values($arr);
    }

    public function channelsAction(){
        $channel_id = $this->get('category_id', 'int');
        $channel = $this->get('channel','int',null);
        if($channel == 1){
            $this->action = 'channel';
        }
        $crnt_channel = [];
        if( !is_null( $channel_id ) ){
            $crnt_channel = sCategory::detail( sCategory::getCategoryById( $channel_id ) );
        }
        $channels = sCategory::getCategoryByPid( mCategory::CATEGORY_TYPE_CHANNEL, 'all' );

        return $this->output( [
                'channels'=>$channels,
                'crnt_channel' => $crnt_channel,
                'pc_host'=>'http://'.env('MAIN_HOST')
        ] );
    }

    public function activitiesAction(){
        $activity_id = $this->get('category_id', 'int');
        $crnt_channel = [];
        if( !is_null( $activity_id ) ){
            $crnt_channel = sCategory::detail( sCategory::getCategoryById( $activity_id ) );
        }
        $activities = sCategory::getCategoryByPid( mCategory::CATEGORY_TYPE_ACTIVITY, 'valid' );

        return $this->output( [
                'activities'=>$activities,
                'crnt_channel' => $crnt_channel,
                'pc_host'=>'http://'.env('MAIN_HOST')
        ] );
    }

    public function tempAction(){
        $channel_id = $this->get('channel_id', 'int');
        $activities = sCategory::getCategoryByPid( mCategory::CATEGORY_TYPE_ACTIVITY, 'valid' );
        $channels = sCategory::getCategoryByPid( mCategory::CATEGORY_TYPE_CHANNEL, 'all' );

        $categories = array_merge( $activities->toArray(), $channels->toArray() );
        return $this->output( [
            'categories'=>$categories,
            'pc_host'=>'http://'.env('MAIN_HOST')
        ] );
    }

    public function set_thread_statusAction( ){
        $target_id = $this->post( 'target_id', 'int' );
        $target_type = $this->post( 'target_type', 'int' );
        $status = $this->post( 'status', 'int' );
        $reason = $this->post( 'reason', 'int' );

        $tc = sThreadCategory::setThreadStatus( $this->_uid, $target_type, $target_id, $status, $reason );
        if( $target_type == mUser::TYPE_ASK ){
            $ask = sAsk::getAskById( $target_id );
            sAsk::updateAskStatus( $ask, $status, $this->_uid );
        }
        else{
            $reply = sReply::getReplyById( $target_id );
            sReply::updateReplyStatus( $reply, $status, $this->_uid );
        }

        return $this->output( ['result'=>'ok'] );
    }

    public function set_thread_categoryAction(){
        $status = $this->post( 'status', 'string' );
        $target_id = $this->post( 'target_id', 'int' );
        $target_type = $this->post( 'target_type', 'int' );
        $category_from = $this->post( 'category_from', 'string', 0 );
        $category_id = $this->post( 'category', 'string', mThreadCategory::CATEGORY_TYPE_POPULAR );//热门的
        if( !is_array( $category_id ) ){
            $cats = [ $category_id ];
        }
        else{
            $cats = $category_id;
        }
        foreach( $cats as $cat ){
            $tc = sThreadCategory::setCategory( $this->_uid, $target_type, $target_id, $cat, $status );
        }

        return $this->output( ['result'=>'ok'] );
    }

    public function set_categoryAction(){
        $status = $this->post( 'status', 'string' );
        $target_ids = $this->post( 'target_id', 'int' );
        $target_types = $this->post( 'target_type', 'int' );
        $category_id = $this->post( 'category_id', 'string', mThreadCategory::CATEGORY_TYPE_POPULAR );//热门的
        foreach( $target_ids as $key => $target_id ){
            $tc = sThreadCategory::setCategory( $this->_uid, $target_types[$key], $target_id, $category_id, $status );
        }

        return $this->output( ['result'=>'ok'] );
    }

    public function set_thread_category_statusAction(){
        $target_ids = $this->post( 'target_id', 'string' );
        $target_types = $this->post( 'target_type', 'string' );
        $category_ids = $this->post( 'category_id', 'string' );
        $status = $this->post( 'status', 'string' );

        $uid = $this->_uid;
        foreach ($target_ids as $key => $target_id) {
            $target_type = $target_types[$key];
            $category_id = $category_ids[$key];
            sThreadCategory::setCategory( $uid, $target_type, $target_id, $category_id, $status );
        }
        return $this->output( ['result'=>'ok'] );
    }

    public function set_thread_as_pouplarAction(){
        $type   = $this->post( 'type', 'string' );
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
            $status = 0;
            switch( $statuses[$key]){
                case 'online':
                    $status = mThreadCategory::STATUS_NORMAL;
                    break;
                case 'delete':
                    $status = mThreadCategory::STATUS_DELETED;
                    break;
                case 'invalid':
                    $status = mThreadCategory::STATUS_REJECT;
                    break;
                case 'hidden':
                    $status =mThreadCategory::STATUS_HIDDEN;
                    break;
                case 'ready':
                    $status = mThreadCategory::STATUS_READY;
                    break;
                case 'checked':
                    $status = mThreadCategory::STATUS_CHECKED;
                    break;
                default:
                    break;
            }
            $tc = sThreadCategory::setCategory( $this->_uid, $target_type, $target_id, $category_id, $status );
        }
        return $this->output( ['result'=>'ok'] );
    }

    public function delete_popularAction(){
        $target_ids   = $this->post( 'target_ids', 'int' );
        $target_types = $this->post( 'target_types', 'int' );
        $category_type = $this->post( 'category', 'string');
        $status_name = $this->post( 'status', 'string');

        $status = 0;
        switch( $status_name ){
            case 'online':
                $status = mThreadCategory::STATUS_NORMAL;
                break;
            case 'delete':
                $status = mThreadCategory::STATUS_DELETED;
                break;
            case 'invalid':
                $status = mThreadCategory::STATUS_REJECT;
                break;
            case 'hidden':
                $status =mThreadCategory::STATUS_HIDDEN;
                break;
            case 'ready':
                $status = mThreadCategory::STATUS_READY;
                break;
            default:
                break;
        }

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

        $invalid_status = [
            mThreadCategory::STATUS_READY,
            mThreadCategory::STATUS_REJECT,
            mThreadCategory::STATUS_HIDDEN
        ];
        foreach( $target_ids as $key => $target_id ){
            if( $category_type == 'unreviewed' ){
                //判断是否有正在生效中的
                $app_cat = sThreadCategory::getCategoryByTarget( $target_types[$key], $target_id, mThreadCategory::CATEGORY_TYPE_APP_POPULAR );
                if( $app_cat && in_array( $app_cat->status, $invalid_status ) ){
                    sThreadCategory::deleteThread( $this->_uid, $target_types[$key], $target_id, $status, '', mThreadCategory::CATEGORY_TYPE_APP_POPULAR );
                }
                else if( $app_cat && $app_cat->status != mThreadCategory::STATUS_DELETED ){
                    break;
                }

                $pc_cat = sThreadCategory::getCategoryByTarget( $target_types[$key], $target_id, mThreadCategory::CATEGORY_TYPE_PC_POPULAR );
                if( $pc_cat && in_array( $pc_cat->status, $invalid_status ) ){
                    sThreadCategory::deleteThread( $this->_uid, $target_types[$key], $target_id, $status, '', mThreadCategory::CATEGORY_TYPE_PC_POPULAR );
                }
                else if( $pc_cat && $pc_cat->status != mThreadCategory::STATUS_DELETED ){
                    break;
                }
            }
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

    /**
     * 设置标签
     */
    public function set_thread_tagAction(){

        $target_id  = $this->post( 'target_id', 'int' );
        $target_type= $this->post( 'target_type', 'int' );
        $tag_id     = $this->post( 'tag_id', 'int');
        $status     = mTag::STATUS_NORMAL;

        $tag = mThreadTag::where('target_type', $target_type)
            ->where('target_id', $target_id)
            ->where('tag_id', $tag_id)
            ->first();

        if($tag && $tag->status == mTag::STATUS_NORMAL) {
            $status = mTag::STATUS_DELETED;
        }

        $tc = sThreadTag::setTag( $this->_uid, $target_type, $target_id, $tag_id, $status );
        return $this->output( ['result'=>'ok'] );
    }
}

<?php namespace App\Http\Controllers\Admin;

use App\Models\User as mUser;
use App\Models\UserRole as mUserRole;
use App\Models\ThreadCategory as mThreadCategory;
use App\Models\Ask as mAsk;
use App\Models\Reply as mReply;
use App\Models\Role as mRole;
use App\Models\Category as mCategory;


use App\Services\Thread as sThread;
use App\Services\Tag as sTag;
use App\Services\User as sUser;
use App\Services\Ask as sAsk;
use App\Services\Reply as sReply;
use App\Services\Role as sRole;
use App\Services\ThreadTag as sThreadTag;
use App\Services\Upload as sUpload;
use App\Services\ThreadCategory as sThreadCategory;
use App\Services\Category as sCategory;
use App\Services\Download as sDownload;
use App\Services\UserRole as sUserRole;
use App\Services\Device as sDevice;
use App\Services\Recommendation as sRec;

use App\Counters\AskDownloads as cAskDownloads;

use App\Facades\CloudCDN;
use Form, Html;

class ActivityController extends ControllerBase{

    public function indexAction(){
        return $this->output();
    }

    public function worksAction(){
        return $this->output();
    }

    public function list_activitiesAction(){
        $category = new mCategory;
        // 检索条件
        $cond = array();
        $cond['id']             = $this->post("category_id", "int");
        $cond['categoryName']           = array(
            $this->post("categoryName", "string"),
            'LIKE'
        );
        $cond['display_name']   = array(
            $this->post("category_display_name", "string"),
            'LIKE'
        );
        $cond['pid']   = mCategory::CATEGORY_TYPE_ACTIVITY;

        // 用于遍历修改数据
        $data  = $this->page($category, $cond);

        foreach($data['data'] as $row){
            $activity_id = $row->id;

            if( $row->url ){
                $row->id = '<a href="'.$row->url.'" target="_blank">'.$activity_id.'</a>';
            }
            $row->create_time = date('Y-m-d H:i:s', $row->create_time);
            $row->update_time = date('Y-m-d H:i:s', $row->update_time);
            $row->end_time = date('Y-m-d', $row->end_time);
            $row->display_name = '<a href="/verify/activities?status=valid&category_id='.$activity_id.'">'.$row->display_name.'</a>';

            $row->pc_pic    = $row->pc_pic?Html::image( $row->pc_pic, 'pc_pic', array(
                'width'=>100,
            )): '无';
            $row->app_pic   = $row->app_pic?Html::image( $row->app_pic, 'pc_pic', array(
                'width'=>100,
            )): '无';
            $row->post_btn  = $row->post_btn?Html::image( $row->post_btn, 'post_btn', array(
                'width'=>100,
            )): '无';
            $row->icon      = $row->icon?Html::image( $row->icon, 'icon', array(
                'width'=>100,
            )): '无';
            $row->banner_pic= $row->banner_pic?Html::image( $row->banner_pic, 'banner_pic', array(
                'width'=>100,
            )): '无';
            $row->pc_banner_pic= $row->pc_banner_pic?Html::image( $row->pc_banner_pic, 'pc_banner_pic', array(
                'width'=>100,
            )): '无';

            $oper = [];
            if(    $row->status != mCategory::STATUS_DONE
                && $row->status != mCategory::STATUS_DELETED
            ){
                $oper[] = "<a href='#edit_category' data-toggle='modal' data-id='$activity_id' class='edit'>编辑</a>";
            }

            if( $row->status == mCategory::STATUS_DELETED || $row->status == mCategory::STATUS_DONE){
                $oper[] = "<a href='#' data-status='restore' data-id='".$activity_id."' class='restore'>恢复</a>";
            }
            else{
                $oper[] = "<a href='#delete_category' data-id='$activity_id' data-toggle='modal' data-status='delete' class='delete'>删除</a>";
            }

            if( $row->status == mCategory::STATUS_NORMAL ){
                $oper[] = "<a href='#' data-id='".$activity_id."' data-status='offline' class='offline'>下架</a>";
            }
            if( $row->status == mCategory::STATUS_READY
                || $row->status == mCategory::STATUS_HIDDEN ){
                $oper[] = "<a href='#' data-id='".$activity_id."' data-status='online' class='online'>上架</a>";
            }
            $row->oper  = implode( ' / ', $oper );

            $row->ask_view = '';

            $ask = sThreadCategory::getHiddenAskByCategoryId($activity_id);
            if($ask) {
                $status = $ask->status;
                $model  = sAsk::brief($ask);

                $row->ask_view = Html::image( $model['image_url'], 'ask_view', array(
                    'width'=>100,
                    'id'=>$model['id'],
                    'uid'=>$model['uid'],
                    'upload_id'=>$model['upload_id'],
                    'desc'=>$model['desc'],
                    'status'=>$status
                ));
            }
        }
        // 输出json
        return $this->output_table($data);
    }

    public function list_worksAction(){
        $category_id = $this->get( 'activity_id', 'int' );
        $page     = $this->post('page', 'int', 1);
        $size     = $this->post('length', 'int', 15);
        $type = ['ask', 'reply'];

        $cond = [];
        $cond['category_ids'] = $category_id;
        $cond['target_type'] = $type;
        $thread_ids = sThread::getThreadIds($cond, $page, $size);

        $data = $this->format($thread_ids['result'], $category_id, $type );

        return $this->output_table(array(
            'data'=>$data,
            'recordsTotal'=>$thread_ids['total']
        ));
    }

    public function set_activityAction(){
        $activity_id  = $this->post("activity_id", "int", NULL );
        $activity_display_name  = $this->post("activity_display_name", "string");
        $end_time  = $this->post("end_time", "string");
        $activityName = md5( $activity_display_name );
        //$activity_display_name  = $this->post("activity_display_name", "string");
        $parent_activity_id     = mCategory::CATEGORY_TYPE_ACTIVITY;
        $pc_pic     = $this->post( 'pc_pic', 'string', '' );
        $app_pic    = $this->post( 'app_pic', 'string', '' );
        $banner_pic = $this->post( 'banner_pic', 'string', '' );
        $pc_banner_pic = $this->post( 'pc_banner_pic', 'string', '' );
        $url        = $this->post( 'url', 'string', '' );
        //活动按钮
        $icon = $this->post( 'category_icon', 'string','' );
        $description = $this->post( 'description', 'string','' );
        $post_btn = $this->post( 'post_btn', 'string','' );
        //新建求助
        $ask_id = $this->post('ask_id', 'int');
        $uid = $this->post('uid', 'int', $this->_uid);
        $desc   = $this->post('desc', 'string');
        $upload_id = $this->post('upload_id', 'string');
        $status = $this->post('status', 'int');

        if(is_null($activityName) || is_null($activity_display_name)){
            return error('EMPTY_ACTIVITY_NAME');
        }

        $activity = sCategory::updateCategory(
            $this->_uid,
            $activity_id,
            $activityName,
            $activity_display_name,
            $parent_activity_id,
            $pc_pic,
            $app_pic,
            $banner_pic,
            $pc_banner_pic,
            $url,
            $icon,
            $post_btn,
            $description,
            $end_time
        );

        if(isset($desc) && isset($upload_id)) {
            if($ask_id) {
                $ask = sAsk::getAskById($ask_id);
            }
            else {
                $ask = new mAsk;
            }
            $ask->uid = $uid;
            $ask->upload_ids= $upload_id;
            $ask->desc      = $desc;
            $ask->status    = $status;
            $ask->save();

            $category = sThreadCategory::getCategoryByTarget( mAsk::TYPE_ASK, $ask->id, $activity->id);
            if($category && $status == mAsk::STATUS_HIDDEN) {
                $category->status = mAsk::STATUS_NORMAL;
                $category->save();
            }
            else if($category && $status == mAsk::STATUS_DELETED) {
                $category->status = mAsk::STATUS_DELETED;
                $category->save();
            }
            else {
                sThreadCategory::addCategoryToThread( $this->_uid, mAsk::TYPE_ASK, $ask->id, $activity->id, mAsk::STATUS_NORMAL);
            }
        }

        return $this->output( ['id'=>$activity->id] );
    }

    private function format($data, $category_id, $type ){
        $arr = array();
        $roles = array_reverse(sRole::getRoles()->toArray());
        $tags  = sTag::getTags(0, 9999);

        foreach($data as $row) {

            if($row->type == mUser::TYPE_ASK) {
                $row = sAsk::getAskById($row->id, false);
                $upload_ids = $row->upload_ids;

                $target_type = mAsk::TYPE_ASK;
            }
            else {
                $row = sReply::getReplyById($row->id);
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

            $row->category_id = $category_id;

            //$row->is_hot = (bool)sThreadCategory::checkThreadIsPopular( $target_type, $row->id );
            $row->uploads =[];
            $row->isActivity = sThreadCategory::checkThreadIsActivity( $target_type, $row->id);
            $row->isChannel = sThreadCategory::checkThreadIsChannel( $target_type, $row->id);
            if( $target_type == mAsk::TYPE_ASK ){
                $uploads = sUpload::getUploadByIds(explode(',', $upload_ids));
                foreach($uploads as $upload) {
                    $upload->image_url = CloudCDN::file_url($upload->savename);
                }
                $row->uploads = $uploads;
            }
            $row->thread_status = $row->status;
            $row->thread_categories = sThreadCategory::getCategoriesByTarget( $row->target_type, $row->id );
            $row->recRole = sRec::getRecRoleIdByUid( $row->uid );
            $roles = sUserRole::getRoleStrByUid( $row->uid );
            $row->user_roles   = $roles;
            $row->is_star = in_array(mRole::ROLE_STAR, $roles);
            $row->is_in_blacklist = in_array(mRole::ROLE_BLACKLIST, $roles);
            $row->is_puppet= false;
            $puppet_roles = [ //PuppetController::get_puppets && RoleController::get_roles 处定义
                mRole::TYPE_HELP,
                mRole::TYPE_WORK,
                mRole::TYPE_CRITIC
            ];
            foreach( $puppet_roles as $puppet_role ){
                if( in_array($puppet_role, $roles) ){
                    $row->is_puppet=true;
                    break;
                }
            }

            $desc = json_decode($row->desc);
            $row->desc    = !empty($desc) && is_array($desc)? $desc[0]->content: $row->desc;
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


            $row->download_count = cAskDownloads::get($row->id);

            $row->device = sDevice::getDeviceById($row->device_id);
            $row->recRoleList = sRole::getRoles( [mRole::ROLE_STAR, mRole::ROLE_BLACKLIST] );
            $row->pc_host = env('MAIN_HOST');
            $arr[] = $row;
        }
        return array_values($arr);
    }
}

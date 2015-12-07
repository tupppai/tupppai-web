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
                $row->display_name = '<a href="/activity/works?category_id='.$activity_id.'">'.$row->display_name.'</a>';
            $row->pc_pic  = $row->pc_pic  ? '<img src="'.CloudCDN::file_url( $row->pc_pic  ).'" />' : '无';
            $row->app_pic = $row->app_pic ? '<img src="'.CloudCDN::file_url( $row->app_pic ).'" />' : '无';
            $oper = [];
            if(    $row->status != mCategory::STATUS_DONE
                && $row->status != mCategory::STATUS_DELETED
            ){
                $oper[] = "<a href='#edit_category' data-toggle='modal' data-id='$activity_id' class='edit'>编辑</a>";
            }

            if( $row->status == mCategory::STATUS_DELETED ){
                $oper[] = "<a href='#' data-status='restore' data-id='".$activity_id."' class='restore'>恢复</a>";
            }
            else{
                $oper[] = "<a href='#delete_category' data-id='$activity_id' data-toggle='modal' data-status='delete' class='delete'>删除</a>";
            }

            if( $row->status == mCategory::STATUS_NORMAL ){
                $oper[] = "<a href='#' data-id='".$activity_id."' data-status='offline' class='offline'>下架</a>";
            }
            if( $row->status == mCategory::STATUS_HIDDEN ){
                $oper[] = "<a href='#' data-id='".$activity_id."' data-status='online' class='online'>上架</a>";
            }
            $row->oper = implode( ' / ', $oper );

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

        $data = $this->format($thread_ids['result'], null, $type );

        return $this->output_table(array(
            'data'=>$data,
            'recordsTotal'=>$thread_ids['total']
        ));
    }

    public function set_activityAction(){
        $activity_id  = $this->post("activity_id", "int", NULL );
        $activityName = md5( $activity_display_name );
        $activity_display_name  = $this->post("activity_display_name", "string");
        $parent_activity_id     = mCategory::CATEGORY_TYPE_ACTIVITY;
        $pc_pic     = $this->post( 'pc_pic', 'string', '' );
        $app_pic    = $this->post( 'app_pic', 'string', '' );
        $url        = $this->post( 'url', 'string', '' );

        //新建求助
        $ask_id = $this->post('ask_id', 'int');
        $desc   = $this->post('desc', 'string');
        $upload_id = $this->post('upload_id', 'string');

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
            $url
        );

        if(isset($desc) && isset($upload_id)) {
            if($ask_id) {
                $ask = sAsk::getAskById($ask_id);
                $ask->upload_ids = $upload_id;
                $ask->desc = $desc;
                $ask->save();
            }
            else {
                $ask = sAsk::addNewAsk($this->uid, array($upload_id), $desc);
            }
        }

        return $this->output( ['id'=>$activity->id] );
    }

    private function format($data, $index = null, $type ){
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

            $row->tags = '';
            foreach($tags as $tag) {
                $selected = '';
                if(sThreadTag::checkThreadHasTag($target_type, $row->id, $tag->id)) {
                    $selected = ' btn-primary';
                }

                $row->tags .= Form::button($tag->name, array(
                    'class'=>'tags btn-xs'.$selected,
                    'type'=>'button',
                    'data-id'=>$tag->id,
                    'data-target-id'=>$row->id,
                    'data-target-type'=>$row->target_type
                ));
            }

            $index = $row->create_time;

            //$row->is_hot = (bool)sThreadCategory::checkThreadIsPopular( $target_type, $row->id );
            $row->uploads =[];
            if( $target_type == mAsk::TYPE_ASK ){
                $is_activity = sThreadCategory::checkedThreadAsCategoryType(mAsk::TYPE_ASK, $row->id, 4);
                $row->isActivity = $is_activity;
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

            $row->download_count = sDownload::countDownload($target_type, $row->id);

            $row->device = sDevice::getDeviceById($row->device_id);
            $row->recRoleList = sRole::getRoles( [mRole::ROLE_STAR, mRole::ROLE_BLACKLIST] );

            $arr[] = $row;
        }
        return array_values($arr);
    }
}

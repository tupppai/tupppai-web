<?php
namespace App\Services;
use App\Models\Category as mCategory;
use App\Models\User as mUser;
use App\Models\ThreadCategory as mThreadCategory;

use App\Services\ActionLog as sActionLog;
use App\Services\ThreadCategory as sThreadCategory;

class Category extends ServiceBase{

    public static function updateCategory(
            $uid,
            $id,
            $name,
            $display_name,
            $pid,
            $pc_pic,
            $app_pic,
            $banner_pic,
            $url,
            $icon,
            $post_btn,
            $desc
        ){
        $mCategory = new mCategory;

        $category  = $mCategory->get_category_by_id($id);
        sActionLog::init( 'UPDATE_CATEGORY', $category );
        if ($category) {
            sActionLog::init( 'ADD_NEW_CATEGORY' );
            $status = $category->status;
        }
        else {
            $category = $mCategory;
            /*
            $channel_id = mCategory::where('id', '<', 1000)
                ->orderBy('id', 'desc')
                ->pluck('id');
            $category->id = $channel_id + 1;

            if($channel_id > 999) {
                return error('SYSTEM_ERROR');
            }
             */

            $status = mCategory::STATUS_READY;
        }
        $category->assign(array(
            'create_by' => $uid,
            'update_by' => $uid,
            'status'    => $status,
            'pid'   => $pid,
            'name'  => $name,
            'pc_pic'  => $pc_pic,
            'app_pic'  => $app_pic,
            'banner_pic'  => $banner_pic,
            'url'  => $url,
            'display_name' => $display_name,
            'icon' => $icon,
            'post_btn' => $post_btn,
            'description' => $desc
        ));

        $category->save();
        sActionLog::save( $category );
        return $category;
    }

    public static function deleteCategory( $uid, $category_id ){
        return self::updateStatus( $category_id, 'delete' );
    }

    public static function updateStatus( $id, $status_name ){
        $status = '';
        switch( $status_name ){
            case 'offline':
                $status = mCategory::STATUS_DONE;
                break;
            case 'online':
                $status = mCategory::STATUS_NORMAL;
                break;
            case 'delete':
                $status = mCategory::STATUS_DELETED;
                break;
            case 'restore':  //回复
                $status = mCategory::STATUS_HIDDEN;
                break;
            case 'undelete':
                $status = mCategory::STATUS_READY;
                break;
            default:
                return false;
        }

        $mCategory = new mCategory();
        $category = $mCategory->get_category_by_id( $id );
        if( !$category ){
            return error('CATEGORY_NOT_EXIST');
        }

        $category->assign([
            'status' => $status
        ])->save();

        return $category;
    }

    public static function getCategoryById ($id) {
        $category = (new mCategory)->get_category_by_id($id);

        return $category;
    }

    public static function getCategoryByPid ($pid, $type = 'all', $page = 0, $size = 0 ) {
        switch( $type ){
            case 'valid':
                $status = mThreadCategory::STATUS_NORMAL;
                break;
            case 'done':
                $status = mThreadCategory::STATUS_DONE;
                break;
            case 'next':  //即将开始的活动（公开的）
                $status = mThreadCategory::STATUS_READY;
                break;
            case 'hidden':
            case 'ready': //后台储备的
                $status = mThreadCategory::STATUS_HIDDEN;
                break;
            case 'all':
            default:
                $status = [
                    mThreadCategory::STATUS_NORMAL,
                    mThreadCategory::STATUS_READY,
                    mThreadCategory::STATUS_DONE
                ];
                break;
        }

        $category = (new mCategory)->get_category_by_pid( $pid, $status, $page, $size );

        return $category;
    }

    public static function getCategories( $type = 'all', $status = 'valid', $page = 0, $size = 0 ) {
        switch( $status ){
            case 'valid':
                $status = [ mThreadCategory::STATUS_NORMAL ];
                break;
            case 'done':
                $status = [ mThreadCategory::STATUS_DONE ];
                break;
            case 'next':  //即将开始的活动（公开的）
                $status = [ mThreadCategory::STATUS_READY ];
                break;
            case 'hidden':
            case 'ready': //后台储备的
                $status = mThreadCategory::STATUS_HIDDEN;
                break;
            case 'all':
            default:
                $status = [
                    mThreadCategory::STATUS_NORMAL,
                    mThreadCategory::STATUS_READY,
                    mThreadCategory::STATUS_DONE
                ];
                break;
        }
        return (new mCategory)->get_categories( $type, $status, $page, $size );
    }

    public static function searchCategory( $keyword ){
        $mCategory = new mCategory();
        $cond = [];
        $cond['display_name'] = $keyword;
        $cond['status'] = mCategory::STATUS_NORMAL;
        $cond['pid'] = [mCategory::CATEGORY_TYPE_CHANNEL, mCategory::CATEGORY_TYPE_ACTIVITY ];
        $categories = $mCategory->find_category_by_cond( $cond );

        return $categories;
    }

    public static function detail( $cat ){
        $data = [];
        $data['id'] = $cat['id'];
        $data['display_name'] = $cat['display_name'];
        $data['pc_pic'] = $cat['pc_pic'];
        $data['app_pic'] = $cat['app_pic'];
        $data['banner_pic'] = $cat['banner_pic'];
        $data['url'] = $cat['url'];
        $data['pid'] = $cat['pid'];
        $data['icon'] = $cat['icon'];
        $data['post_btn'] = $cat['post_btn'];

        $data['description'] = $cat['description'];

        //todo: jq
        $data['uped_count']     = 0;
        $data['download_count'] = 0;
        $data['click_count']    = 0;
        $data['replies_count']  = 0;

        $ask = sThreadCategory::getHiddenAskByCategoryId($cat['id']);

        if($ask) {
            $data['ask_id'] = $ask->id;
            $data['users']  = (new mUser)->get_users_by_downloads($ask->id);
        }
        else {
            $data['ask_id'] = 0;
            $data['users']  = array();
        }

        //获取频道类型
        if( $cat->pid == mThreadCategory::CATEGORY_TYPE_ACTIVITY ) {
            $data['category_type'] = 'activity';
        }
        else if( $cat->pid == mThreadCategory::CATEGORY_TYPE_CHANNEL ) {
            $data['category_type'] = 'channel';
        }
        else {
            $data['category_type'] = 'nothing';
        }

        return $data;
    }

    public static function brief($category) {
        $data = array();

        $data['id'] = $category->id;
        $data['display_name'] = $category->display_name;
        $data['app_pic']    = $category->app_pic;
        $data['pc_pic']     = $category->pc_pic;
        $data['banner_pic'] = $category->banner_pic;
        $data['url']        = $category->url;
        $data['icon']       = $category->icon;
        $data['post_btn']   = $category->post_btn;

        //获取频道类型
        if( $category->pid == mThreadCategory::CATEGORY_TYPE_ACTIVITY ) {
            $data['category_type'] = 'activity';
        }
        else if( $category->pid == mThreadCategory::CATEGORY_TYPE_CHANNEL ) {
            $data['category_type'] = 'channel';
        }
        else {
            $data['category_type'] = 'nothing';
        }

        return $data;
    }
}

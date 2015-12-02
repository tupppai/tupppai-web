<?php
namespace App\Services;
use \App\Models\Category as mCategory;
use \App\Models\ThreadCategory as mThreadCategory;
use App\Services\ActionLog as sActionLog;

class Category extends ServiceBase{

    public static function addNewCategory ( $uid, $name, $display_name, $pid, $pc_pic, $app_pic ) {
        sActionLog::init( 'ADD_NEW_CATEGORY' );

        $category = new mCategory;
        $category->assign(array(
            'name'=>$name,
            'display_name'=>$display_name,
            'pid' => $pid,
            'pc_pic' => $pc_pic,
            'app_pic' => $app_pic,
            'create_by' => $uid
        ));

        $category->save();
        sActionLog::save( $category );
        return $ret;
    }

    public static function updateCategory( $uid, $id, $name, $display_name, $pid, $pc_pic, $app_pic ){
        $mCategory = new mCategory;

        $category  = $mCategory->get_category_by_id($id);
        sActionLog::init( 'UPDATE_CATEGORY', $category );
        if ($category) {
            sActionLog::init( 'ADD_NEW_CATEGORY' );
        }
        else {
            $category = $mCategory;
        }

        $category->assign(array(
            'create_by' => $uid,
            'update_by' => $uid,
            'status'    => mCategory::STATUS_NORMAL,
            'pid'   => $pid,
            'name'  => $name,
            'pc_pic'  => $pc_pic,
            'app_pic'  => $app_pic,
            'display_name' => $display_name
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
            case 'restore':
                $status = mCategory::STATUS_HIDDEN;
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

    public static function getCategoryByPid ($pid, $type ) {
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

        $category = (new mCategory)->get_category_by_pid($pid, $status );

        return $category;
    }

    public static function getCategories() {
        return (new mCategory)->get_categories();
    }

    public static function detail( $cat ){
        $data = [];
        $data['id'] = $cat['id'];
        $data['display_name'] = $cat['display_name'];
        $data['pc_pic'] = $cat['pc_pic'];
        $data['app_pic'] = $cat['app_pic'];

        return $data;
    }
}

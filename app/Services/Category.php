<?php
namespace App\Services;
use \App\Models\Category as mCategory;
use App\Services\ActionLog as sActionLog;

class Category extends ServiceBase{

    public static function addNewCategory ( $uid, $name, $display_name ) {
        sActionLog::init( 'ADD_NEW_CATEGORY' );

        $category = new mCategory;
        $category->assign(array(
            'name'=>$name,
            'display_name'=>$display_name,
            'create_by' => $uid
        ));

        $category->save();
        sActionLog::save( $category );
        return $ret;
    }

    public static function updateCategory( $uid, $id, $name, $display_name, $pid ){
        $mCategory = new mCategory;

        $category  = $mCategory->get_category_byid($id);
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
            'display_name' => $display_name
        ));

        $category->save();
        sActionLog::save( $category );
        return $category;
    }

    public static function deleteCategory( $uid, $category_id ){
        $mCategory = new mCategory();
        $category = $mCategory->where(['id' => $category_id])->first();
        if( !$category ){
            return error('CATEGORY_NOT_EXIST');
        }

        $category->assign([
            'status' => $mCategory::STATUS_DELETED,
            'delete_by' => $uid,
            'delete_time' => time()
        ])->save();

        return $category;
    }

    public static function getCategoryById ($id) {
        $category = (new mCategory)->get_category_by_id($id);

        return $category;
    }

    public static function getCategoryByPid ($pid) {
        $category = (new mCategory)->get_category_by_pid($pid);

        return $category;
    }

    public static function getCategories() {
        return (new mCategory)->get_categories();
    }
}

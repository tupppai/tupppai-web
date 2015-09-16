<?php
namespace App\Services;
use \App\Models\Category as mCategory;
use App\Services\ActionLog as sActionLog;

class Category extends ServiceBase{

    public static function addNewCategory ( $uid, $name, $display_name ) {
        $category = new mCategory;
        $category->assign(array(
            'name'=>$name,
            'display_name'=>$display_name,
            'create_by' => $uid
        ));

        sActionLog::init( 'ADD_NEW_CATEGORY' );
        #todo: ActionLog
        $ret = $category->save();
        sActionLog::save( $ret );
        return $ret;
    }

    public static function updateCategory( $uid, $id, $name, $display_name, $pid ){
        $mCategory = new mCategory();
        $cond = [ 'id' => $id ];
        $category = $mCategory->firstOrNew( $cond );
        sActionLog::init( 'UPDATE_CATEGORY', $category );

        $data = [];
        if( !$category->id ){
            $data['status'] = mCategory::STATUS_NORMAL;
            $data['create_by'] = $uid;
            $data['update_by'] = $uid;
            sActionLog::init( 'ADD_NEW_CATEGORY' );
        }

        $data['pid'] = $pid;
        $data['name'] = $name;
        $data['display_name'] = $display_name;

        #todo: ActionLog
        $c = $category->fill( $data )->save();

        sActionLog::save( $c );
        return $c;
    }

    public static function deleteCategory( $uid, $category_id ){
        $mCategory = new mCategory();
        $category = $mCategory->where(['id' => $category_id])->first();
        if( !$category ){
            return error('CATEGORY_NOT_EXIST');
        }

        $category->fill([
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

    public static function getCategoryByPid ($uid) {
        $category = (new mCategory)->get_category_by_uid($uid);

        return $category;
    }

    public static function getCategories() {
        return (new mCategory)->get_categories();
    }
}

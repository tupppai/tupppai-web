<?php
namespace App\Http\Controllers\Admin;

use App\Models\User,
    App\Models\Usermeta,
    App\Models\Category as mCategory,
    App\Models\ActionLog;

use App\Services\Category as sCategory;

class CategoryController extends ControllerBase{
    public function indexAction(){
        return $this->output();
    }

    public function list_categoriesAction(){
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
        $cond['status'] = $this->post( 'status', 'int', mCategory::STATUS_NORMAL );

        // 用于遍历修改数据
        $data  = $this->page($category, $cond);

        foreach($data['data'] as $row){
            $category_id = $row->id;
            $row->create_time = date('Y-m-d H:i:s', $row->create_time);
            $row->update_time = date('Y-m-d H:i:s', $row->update_time);
            $row->oper = "<a href='#edit_category' data-toggle='modal' data-id='$category_id' class='edit'>编辑</a>".
                      " / <a href='#delete_category' data-toggle='modal' class='delete'>删除</a>";
        }
        // 输出json
        return $this->output_table($data);
	}

    public function set_categoryAction(){
        $category_id  = $this->post("category_id", "int", 0 );
        $categoryName = $this->post("category_name", "string");
        $category_display_name = $this->post("category_display_name", "string");
        $parent_category_id = $this->post( 'pid', 'int', 0 );

        if(is_null($categoryName) || is_null($categoryName)){
            return error('EMPTY_CATEGORY_NAME');
        }

        $category = sCategory::updateCategory(
            $this->_uid,
            $category_id,
            $categoryName,
            $category_display_name,
            $parent_category_id
        );

        return $this->output( ['id'=>$category->id] );
    }

    public function delete_categoryAction(){
        $category_id  = $this->post("category_id", "int", 0 );
        $uid  = $this->_uid;
        if( !$category_id ){
            return error('EMPTY_CATEGORY_ID');
        }

        $category = sCategory::deleteCategory( $uid, $category_id );
        return $this->output( ['id'=>$category->id],'删除成功' );
    }
}

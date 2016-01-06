<?php namespace App\Http\Controllers\Admin;

use App\Models\User,
    App\Models\Usermeta,
    App\Models\Category as mCategory,
    App\Models\ActionLog;

use App\Services\Category as sCategory;
use App\Services\ThreadCategory as sThreadCategory;
use App\Facades\CloudCDN;

class CategoryController extends ControllerBase{
    public function indexAction(){
        return $this->output();
    }

    public function list_categoriesAction(){
        $category = new mCategory;
        // 检索条件
        $cond = array();
        $cond['id']             = $this->post("category_id", "int");
        //todo: remove
        $cond['pid'] = mCategory::CATEGORY_TYPE_CHANNEL;
        $cond['categoryName']           = array(
            $this->post("categoryName", "string"),
            'LIKE'
        );
        $cond['display_name']   = array(
            $this->post("category_display_name", "string"),
            'LIKE'
        );

        // 用于遍历修改数据
        $data  = $this->page($category, $cond);

        foreach($data['data'] as $row){
            $category_id = $row->id;
            $row->display_name = '<a href="/verify/channels?status=valid&category_id='.$category_id.'">'.$row->display_name.'</a>';
            $row->create_time = date('Y-m-d H:i:s', $row->create_time);
            $row->update_time = date('Y-m-d H:i:s', $row->update_time);
            $par = sCategory::getCategoryById( $row->pid );
            $row->parent_name = $par?$par->display_name: '无';
            $row->pc_pic  = $row->pc_pic  ? '<img src="'.CloudCDN::file_url( $row->pc_pic  ).'" />' : '无';
            $row->app_pic = $row->app_pic ? '<img src="'.CloudCDN::file_url( $row->app_pic ).'" />' : '无';
            $row->icon = $row->icon ? '<img src="'.CloudCDN::file_url( $row->icon ).'" />' : '无';
            $row->post_btn = $row->post_btn ? '<img src="'.CloudCDN::file_url( $row->post_btn ).'" />' : '无';
            $oper = [];
            if( $row->id != 0){
                if(    $row->status != mCategory::STATUS_DONE
                    && $row->status != mCategory::STATUS_DELETED
                ){
                    $oper[] = "<a href='#edit_category' data-toggle='modal' data-id='$category_id' class='edit'>编辑</a>";
                }

                if( $row->status == mCategory::STATUS_DELETED ){
                    $oper[] = "<a href='#' data-status='restore' data-id='".$category_id."' class='restore'>恢复</a>";
                }
                else if( $row->status != mCategory::STATUS_NORMAL) {
                    $oper[] = "<a href='#delete_category' data-id='$category_id' data-toggle='modal' data-status='delete' class='delete'>删除</a>";
                }

                if( $row->status == mCategory::STATUS_NORMAL ){
                    $oper[] = "<a href='#' data-id='".$category_id."' data-status='undelete' class='offline'>失效</a>";
                }
                if( $row->status == mCategory::STATUS_READY
                    || $row->status == mCategory::STATUS_HIDDEN ){
                    $oper[] = "<a href='#' data-id='".$category_id."' data-status='online' class='online'>生效</a>";
                }
            }
            else{
                $oper[] = '-----';
            }
            $row->oper = implode( ' / ', $oper );

        }
        // 输出json
        return $this->output_table($data);
	}

    public function get_categoriesAction(){
        $categories = sCategory::getCategoryByPid( 0 ); //只能有两级目录

        return $categories;
    }

    public function get_category_of_threadAction(){
        $target_id = $this->get('target_id', 'int');
        $target_type = $this->get('target_type', 'int');

        $thread_categories = sThreadCategory::getCategoriesByTarget( $target_type, $target_id );
        $valid_status = [
            mCategory::STATUS_NORMAL,
            mCategory::STATUS_READY,
            mCategory::STATUS_HIDDEN,
            mCategory::STATUS_CHECKED
        ];
        $categories = [];
        $cat_base = config('global.CATEGORY_BASE');
        foreach( $thread_categories as $th_cat ){
            if( in_array( $th_cat->status, $valid_status ) != false
                && $th_cat->category_id > $cat_base
                ){
                $category = sCategory::getCategoryById( $th_cat->category_id );
                if( $category->pid != 0 ){
                    $categories[] = sCategory::detail( $category );
                }
            }
        }
        return $this->output_json( $categories );
    }

    public function search_categoryAction(){
        $name = $this->get( 'q', 'string', '');
        $target_type = $this->get( 'target_type', 'int', '');
        $target_id = $this->get( 'target_id', 'int', '');
        if( empty( $name )){
            return error('EMPTY_QUERY_STRING');
        }
        $categories = sCategory::searchCategory( $name );
        return $this->output_json($categories);
    }

    public function set_categoryAction(){
        $category_id  = $this->post("category_id", "int", NULL );
        $category_display_name = $this->post("category_display_name", "string");
        $categoryName = md5( $category_display_name);//$this->post("category_name", "string");
        $parent_category_id = $this->post( 'pid', 'int', mCategory::CATEGORY_TYPE_CHANNEL );
        $pc_pic = $this->post( 'pc_pic', 'string', '' );
        $app_pic = $this->post( 'app_pic', 'string', '' );
        $banner_pic = $this->post( 'banner_pic', 'string', '' );
        $pc_banner_pic = $this->post( 'pc_banner_pic', 'string', '' );
        $url = $this->post( 'url', 'string','' );
        $icon = $this->post( 'category_icon', 'string','' );
        $desc = $this->post( 'desc', 'string','' );
        $post_btn = $this->post( 'post_btn', 'string','' );

        if(is_null($categoryName) || is_null($category_display_name)){
            return error('EMPTY_CATEGORY_NAME');
        }


        $category = sCategory::updateCategory(
            $this->_uid,
            $category_id,
            $categoryName,
            $category_display_name,
            $parent_category_id,
            $pc_pic,
            $app_pic,
            $banner_pic,
            $pc_banner_pic,
            $url,
            $icon,
            $post_btn,
            $desc
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

    public function update_statusAction(){
        $id = $this->post( 'id', 'int' );
        $status = $this->post( 'status', 'string' );
        if( !$id ){
            return error('EMPTY_CATEGORY_ID');
        }
        if( !$status ){
            return error( 'EMPTY_STATUS' );
        }

        sCategory::updateStatus( $id, $status );

        return $this->output_json( ['result'=>'ok'] );
    }

    public function getCategoryKeywordHasActivityChannelListAction()
    {
        $q = $this->get('q','string','all');
        return $this->output_json(sCategory::getCategoryKeywordHasActivityChannelList($q));
    }
}

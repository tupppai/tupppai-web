<?php namespace App\Http\Controllers\Admin;

use App\Models\Category as mCategory;

use App\Services\Category as sCategory;
use App\Facades\CloudCDN;

class TutorialController extends ControllerBase {
	public function indexAction(){
		return $this->output();
	}

	public function list_tutorialsAction(){
        $category = new mCategory;
        // 检索条件
        $cond = array();
        $cond['id']             = $this->post("category_id", "int");
        //todo: remove
        $cond['pid'] = mCategory::CATEGORY_TYPE_TUTORIAL;
        if( $this->get('all', 'string', NULL) ){
            $cond['id'] = [config('global.CATEGORY_BASE'), '>'];
            $cond['pid'] = ['0', '>'];
            $cond['status'] = ['0', '>'];
        }
        $cond['categoryName']           = array(
            $this->post("categoryName", "string"),
            'LIKE'
        );
        $cond['display_name']   = array(
            $this->post("category_display_name", "string"),
            'LIKE'
        );

        // 用于遍历修改数据
        $data  = $this->page($category, $cond, [], ['status'=>'DESC', 'order ASC', 'id']);

        foreach($data['data'] as $row){
            $category_id = $row->id;
            $row->display_name = '<a href="/verify/channels?status=valid&category_id='.$category_id.'">'.$row->display_name.'</a>';
			$row->link = '<a href="'.$row->url.'">链接</a>';
			$row->pc_banner_pic  = $row->pc_banner_pic  ? '<img src="'.CloudCDN::file_url( $row->pc_banner_pic  ).'" />' : '无';
            $row->banner_pic = $row->banner_pic ? '<img src="'.CloudCDN::file_url( $row->banner_pic ).'" />' : '无';

            $par = sCategory::getCategoryById( $row->pid );
            $row->parent_name = $par?$par->display_name: '无';
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

	public function set_tutorialAction(){
        $category_id  = $this->post("tutorial_id", "int", NULL );
        $category_display_name = $this->post("tutorial_display_name", "string");
        $categoryName = md5( $category_display_name);//$this->post("tutorial_name", "string");
        $parent_category_id = $this->post( 'pid', 'int', mCategory::CATEGORY_TYPE_TUTORIAL );
        $banner_pic = $this->post( 'banner_pic', 'string', '' );
        $pc_banner_pic = $this->post( 'pc_banner_pic', 'string', '' );
        $pc_pic = $this->post( 'pc_pic', 'string', $pc_banner_pic );
        $app_pic = $this->post( 'app_pic', 'string', $banner_pic );
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
            $desc,
            ''
        );

        return $this->output( ['id'=>$category->id] );
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
}

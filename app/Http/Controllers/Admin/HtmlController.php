<?php namespace App\Http\Controllers\Admin;

use App\Models\Html as mHtml;
use App\Services\Html as sHtml;

class HtmlController extends ControllerBase {

    public function indexAction(){

        return $this->output();
    }

    public function addAction() { 
        $id = $this->get('id', 'int');

        $data = array();
        $data['id']      = '';
        $data['title']   = '';
        $data['content'] = '';

        $html = sHtml::getHtmlById($id);
        if($html) {
            $data['id'] = $html->id;
            $data['title']   = $html->title;
            $data['content'] = file_get_contents($html->path);
        }

        return $this->output($data);
    }

    public function set_htmlAction() {
        $id      = $this->post('id');
        $title   = $this->post('title');
        $content = $this->post('content');

        $html = sHtml::getHtmlById($id);
        if(!$html) {
            $html = new mHtml;
            $path = base_path().'/public/htmls/'.date('Ymd')."-$title.html";
            $url  = '/htmls/'.date('Ymd')."-$title.html";

            $html->assign(array(
                'status'=>mHtml::STATUS_NORMAL,
                'path'=>$path,
                'url'=>$url,
                'create_by'=>$this->_uid,
                'update_by'=>$this->_uid
            ));
        }

        file_put_contents($html->path, $content);
        $html->title = $title;
        $html->save();

        return $this->output();
    }

    public function list_htmlsAction(){
        $model = new mHtml;

        $cond = array();
        $cond['uid'] = array(
            $this->post('uid'),
            'LIKE',
            'AND'
        );
        $cond['status'] = mHtml::STATUS_NORMAL;
        $join = array();
        $order = array();

        $data = $this->page($model, $cond, $join, $order );
        foreach ($data['data'] as $app) {
            $app->url  = 'http://'.env('API_HOST').$app->url;
            $app->path = "<a target='_blank' href='".$app->url."'>链接</a>";
            $app->oper = '<a href="/html/add?id='.$app->id.'" class="edit">编辑</a>';
            //$app->oper .= ' <a href="#" class="delete">删除</a>';
        }

        return $this->output_table($data);
    }

    public function save_bannerAction(){
        if( !Request::ajax() ){
            return error('WRONG_ARGUMENTS');
        }

        $desc = $this->post('desc','string');
        if( empty($desc) ){
            return error('EMPTY_CONTENT');
        }
        $id = $this->post('id', 'int');

        $large_pic = $this->post('large_pic','string');
        $small_pic = $this->post('small_pic','string');

        $url = $this->post('url', 'url');
        $pc_url = $this->post('pc_url', 'url');
        if( empty($url)){
            return error('EMPTY_JUMP_URL');
        }

        $uid = $this->_uid;
        if($id && $banner = sBanner::getBannerById($id)) {
            $banner->desc = $desc;
            $banner->large_pic  = $large_pic;
            $banner->small_pic  = $small_pic;
            $banner->url = $url;
            $banner->pc_url  = $pc_url;
            $banner->save();
        }
        else {
            sBanner::addNewBanner( $uid, $desc, $small_pic, $large_pic, $url, $pc_url);
        }

        return $this->output();
    }

    public function del_bannerAction(){

        if( !Request::ajax() ){
            return error('WRONG_ARGUMENTS');
        }

        $banner_id = $this->post('banner_id', 'int');
        if(empty($banner_id)){
            return error('EMPTY_ID');
        }

        sBanner::delBanner($this->_uid, $banner_id);

        return $this->output();
    }

    public function sort_bannersAction(){
        if( !Request::ajax()){
            return error('WRONG_ARGUMENTS');
        }
        $app_sort = $this->post('sorts','string');
        $sorts = array_filter( explode(',', $app_sort) );

        if( empty($sorts) ){
            return error('WRONG_ARGUMENTS');
        }

        sBanner::sortBanners($sorts);

        return $this->output();
    }
}

<?php namespace App\Http\Controllers\Admin;

use App\Models\App;
use App\Models\ActionLog;

use App\Services\User;
use App\Services\Ask;

use App\Facades\CloudCDN;

class AppController extends ControllerBase {

    public function indexAction(){

        return $this->output();
        //return view('admin.app.index', array(123));
        //$log = new \App\Models\ActionLog;
        //ActionLog::log(ActionLog::TYPE_OTHERS, $upload, $upload2);
    }

    public function list_appsAction(){
        $appModel = new App;

        $cond = array();
        $cond['app_name'] = array(
            $this->post('app_name'),
            'LIKE',
            'AND'
        );
        $cond['del_time'] = array('junk','NULL');
        $join = array();
        $join['Upload'] = array('logo_upload_id','id');
        $order = array();
        $order[]='order_by ASC';

        $data = $this->page($appModel, $cond, $join, $order );
        foreach ($data['data'] as $app) {
            $app->logo = '<img class="applogo" src="'.CloudCDN::file_url($app->savename).'"/>';
            $app->app_name = '<a target="_blank" href="'.$app->jumpurl.'">'.$app->app_name.'</a>';
            $app->create_time = date('Y-m-d H:i:s', $app->create_time);
            $app->oper = '<a href="#" class="delete">删除</a>';
        }

        return $this->output_table($data);
    }

    public function save_appAction(){
        $this->noview();
        if( !$this->request->isAjax() ){
            return array();
        }

        $app = new App();
        $app->app_name = $this->post('app_name','string');
        if( empty($app->app_name) ){
            return ajax_return(2, '应用名称不能为空', 'error');
        }

        $app->logo_upload_id = $this->post('logo_id','int');
        if( empty($app->logo_upload_id) ){
            return ajax_return(3, '请上传Logo', 'error');
        }

        $app->jumpurl = $this->post('jump_url', 'string');
        if( empty($app->jumpurl)){
            return ajax_return(4, '请输入跳转链接', 'error');
        }
        if( !filter_var($app->jumpurl, FILTER_CALLBACK, array('options' => 'match_url_format')) ){
            return ajax_return(6, '请输入正确的URL格式');
        }

        $app->create_by = $this->_uid;
        $app->create_time = time();
        $app->order_by = 9999;
        if( $app->save() ){
            ActionLog::log(ActionLog::TYPE_ADD_APP, array(), $app);

            return ajax_return(1,'添加成功','ok');
        }
        else{
            return ajax_return(5,'添加失败','error');
        }
    }

    public function del_appAction(){
        $this->noview();

        if( !$this->request->isAjax() ){
            return array();
        }

        $app = new App();
        $app_id = $this->post('app_id', 'int');
        if(empty($app_id)){
            return ajax_return(4,'请选择要删除的app');
        }

        $app = $app::findFirst($app_id);
        if( !$app ){
            return ajax_return(2,'没有这个app', 'error');
        }

        $app->del_by = $this->_uid;
        $app->del_time = time();
        if( $app->save() ){
            ActionLog::log(ActionLog::TYPE_DELETE_APP, array(), $app);

            return ajax_return(1,'删除成功', 'ok');
        }
        else{
            return ajax_return(3,'删除失败','error');
        }
    }

    public function sort_appsAction(){
        $this->noview();
        if( !$this->request->isAjax()){
            return array();
        }
        $app_sort = $this->post('sorts','string');
        $sorts = array_filter( explode(',', $app_sort) );

        if( empty($sorts) ){
            return ajax_return(2, '没传顺序', 'error');
        }

        $appModel = new App();
        foreach ($sorts as $order => $id) {
            $app = $appModel->findFirst($id);
            $app->order_by = $order+1;
            $app->save();
        }
        return ajax_return('1','调整顺序成功','ok');
    }

    public function get_app_listAction(){
        $this->noview();
        if( !$this->request->isAjax() ){
            return;
        }

        $app = new App();
        return ajax_return(0, 'ok', $app->get_list() );
    }
}

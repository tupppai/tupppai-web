<?php namespace App\Http\Controllers\Admin;

use App\Models\App;
use App\Models\ActionLog;

use App\Services\User;
use App\Services\Ask;

use App\Facades\CloudCDN, Log, Queue, Request;
use Carbon\Carbon;
use App\Jobs\Push;

use App\Jobs\SendEmail;

use App\Services\Ask as sAsk;
use App\Services\Reply as sReply;
use App\Services\Comment as sComment;
use App\Services\User as sUser;

use App\Models\Banner as mBanner;
use App\Services\Banner as sBanner;

class BannerController extends ControllerBase {

    public function indexAction(){

        return $this->output();
    }

    public function list_bannersAction(){
        $model = new mBanner;

        $cond = array();
        $cond['uid'] = array(
            $this->post('uid'),
            'LIKE',
            'AND'
        );
        $join = array();
        $order = array();

        $data = $this->page($model, $cond, $join, $order );
        foreach ($data['data'] as $app) {
            $app->small_pic = '<img class="applogo" src="'.$app->small_pic.'"/>';
            $app->large_pic = '<img class="applogo" src="'.$app->large_pic.'"/>';
            $app->create_time = date('Y-m-d H:i:s', $app->create_time);
            $app->oper = '<a href="#" class="delete">删除</a>';
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

        $large_pic = $this->post('large_pic','string');
        $small_pic = $this->post('small_pic','string');

        $url = $this->post('url', 'url');
        if( empty($url)){
            return error('EMPTY_JUMP_URL');
        }

        $uid = $this->_uid;
        sBanner::addNewBanner( $uid, $desc, $small_pic, $large_pic, $url);

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
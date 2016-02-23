<?php namespace App\Http\Controllers\Admin;

use App\Models\App as mApp;
use App\Models\Count as mCount;
use App\Models\ActionLog;

use App\Services\User;
use App\Services\Ask;

use App\Facades\CloudCDN, Log, Queue, Cache, Request;
use Carbon\Carbon;
use App\Jobs\Push;

use App\Jobs\SendEmail;

use App\Services\Ask as sAsk;
use App\Services\Reply as sReply;
use App\Services\Count as sCount;
use App\Services\Comment as sComment;
use App\Services\User as sUser;
use App\Services\App as sApp;

use App\Counters\AskReplies as cAskReplies;
use App\Counters\AskComments as cAskComments;
use App\Counters\ReplyComments as cReplyComments;
use App\Counters\ReplyUpeds as cReplyUpeds;
use App\Counters\UserBadges as cUserBadges;

use App\Jobs\SendSms as jSendSms;

use App\Models\Sms as mSms;

use App\Trades\Order as tOrder;

class AppController extends ControllerBase {

    public function testAction() {
        $reply = sReply::getReplyById(8690);
        fire('TRADE_HANDLE_REPLY_SAVE',['reply'=>$reply]);
        //dd(sReply::getRepliesCountByAskId(2249));
        //dd(Carbon::now()->addMinutes(3));
        return ;
        mUser::where('uid', '>', 1000)->get();
        return ;
        $order = new tOrder(1);
        $order->setPaymentType(1);
        dd($order);

        return ;
        dd((new mSms)->today_useless_sms_count());

        Queue::push(new jSendSms(15018749436, 1234));
        
        dd(sCount::getLoveReplyNum(1, 1));

        dd(cUserBadges::inc(1));
        dd(sReply::shareReply(8345, mCount::STATUS_NORMAL));
        dd(cReplyComments::get(8435));
        dd(cReplyUpeds::get(8435));
        cAskComments::inc(1816, _uid());
        cAskReplies::inc(1810, _uid());

        $password = sUser::hash(123123);
        pr($password, false);
        dd(sUser::verify('123123', $password));
        $uid = 1;
        $reply_to = 253;
        $msg_type       = 'comment_comment';
        #评论推送
        Queue::push(new Push(array(
            'uid'=>$uid,
            'target_uid'=>$reply_to,
            'type'=>$msg_type,
            'comment_id'=>1,
            'for_comment'=> 0
        )));
    }

    public function pushAction() {

        $type = $this->post('type', 'int');
        $uid  = $this->post('uid', 'int');
        $target_id = $this->post('target_id', 'int');
        if(!$type || !$uid || !$target_id) {
            return $this->output();
        }
        switch($type) {
        case 'post_reply':
            $reply = sReply::getReplyById($target_id);
            Queue::push(new Push(array(
                'uid'=>$uid,
                'ask_id'=>$reply->ask_id,
                'reply_id'=>$reply->id,
                'type'=>'post_reply'
            )));
            break;
        case 'post_ask':
            $ask = sAsk::getAskById($target_id);
            Queue::push(new Push(array(
                'uid'=>$uid,
                'ask_id'=>$ask->id,
                'type'=>'post_ask'
            )));
            break;
        case 'like_reply':
            $reply = sReply::getReplyById($target_id);
            Queue::push(new Push(array(
                'uid'=>$uid,
                'target_uid'=>$reply->uid,
                //前期统一点赞,不区分类型
                'type'=>'like_reply',
                'target_id'=>$target_id
            )));
            break;
        case 'follow':
            #关注推送
            Queue::push(new Push(array(
                'uid'=>$uid,
                'target_uid'=>$target_id,
                'type'=>'follow'
            )));
            break;
        case 'comment_ask':
            $ask = sAsk::getAskById($target_id);
            #评论推送
            Queue::push(new Push(array(
                'uid'=>$uid,
                'target_uid'=>$ask->uid,
                'type'=>'comment_ask',
                'comment_id'=>1,
                'for_comment'=> 0
            )));
            break;
        case 'comment_reply':
            $reply = sReply::getReplyById($target_id);
            #评论推送
            Queue::push(new Push(array(
                'uid'=>$uid,
                'target_uid'=>$reply->uid,
                'type'=>'comment_reply',
                'comment_id'=>1,
                'for_comment'=> 0
            )));
            break;

        }
        return $this->output();
    }

    public function indexAction(){

        return $this->output();
    }

    public function list_appsAction(){
        $app_name = $this->post('app_name','string');
        $status = mApp::STATUS_NORMAL;
        $data = sApp::getAppList($app_name, $status);

        $i=0;
        foreach ($data['data'] as $app) {
            $app['logo']        = '<img class="applogo" src="'.CloudCDN::file_url($app['savename']).'"/>';
            $app['app_name']    = '<a target="_blank" href="'.$app['jumpurl'].'">'.$app['app_name'].'</a>';
            $app['create_time'] = date('Y-m-d H:i:s', $app['create_time']);
            $app['oper']        = '<a href="#" class="delete">删除</a>';
            $data['data'][$i] = $app;
            $i++;
        }

        return $this->output_table($data);
    }

    public function save_appAction(){
        $app_name = $this->post('app_name','string');
        if( empty($app_name) ){
            return error('EMPTY_APP_NAME');
        }

        $logo_upload_id = $this->post('logo_id','int');
        if( empty($logo_upload_id) ){
            return error('EMPTY_ID');
        }

        $jumpurl = $this->post('jump_url', 'url');
        if( empty($jumpurl)){
            return error('EMPTY_URL');
        }

        sApp::addNewApp( $this->_uid, $app_name, $logo_upload_id, $jumpurl );

        return $this->output_json(['result'=>'ok']);
    }

    public function del_appAction(){
        $app_id = $this->post('app_id', 'int');
        if(empty($app_id)){
            return error('EMPTY_ID');
        }

        sApp::delApp($this->_uid, $app_id);

        return $this->output_json(['result'=>'ok']);
    }

    public function sort_appsAction(){
        #todo: skys
        if( !Request::ajax()){
            return error('WRONG_ARGUMENTS');
        }
        $app_sort = $this->post('sorts','string');
        $sorts = array_filter( explode(',', $app_sort) );

        if( empty($sorts) ){
            return error('WRONG_ARGUMENTS');
        }

        sApp::sortApps($sorts);

        return $this->output();
    }

    public function get_app_listAction(){
        if( !Request::ajax() ){
            return error('WRONG_ARGUMENTS');
        }

        $mApp = new sApp();
        $apps = $mApp->getAppList();

        return $this->output($apps);
    }

    //更新asks对应的作品 最后更新时间
    public function updateLastUpdateTimeAction() {
        $last_time_replys = \DB::table('replies')
            ->select('ask_id', 'update_time')
            ->orderBy('update_time')
            ->groupBy('ask_id')
            ->get();

        foreach ($last_time_replys as $key => $reply) {
            \DB::table('asks')
                ->where('id', $reply->ask_id)
                ->update(['last_reply_time' => $reply->update_time]);     
        } 
    }
}

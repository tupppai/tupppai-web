<?php namespace App\Http\Controllers\Android;

use App\Services\ActionLog as sActionLog,
    App\Services\Device as sDevice,
    App\Services\User as sUser,
    App\Services\UserLanding as sUserLanding,
    App\Services\UserDevice as sUserDevice;

use App\Models\User as mUser;
use App\Models\UserLanding as mUserLanding;

use App\Facades\Sms;

class UserController extends ControllerBase
{
    public $_allow = array(
        'login',
        'get_mobile_code',
        'save',
        'device_token',
        'check_token',
        'check_mobile',
        'test'
    );

    public function __construct(){
        parent::__construct();
    }

    public function testAction(){
        dd(Sms::make([
              'YunPian'    => '1',
              'SubMail'    => '123'
          ])
          ->to('15018749436')
          ->data(['皮埃斯网络科技', '123456'])
          ->content('【皮埃斯网络科技】您的验证码是123456'));
    }

    public function device_tokenAction() {
        $uid      = $this->_uid;

        $name     = $this->post("device_name", 'string');
        $os       = $this->post("device_os", 'string');
        $platform = $this->post('platform','int', 0);
        $mac      = $this->post("device_mac", 'string');
        $token    = $this->post("device_token", 'string');
        $options  = $this->post("options", 'string', '');

        if( empty($mac) )
            return error('EMPTY_DEVICE_MAC');
        if( empty($os) )
            return error('EMPTY_DEVICE_OS');
        if( empty($token) )
            return error('EMPTY_DEVICE_TOKEN');

        $deviceInfo = sDevice::updateDevice( $uid, $name, $os, $platform, $mac, $token, $options );
        $userDevice = sUserDevice::bindDevice( $uid, $deviceInfo->id );

        return $this->output();
    }

    /**
     * 检测手机是否被注册
     */
    public function check_mobileAction() {
        $phone = $this->get('phone', 'string', '');
        if( empty($phone) ){
            return error('WRONG_ARGUMENTS');
        }

        if (!match_phone_format($phone)) {
            return error('INVALID_PHONE_NUMBER');
        }
        if ( sUser::getUserByPhone($phone) )  {
            return error('PHONE_ALREADY_EXIST', 'phone already exist', array(
                'is_register' => 1
            ));
        }

        return $this->output(array(
            'is_register'=>0
        ));
    }

    /**
     * 获取用户个人信息
     */
    public function infoAction(){
        $user = sUser::getUserByUid($this->_uid);
        $data = sUser::detail($user);

        return $this->output($data);
    }

    public function loginAction(){
        $username   = $this->post('username', 'string');
        $phone      = $this->post('phone', 'string');
        $password   = $this->post('password', 'string');

        #todo: remove
        $phone = "13580504992";
        $password = "123123";

        if ( (is_null($phone) and is_null($username)) or is_null($password) ) {
            return error('WRONG_ARGUMENTS');
        }

        $user = sUser::loginUser($phone, $username, $password);
        session(['uid'=>$user['uid']]);
        //$this->session->set('uid', $user['uid']);

        return $this->output($user);
    }

    public function saveAction()
    {
        //get platform
        $type     = $this->post('type', 'string');
        //todo: 验证码
        $code     = $this->post('code');
        //post param
        $mobile   = $this->post('mobile', 'string');
        $password = $this->post('password', 'string');
        $nickname = $this->post('nickname', 'string');
        $avatar   = $this->post('avatar', 'string');
        $location = $this->post('location', 'string');
        $city     = $this->post('city', 'int');
        $province = $this->post('province', 'int');
        //$location = $this->encode_location($province, $city, $location);

        $sex      = $this->post('sex', 'string');
        $openid   = $this->post('openid','string');
        $auth     = $this->post('auth', 'string');
        $avatar_url = $this->post('avatar_url', 'string');


        if(!$mobile) {
            return $this->output( '请输入手机号码' );
        }
        if(!$password) {
            return $this->output( '请输入密码' );
        }
        $user   = sUser::getUserByPhone($mobile);
        if($user && !$openid){
            return $this->output( '手机已注册' );
        }

        $user = new mUser();
        sActionLog::init( 'REGISTER', $user );
        switch($type){
        case 'mobile':
            if(!$avatar) {
                return $this->output( '请上传头像' );
            }
            $username = '';
            $email    = '';
            $options  = [];

            $user =sUser::addNewUser($username, $password, $nickname, $mobile, $location, $email, $avatar, $sex, $options);
            if($user) {
                $data = sUser::detail( $user );
                session(['uid' => $user->uid]);
                sActionLog::save( $user );
                return $this->output( $data, '手机注册成功！' );
            } else{
                return $this->output( '手机注册失败！' );
            }
        case 'weixin':
            if(!$avatar_url && !$avatar) {
                return $this->output( '请上传头像' );
            }
            if(!$openid) {
                return $this->output( '请重新微信授权！' );
            }

            //todo check mobile code
            if(sUserLanding::getUserByOpenid($openid, mUserLanding::TYPE_WEIXIN)){
                return $this->output( '微信注册失败！用户已存在' );
            }
            if($user){
                $user = sUserLanding::updateAuthUser(
                    $user,
                    $openid,
                    mUserLanding::TYPE_WEIXIN,
                    $mobile,
                    $password,
                    $location,
                    $nickname,
                    $avatar_url,
                    $sex
                );

            }
            else {
                $user = sUserLanding::addAuthUser(
                    $openid,
                    mUserLanding::TYPE_WEIXIN,
                    $mobile,
                    $password,
                    $location,
                    $nickname,
                    $avatar_url,
                    $sex
                );
            }

            if($user) {
                $data = $user->format_login_info();
                $this->session->set('uid', $user->uid);
                sActionLog::save( $user );
                return $this->output( $data, '微信注册成功！' );
            }
            return $this->output( '微信注册失败！' );
        case 'weibo':
            if(!$avatar_url && !$avatar) {
                return $this->output( '请上传头像' );
            }
            if(!$openid) {
                return $this->output( '请重新微博授权！' );
            }

            if(UserLanding::getUserByOpenid($openid, mUserLanding::TYPE_WEIBO)){
                return $this->output( '微博注册失败！用户已存在' );
            }

            if($user){
                $user = UserLanding::updateAuthUser(
                    $user,
                    $openid,
                    mUserLanding::TYPE_WEIXIN,
                    $mobile,
                    $password,
                    $location,
                    $nickname,
                    $avatar_url,
                    $sex
                );

            }
            else {
                $user = UserLanding::addAuthUser(
                    $openid,
                    mUserLanding::TYPE_WEIBO,
                    $mobile,
                    $password,
                    $location,
                    $nickname,
                    $avatar_url,
                    $sex
                );
            }

            if($user) {
                $data = $user->format_login_info();
                $this->session->set('uid', $user->uid);
                ActionLog::save( $user );
                return $this->output( $data, '微博注册成功！' );
            }
            return $this->output( '微博注册失败！' );
            break;
        default:
            return $this->output( '注册类型出错！' );
        }
    }

    public function count_unread_noticesAction( $type = '' ){
        $uid = $this->_uid;
        $page = $this->get('page', 'int', 1);
        $size = $this->get('size', 'int', 15);

        $unread= array();
        $unread['comment'] = Comment::count_unread( $uid );
        $unread['follow'] = Follow::count_new_followers( $uid );
        $unread['invite'] =  Invitation::count_new_invitation( $uid );
        $unread['reply'] = Reply::count_unread_reply( $uid );
        $unread['system'] = SysMsg::count_unread_sysmsgs( $uid );

        return $this->output( $unread );
    }

    public function get_mobile_codeAction() {
        $phone = $this->get('phone', 'string', '');
        if (match_phone_format($phone)) {
            //$active_code = mt_rand(100000, 9999999);    // 六位手机验证码
            $active_code  = '123456';

            /*
            $Msg = new \Msg();
            $send = $Msg -> phone( $phone )
                         -> content( str_replace('::code::', $active_code, VERIFY_MSG) )
                         -> send();

            if(!$send) {
                return $this->output( '验证码发送失败'  );
            }
            */
           $this->session->set('code',$active_code);

            return $this->output(array('code'=>$active_code));
        } else {
            return $this->output( '输入的手机号码不符合要求，请确认后重输' );
        }
    }

    /**
     * [editAction 修改个人资料]
     * @return [type] [description]
     */
    public function editAction(){
        $uid = $this->_uid;

        $nickname = $this->post('nickname');
        $avatar   = $this->post('avatar');
        $sex      = $this->post('sex');
        $location = $this->post('location');
        $city     = $this->post('city');
        $province = $this->post('province');

        $user = User::findUserByUID($uid);
        if( !$user ){
            return $this->output( false, 'user doesn\'t exists' );
        }
        $old = ActionLog::clone_obj( $user );

        if($nickname) {
            if(User::findUserByNickname($nickname)) {
                $data = array('result' => 2);
                return $this->output( $data, 'nickname be used' );
            }
            $user->nickname = $nickname;
        }

        if($avatar) {
            $user->avatar = $avatar;
        }

        if($sex) {
            $user->sex = $sex;
        }

        if($location || $city || $province) {
            $location = $this->encode_location($province, $city, $location);
            $user->location = $location;
        }

        $user->update_time = time();

        // 保存数据
        if ($user->save_and_return($user)) {
            $data = array('result' => 1);
            ActionLog::log(ActionLog::TYPE_MODIFY_USER_INFO, $old, $user);
            return $this->output( $data, 'ok' );
        }else{
            $data = array('result' => 0);
            return $this->output( $data, 'error' );
        }
    }

    /**
     * [collecAction 收藏/取消收藏 回复]
     */
    public function collectAction(){
        $rid    = $this->post('rid', 'int');             // 回复ID
        $status = $this->post('status', 'int');       // 收藏或取消收藏 1收藏 0 取消收藏
        $uid    = $this->_uid;

        if (empty($rid) || empty($status)) {
            return $this->output( array('result' => 0), '非法操作' );
        }

        $result = Collection::collection($uid, $rid, $status);

        if ($result){
            return $this->output( array('result' => 1) );
        }else{
            return $this->output( array('result' => 0), 'error' );
        }
    }

    /**
     * [focusAction 关注/取消关注 问题]
     */
    public function focusAction(){
        $aid    = $this->post('aid', 'int');          // 提问id
        $status = $this->post('status', 'int');       // 关注或取消关注 1 关注 0 取消关注
        $uid    = $this->_uid;

        if (empty($aid) || empty($status)) {
            return $this->output( array('result' => 0), '非法操作' );
        }

        $result = Focus::focus($uid, $aid, $status);

        if ($result){
            return $this->output( array('result' => 1) );
        }else{
            return $this->output( array('result' => 0), 'error' );
        }
    }

    /**
     * 我的作品Reply
     */
    public function my_replyAction() {
        $uid            = $this->_uid;
        $page           = $this->get("page", "int", 1);
        $size           = $this->get("size", "int", 15);
        $width          = $this->get("width", "int", 480);
        $last_updated   = $this->get("last_updated", "int", time());

        //我的作品 Reply
        $reply_items    = Reply::userReplyList($uid, $last_updated, $page, $size);
        $data           = array();
        foreach ($reply_items as $reply) {
            $data[] = $reply->toStandardArray($uid, $width);
        }

        return $this->output( $data, "okay" );
    }

    /**
     * 我的求P
     */
    public function my_askAction() {
        $uid            = $this->_uid;
        $page           = $this->get("page", "int", 1);
        $size           = $this->get("size", "int", 15);
        $width          = $this->get("width", "int", 480);
        $last_updated   = $this->get("last_updated", "int", time());

        //我的求P
        $ask_items      = Ask::userAskList($uid, $last_updated, $page, $size);
        $data = array();
        foreach ($ask_items as $ask) {
            $data[]  = $ask->toStandardArray($uid, $width);
        }

        return $this->output( $data, "okay" );
    }

    /**
     * [my_collectionAction 我的收藏]
     * @return [type] [description]
     */
    public function my_collectionAction(){
        $uid          = $this->_uid;

        $page         = $this->get('page', 'int', 1);       // 页码
        $size         = $this->get('size', 'int', 15);   // 每页显示数量
        $width        = $this->get('width', 'int', 480);
        $last_updated = $this->post('last_updated', 'int', time());

        // 我的收藏
        $reply_items  = Reply::collectionList($uid, $page, $size);
        $data = array();
        foreach ($reply_items as $reply) {
            $data[] = $reply->toStandardArray($uid, $width);
        }
        return $this->output( $data, "okay" );
    }

    /**
     * [my_focusAction 我的关注]
     * @return [type] [description]
     */
    public function my_focusAction(){
        $uid = $this->_uid;

        $page  = $this->get('page', 'int', 1);           // 页码
        $size  = $this->get('size', 'int', 15);       // 每页显示数量
        $width = $this->get('width', 'int', 480);     // 屏幕宽度
        $last_updated = $this->get('last_updated', 'int', time());

        // 我的关注
        $ask_items    = Ask::focusList($uid, $page, $size);
        $data = array();
        foreach ($ask_items as $ask) {
            $data[] = $ask->toStandardArray($uid, $width);
        }

        return $this->output( $data, "okay" );
    }

    /**
     * [my_focusAction 我的关注]
     * @return [type] [description]
     */
    public function my_collectionfocusAction(){
        $uid = $this->_uid;

        $page  = $this->get('page', 'int', 1);           // 页码
        $size  = $this->get('size', 'int', 15);       // 每页显示数量
        $width = $this->get('width', 'int', 480);     // 屏幕宽度
        $last_updated = $this->get('last_updated', 'int', time());

        $items = User::getCollectionFocus($this->_uid, $last_updated, $page, $width);
        $data  = array();
        foreach($items as $item) {
            if($item['type'] == Label::TYPE_ASK)
                $model = new Ask();
            else
                $model = new Reply();

            foreach($item as $key=>$val){
                $model->$key = $val;
            }
            $data[] = $model->toStandardArray($uid, $width);
        }

        return $this->output( $data, "okay" );
    }

    /**
     * 获取我的粉丝列表
     * @return [type] [description]
     */
    public function myFansAction(){
        $page = $this->get('page', 'int', 1);
        $size = $this->get('size', 'int', 15);

        $data = array();
        $data = User::myFansList($this->_uid, $page, $size);
        return $this->output( $data );
    }

    /**
     * 获取我的fellow列表
     * @return [type] [description]
     */
    public function myFellowAction(){
        $page = $this->get('page', 'int', 1);
        $size = $this->get('size', 'int', 15);
        $uid  = $this->get('uid', 'int', $this->_uid);

        $data = array();
        $recommends = array();
        $data = User::myFellowList($this->_uid, $page, $size);
        $recommends = User::recommendFellows($this->_uid);
        return $this->output( array(
            'recommends'=>$recommends,
            'fellows'=>$data
        ));
    }

    /**
     * 检查token是否有效
     */
    public function check_tokenAction()
    {
        $token = $this->post('token','string');
        if(!$token || $token == '') {
            return $this->output( 'err' );
        }

        if($this->check_token($token)) {
            return $this->output();
        }
        return $this->output( 'err' );
    }

    public function othersAction() {
        $uid  = $this->get('uid',  'int');
        $page = $this->get('page', 'int', 1);
        $size = $this->get('size', 'int', 15);
        $width= $this->get('width', 'int', 480);
        $type = $this->get('type', 'int', 0);
        $last_updated = $this->get('last_updated', 'int', time());
        if( !$uid ){
            return $this->output( '请选择用户' );
        }
        $user = User::findFirst($uid);
        if(!$user) {
            return $this->output( '请选择用户' );
        }

        $data = array();
        $data = $user->to_simple_array();
        $data['is_fans'] = $user->is_fans_to($this->_uid);
        $data['is_fellow'] = $user->is_fellow_to($this->_uid);

        $data['asks'] = array();
        if($page == 1  || $type == Label::TYPE_ASK) {
            $asks = Ask::userAskList($uid, $last_updated, $page, $size);
            foreach ($asks as $ask) {
                $data['asks'][] = $ask->toStandardArray($uid, $width);
            }
        }
        $data['replies'] = array();
        if($page == 1 || $type == Label::TYPE_REPLY) {
            $replies = Reply::userReplyList($uid, $last_updated, $page, $size);
            foreach ($replies as $reply) {
                $data['replies'][] = $reply->toStandardArray($uid, $width);
            }
        }
        return $this->output( $data );
    }

    public function othersFansAction(){
        $uid  = $this->get('uid',  'int', 3);
        $page = $this->get('page', 'int', 1);
        $size = $this->get('size', 'int', 15);

        $data = array();
        $data = User::othersFansList($uid, $this->_uid);

        return $this->output( $data );
    }

    public function othersFellowAction(){
        $uid  = $this->get('uid',  'int', 3);
        $page = $this->get('page', 'int', 1);
        $size = $this->get('size', 'int', 15);

        $data = array();
        $data = User::othersFellowList($this->_uid, $uid);
        return $this->output( $data );
    }

    public function my_proceedingAction() {

        $uid = $this->_uid;
        $page = $this->get('page','int',1);
        $size = $this->get('size','int',10);
        $width = $this->get('width', 'int', '480');
        $last_updated = $this->get('last_updated', 'int', time());

        $items = Download::get_progressing($uid, $last_updated, $page, $size)->items;
        $data = array();
        foreach ($items as $item) {
            if($item->type == Download::TYPE_ASK) {
                $ask = Ask::findFirst($item->target_id);
                $data[] = $ask->toStandardArray($uid, $width);
            } else {
                $reply = Reply::findFirst($item->target_id);
                $data[] = $reply->toStandardArray($uid, $width);
            }
        }

        return $this->output( $data );
    }

    public function followAction() {
        $uid = $this->post('uid');
        if(!$uid)
            return $this->output( '请选择关注的账号' );

        $me  = $this->_uid;

        $ret = Follow::setUserRelation($uid, $me, Follow::STATUS_NORMAL);
        if($ret){
            if( $ret instanceof Follow ){
                ActionLog::log(ActionLog::TYPE_FOLLOW_USER, array(), $ret);
            }
            return $this->output( 1 );
        }
        else
            return $this->output( 'error' );
    }

    public function unfollowAction() {
        $uid = $this->post('uid');
        $me  = $this->_uid;
        $ret = Follow::setUserRelation($uid, $me, Follow::STATUS_DELETED);

        if($ret){
            if( $ret instanceof Follow ){
                ActionLog::log(ActionLog::TYPE_UNFOLLOW_USER, array(), $ret);
            }
            return $this->output( 1 );
        }
        else
            return $this->output( 'error' );
    }

    public function fellowsDynamicAction() {
        $page = $this->get('page', 'int', 1);
        $size = $this->get('size', 'int', 15);
        $width= $this->get("width", "int", 480);
        $last_updated = $this->get('last_updated', 'int', time());
        $uid  = $this->_uid;

        $data = array();
        $items = User::getFellowsDynamicID($uid, $page, $size);

        $counter = 0;
        foreach ($items as $item) {
            if($counter ++ == 0)
                continue;
            switch ($item['type']) {
                case Label::TYPE_ASK:
                    $ask = Ask::findFirst($item['id']);
                    if($ask) {
                        $data[] = $ask->toStandardArray($uid, $width);
                    }
                    break;
                case Label::TYPE_REPLY:
                    $reply = Reply::findFirst($item['id']);
                    if($reply) {
                        $data[] = $reply->toStandardArray($uid, $width);
                    }
                    break;
                default:
                    break;
            }
        }

        return $this->output( $data );
    }


    public function get_recommend_usersAction(){
        $recom_user = array();
        $recom_user['recommends'] = Master::get_master_list(1,2);
        $recom_user['fellows'] = User::myFellowList($this->_uid);
        return $this->output( $recom_user );
    }

    public function get_mastersAction(){
        $page = $this->get('page', 'int', 1);
        $size = $this->get('size', 'int', 15);
        return $this->output( Master::get_master_list($page,$size) );
    }

    //通过手机修改密码
    public function reset_passwordAction(){
        //todo 验证验证码
        $phone    = $this->post('phone', 'int');
        $code    = $this->post('code', 'int');
        $new_pwd = $this->post('new_pwd');
        if(!$code) {
            return $this->output( false, '短信验证码为空' );
        }
        if(!$new_pwd) {
            return $this->output( false, '密码不能为空' );
        }
        if(!$phone) {
            return $this->output( false, '手机号不能为空' );
        }
        $user = User::findUserByPhone($phone);
        $old = ActionLog::clone_obj($user);
        if( !$user ){
            return $this->output( false, '用户不存在' );
        }

        //todo: 验证码有效期
        if( $code != $this->session->get('code') ){
            return $this->output( false, '验证码不正确' );
        }

        $reset = User::set_password( $user->uid, $new_pwd );
        if( $reset instanceof User ){
            ActionLog::log(ActionLog::TYPE_RESET_PASSWORD, $old, $reset);
        }
        return $this->output( array('status'=>(bool)$reset), 'ok' );
    }

    //通过原密码修改密码
    public function chg_passwordAction(){
        $old_pwd = $this->post('old_pwd');
        $new_pwd = $this->post('new_pwd');
        $uid = $this->_uid;

        if( $old_pwd == $new_pwd ) {
            return $this->output( 3, '新密码不能与原密码相同' );
        }
        $user = User::findFirst($uid);
        if( !$user ){
            return $this->output( false, 'user not exist' );
        }

        $old = ActionLog::clone_obj( $user );
        if( !User::verify( $old_pwd, $user->password ) ){
            return $this->output( 2, '原密码校验失败' );
        }

        $user = User::set_password( $uid, $new_pwd );
        //坑！$user instanceof User 居然是flase！因为$user 是Android\User
        if( $user ){
            ActionLog::log(ActionLog::TYPE_CHANGE_PASSWORD, $old, $user);
            return $this->output( true  );
        }
        else{
            return $this->output( false , 'error' );
        }

    }


    /**
     * [recordAction 记录下载]
     * @param type 求助or回复
     * @param target 目标id
     * @return [json]
     */
    public function recordAction() {
        $type       = $this->get('type');
        $target_id  = $this->get('target');
        $width      = $this->get('width', 'int', 480);
        $uid = $this->_uid;

        $url = '';
        if($type=='ask') {
            $type = Download::TYPE_ASK;
            if($ask = Ask::findFirst($target_id)) {
                $image  = $ask->upload->resize($width);
                $url    = $image['image_url'];
            }
        }
        else if($type=='reply') {
            $type = Download::TYPE_REPLY;
            if($reply = Reply::findFirst($target_id)) {
                $image  = $reply->upload->resize($width);
                $url    = $image['image_url'];
            }
        }
        else{
            return $this->output( '未定义类型。' );
        }

        if($url==''){
            return $this->output( '访问出错' );
        }

        //$ext = substr($url, strrpos($url, '.'));
        //todo: watermark
        //$url = watermark2($url, '来自PSGOD', '宋体', '1000', 'white');
        //echo $uid.":".$type.":".$target_id.":".$url;exit();

        //$d = Download::has_downloaded($type, $uid, $target_id);
        if($d = Download::has_downloaded($uid, $type, $target_id)){
            $d->url = $url;
            $d->save_and_return($d);
        } else {
            $dl = Download::addNewDownload($uid, $type, $target_id, $url, 0);
            if( $dl instanceof Download ){
                ActionLog::log(ActionLog::TYPE_USER_DOWNLOAD, array(), $dl);
            }
        }

        return $this->output( array(
            'type'=>$type,
            'target_id'=>$target_id,
            'url'=>$url
        ));
    }

    public function get_push_settingsAction(){
        $type = $this->get('type','string','');

        $uid = $this->_uid;
        $settings = UserDevice::get_push_stgs( $uid );

        switch( $type ){
            case UserDevice::PUSH_TYPE_COMMENT:
            case UserDevice::PUSH_TYPE_FOLLOW:
            case UserDevice::PUSH_TYPE_INVITE:
            case UserDevice::PUSH_TYPE_REPLY:
                $ret = array($type=>$settings->$type);
                break;
            default:
                $ret = $settings;
        }

        return $this->output( $ret );
    }

    public function set_push_settingsAction(){
        $this->noview();
        $type = $this->post('type','string');
        $value = $this->post('value','string');

        $uid = $this->_uid;
        if( !in_array($type, array(
            UserDevice::PUSH_TYPE_COMMENT,
            UserDevice::PUSH_TYPE_FOLLOW,
            UserDevice::PUSH_TYPE_INVITE,
            UserDevice::PUSH_TYPE_REPLY,
            UserDevice::PUSH_TYPE_SYSTEM))
        ){
            return $this->output( false, '设置类型错误' );
        }
        if( $value!=UserDevice::VALUE_ON && $value!=UserDevice::VALUE_OFF ){
            return $this->output( false, '设置参数错误' );
        }

        $settings = UserDevice::get_push_stgs( $uid );
        $old = ActionLog::clone_obj( $settings );
        switch( $type ){
            case UserDevice::PUSH_TYPE_COMMENT:
            case UserDevice::PUSH_TYPE_FOLLOW:
            case UserDevice::PUSH_TYPE_INVITE:
            case UserDevice::PUSH_TYPE_REPLY:
            case UserDevice::PUSH_TYPE_SYSTEM:
                $ret = UserDevice::set_push_stgs( $uid, $type, $value );
                ActionLog::log(ActionLog::TYPE_USER_MODIFY_PUSH_SETTING, $old, $ret);
                break;
            default:
                $ret = false;
        }

        return $this->output( (bool)$ret );
    }

    public function delete_progressAction() {
        $type = $this->post("type", "int", Label::TYPE_ASK);
        $id   = $this->post("id", "int");

        if(!$id){
            return $this->output( false, '请选择删除的记录' );
        }

        $uid = $this->_uid;
        $download = Download::findFirst('uid='.$uid.' AND type='.$type.' AND target_id='.$id);
        if(!$download){
            return $this->output( false, '请选择删除的记录' );
        }

        if($download->uid != $this->_uid){
            return $this->output( false, '未下载' );
        }
        $old = ActionLog::clone_obj( $download );

        $download->status = Download::STATUS_DELETED;
        $new = $download->save_and_return($download);
        if( $new instanceof Download ){
            ActionLog::log(ActionLog::TYPE_DELETE_DOWNLOAD, $old, $new);
        }

        return $this->output( true );
    }
}

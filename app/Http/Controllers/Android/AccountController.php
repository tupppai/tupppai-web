<?php
namespace App\Http\Controllers\Android;

use App\Services\User as sUser;

class AccountController extends ControllerBase{

    public function loginAction(){
        $username   = $this->post('username', 'string');
        $phone      = $this->post('phone', 'string');
        $password   = $this->post('password', 'string');

        #todo: remove
        $phone      = "13580504992";
        $password   = "123123";

        if ( (is_null($phone) and is_null($username)) or is_null($password) ) {
            return error('WRONG_ARGUMENTS');
        }

        $user = sUser::loginUser($phone, $username, $password);
        session( 'uid', $user['uid'] );
        //$this->session->set('uid', $user['uid']);

        return $this->output($user);
    }

    public function registerAction(){
        //get platform
        $type     = $this->post('type', 'string');
        //todo: 验证码
        $code     = $this->post('code');
        //post param
        $mobile   = $this->post('mobile', 'string');
        $password = $this->post('password', 'string');
        $nickname = $this->post('nickname', 'string');
        $username = $nickname;
        $avatar   = $this->post('avatar', 'string');
        $location = $this->post('location', 'string','');
        $city     = $this->post('city', 'int');
        $province = $this->post('province', 'int');
        //$location = $this->encode_location($province, $city, $location);

        $sex      = $this->post('sex', 'string');
        $openid   = $this->post('openid','string', $mobile);
        $avatar_url = $this->post('avatar_url', 'string', $avatar);

        if( !$nickname ){
            return error( 'WRONG_ARGUMENTS', '昵称不能为空');
        }
        if(!$mobile) {
            return error( 'WRONG_ARGUMENTS', '请输入手机号码' );
        }
        if(!$password) {
            return error( 'WRONG_ARGUMENTS', '请输入密码' );
        }
        if(!$avatar) {
            return error( 'WRONG_ARGUMENTS', '请上传头像' );
        }

        if( $type != 'mobile' && !$openid ) {
            return error( 'WRONG_ARGUMENTS', '请重新授权！' );
        }
        if( sUser::checkHasRegistered( $type, $openid ) ){
            //turn to login
            return error('USER_EXISTS');
        }

        //register
        $user =sUser::addUser( $type, $username, $password, $nickname, $mobile, $location, $avatar, $sex, $openid );

        return $this->output( $user, '注册成功');
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
            session('code', $active_code);

            return $this->output(array('code'=>$active_code));
        } else {
            return $this->output( '输入的手机号码不符合要求，请确认后重输' );
        }
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


    public function device_tokenAction() {
        $uid      = $this->_uid;

        $name     = $this->post("device_name", 'string');
        $os       = $this->post("device_os", 'string');
        $platform = $this->post('platform','int', 0);
        $mac      = $this->post("device_mac", 'string');
        $token    = $this->post("device_token", 'string');
        $options  = $this->post("options", 'string', '');

        /*
        $name = 'm2';
        $os   = 'android';
        $platform = 0;
        $mac = '123';
        $token = '1234';
         */

        if( empty($mac) )
            return error('EMPTY_DEVICE_MAC');
        if( empty($os) )
            return error('EMPTY_DEVICE_OS');
        if( empty($token) )
            return error('EMPTY_DEVICE_TOKEN');

        $deviceInfo = sDevice::updateDevice( $name, $os, $platform, $mac, $token, $options );
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
}

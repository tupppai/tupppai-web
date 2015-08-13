<?php
namespace App\Http\Controllers\Android;

use Session;
use App\Facades\Sms;
use App\Services\User as sUser;

class AccountController extends ControllerBase{

    public function loginAction(){
        $username   = $this->post('username', 'string');
        $phone      = $this->post('phone', 'string');
        $password   = $this->post('password', 'string');

        #todo: remove
        $phone      = "19000000001";
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


    public function sendMsgAction(){
        $phone = $this->get('phone','mobile',0);
        if( !$phone ){
            return error( 'WRONG_ARGUMENTS', '手机号格式错误' );
        }

        $active_code = mt_rand(100000, 9999999);    // 六位验证码
        $active_code  = '123456';
        session( ['code'=>$active_code] );

        Sms::make([
              'YunPian'    => '1',
              'SubMail'    => '123'
          ])
          ->to($phone)
          ->data(['皮埃斯网络科技', '123456'])
          ->content('【皮埃斯网络科技】您的验证码是123456')
          ->send();

          return $this->output( [ 'code' => $active_code ], '发送成功' );
    }

    public function resetPasswordAction(){
        $phone   = $this->post('phone', 'int');
        $code    = $this->post('code', 'int','------');
        $new_pwd = $this->post('new_pwd');

        if(!$new_pwd) {
            return error( 'WRONG_ARGUMENTS', '密码不能为空' );
        }
        if(!$phone) {
            return error( 'WRONG_ARGUMENTS', '手机号不能为空' );
        }
        if(!$code) {
            return error( 'WRONG_ARGUMENTS', '短信验证码为空' );
        }
        //todo: 验证码有效期(通过session有效期控制？)
        if( $code != Session::pull('code') ){
            return error( 'WRONG_ARGUMENTS', '验证码过期或不正确' );
        }


        $result = sUser::resetPassword( $phone, $new_pwd );

        return $this->output( ['status'=>(bool)$result] );
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

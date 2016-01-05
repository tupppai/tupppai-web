<?php namespace App\Http\Controllers\Api;

use App\Facades\Sms;
use App\Services\User as sUser;
use App\Services\Device as sDevice;
use App\Services\SysMsg as sSysMsg;
use App\Services\UserDevice as sUserDevice;
use App\Services\UserLanding as sUserLanding;

use App\Models\Device as mDevice;
use App\Models\Message as mMessage;

use App\Jobs\Push, App\Jobs\SendSms;
use Session, Log, Queue;

class AccountController extends ControllerBase{

    public $_allow = array(
        'login',
        'register',
        'requestAuthCode',
        'checkAuthCode',
        'resetPassword',
        'hasRegistered',
        'checkTokenValidity',
        'updateToken'
    );

    public function loginAction(){
        $username = $this->post( 'username', 'string' );
        $phone    = $this->post( 'phone'   , 'string' );
        $password = $this->post( 'password', 'string' );

        if ( (is_null($phone) and is_null($username)) or is_null($password) ) {
            return error('WRONG_ARGUMENTS');
        }

        $user = sUser::loginUser( $phone, $username, $password );
        //todo: status remove
        if(!isset($user['uid'])){
            return $this->output($user);
        }
        session( [ 'uid' => $user['uid'] ] );

        return $this->output( $user );
    }

    public function registerAction(){
        //get platform
        $type     = $this->post( 'type', 'string' );
        //todo: 验证验证码
        $code     = $this->post( 'code' );
        //todo: remove if 验证验证码是否正确
        if($code) $this->check_code();
        //post param
        $mobile   = $this->post( 'mobile'   , 'string' );
        $password = $this->post( 'password' , 'string' );
        $nickname = $this->post( 'nickname' , 'string', '' );
        $location = $this->post( 'location' , 'string', '' );
        $city     = $this->post( 'city'     , 'int', '' );
        $province = $this->post( 'province' , 'int', '' );
        $avatar   = $this->post( 'avatar'   , 'string' );

        if(!$nickname && $mobile) {
            $username = $mobile;
        }
        else {
            $username = $nickname;
        }

        $sex      = $this->post( 'sex'   , 'string', '' );
        $openid   = $this->post( 'openid', 'string', $mobile );
        if( $avatar ) {
            $avatar_url = $avatar;
        }
        else {
            $avatar_url = $this->post( 'avatar_url', 'string', 'http://7u2spr.com1.z0.glb.clouddn.com/20150326-1451205513ac68292ea.jpg');
        }
/*
        $data = json_decode('');
        $nickname = $data->nickname;
        $password = $data->password;
        $mobile = $data->mobile;
        $city   = isset($data->city)?$data->city: '';
        $provice    = isset($data->province)?$data->province: '';
        $avatar_url = $data->avatar_url;
        $type = $data->type;
        $openid = $data->openid;
 */
        /*
         * v1.0.5 允许不穿昵称 默认为手机号码
         */
        if( !$nickname ){
            $nickname = $mobile;
            //return error( 'EMPTY_NICKNAME', '昵称不能为空');
        }
        if( !$mobile ) {
            return error( 'EMPTY_MOBILE', '请输入手机号码' );
        }
        if( !$password ) {
            return error( 'EMPTY_PASSWORD', '请输入密码' );
        }
        if( !$avatar_url ) {
            return error( 'EMPTY_AVATAR', '请上传头像' );
        }

        if( sUser::checkHasRegistered( $type, $openid ) ){
            //turn to login
            return error('USER_EXISTS', '用户已存在');
        }
        if( $type != 'mobile' && !$openid ) {
            return error( 'EMPTY_OPENID', '请重新授权！' );
        }
        if( $type == 'mobile' && sUser::checkHasRegistered( 'mobile', $mobile) ){
            //turn to login
            return error('USER_EXISTS', '该手机已经已注册');
        }

        # 非手机注册流程不一样
        $user = sUser::getUserByPhone($mobile);
        if(!$user) {
            //register
            $user = sUser::addUser(
                $type,
                $username,
                $password,
                $nickname,
                $mobile,
                $location,
                $avatar_url,
                $sex,
                $openid
            );
        }

        if($type != 'mobile') {
            $landing = sUserLanding::bindUser($user->uid, $openid, $type);
            $user->password == sUser::hash($password);
            $user->save();
        }

        $user = sUser::loginUser( $mobile, $username, $password, $type );

        Log::info('afterregister', array(
            'user'=>$user,
            'postdata'=>$_POST
        ));

        if(!$user) {
            Log::info('systemerror', array(
                $user, $_REQUEST
            ));
            return error('SYSTEM_ERROR');
        }
        session( [ 'uid' => $user['uid'] ] );

        return $this->output( $user, '注册成功');
    }

    public function checkAuthCodeAction(){

        $this->check_code();
        return $this->output();
    } 

    public function requestAuthCodeAction(){
        $phone = $this->get( 'phone', 'mobile', 0);
        if( !$phone ){
            return error( 'INVALID_PHONE_NUMBER', '手机号格式错误' );
        }
        //用于每次注册用
        if($phone > '19000000000' && $phone < 19999999999) {
            $code = 123456;
            $time = time();

            session( [ 'authCode' => [
                'code'=>$code,
                'time'=>$time,
                'phone'=>$phone
            ]] );
            return $this->output();
        }

        $authCode = session('authCode');
        $time     = time();
        if( $authCode && isset($authCode['time']) && $time - $authCode['time'] < 60) {
            return error('ALREADY_SEND_SMS');
        }

        $code = mt_rand( 1000, 9999 ); 
        session( [ 'authCode' => [
            'code'=>$code,
            'time'=>$time,
            'phone'=>$phone
        ]] );

        Queue::push(new SendSms($phone, $code));

        return $this->output();
    }

    public function resetPasswordAction(){
        $this->check_code();

        $phone   = $this->post( 'phone', 'int' );
        $new_pwd = $this->post( 'new_pwd' );

        if( !$new_pwd ){
            return error( 'EMPTY_PASSWORD', '密码不能为空' );
        }
        if( !$phone   ){
            return error( 'EMPTY_MOBILE', '手机号不能为空' );
        }

        $result = sUser::resetPassword( $phone, $new_pwd );

        return $this->output( [ 'status' => (bool) $result ] );
    }

    public function hasRegisteredAction(){
        $phone = $this->get( 'phone', 'mobile' );
        if( !$phone ){
            return error( 'INVALID_PHONE_NUMBER', '手机号格式错误' );
        }

        $hasRegistered = sUser::checkHasRegistered( 'mobile', $phone );

        return $this->output( [ 'has_registered' => $hasRegistered ] );
    }

    public function checkTokenValidityAction(){
        $token = $this->post( 'token', 'string' );

        if( !$token ){
            return error( 'INVALID_TOKEN' );
        }

        $isValid = $this->check_token();
        return $this->output( [ 'is_valid' => $isValid ] );
    }

    public function updateTokenAction() {
        $uid      = $this->_uid;

        $name     = $this->post( 'device_name' , 'string' );
        $os       = $this->post( 'device_os'   , 'string' );
        $platform = $this->post( 'platform' , 'int', mDevice::TYPE_ANDROID );
        $mac      = $this->post( 'device_mac'  , 'string' );
        $token    = $this->post( 'device_token', 'string' );
        $version  = $this->post( 'version', 'string', 0);
        $options  = array(
            'v'=>$version
        );
/*
        $data = json_decode('{"token":"ec2905802a6e071aa5c18360accd7eb66e71d162","platform":"0","device_name":"m2","device_token":"AqcJcTtRTh3xJ_tePxspSOQU7yl6RcgH-Dzsli0vLbCz","device_os":"5.1","device_mac":"40:c6:2a:18:40:ac","version":"1.0.2"}');
        $name = $data->device_name;
        $os = $data->device_os;
        $platform = $data->platform;
        $mac = $data->device_mac;
        $token = $data->device_token;
        $version = array('v'=>$data->version);
 */

        if( empty( $mac ) ){
            return error( 'EMPTY_DEVICE_MAC' );
        }
        if( empty( $os  ) ){
            return error( 'EMPTY_DEVICE_OS' );
        }
        if( empty( $token ) ){
            return error( 'EMPTY_DEVICE_TOKEN' );
        }

        $deviceInfo = sDevice::updateDevice(
            $name,
            $os,
            $platform,
            $mac,
            $token,
            $options
        );

        if($uid) {
            $userDevice = sUserDevice::bindDevice( $uid, $deviceInfo->id );
            $user = sUser::getUserByUid( $uid );
            $devices = sUserDevice::getUserUsedDevices( $uid );
            //跟产品商量，这里改成每次新设备都需要提醒
            //create_time 和 update_time可能会有一秒的误差
            if( ($userDevice->update_time - $userDevice->create_time) <= 1 ){
                if( count($devices) == 1 ){
                    sSysMsg::postMsg( 0,  '欢迎新用户', mMessage::TYPE_USER, $uid, '', date( 'Y-m-d H:i:s', time()), $uid, mMessage::MSG_TYPE_NOTICE, '' );
                }
                else{
                    Queue::push(new Push([
                        'type' => 'new_to_app',
                        'uid' => $uid
                    ]));
                }
            }

        }

        return $this->output();
    }

    //todo move to library
    private function check_code(){
        $code     = $this->post( 'code' );
        if( !$code ){
            return error( 'EMPTY_VERIFICATION_CODE', '短信验证码为空' );
        }

        $authCode = session('authCode');
        $time     = time();

        if( $authCode && isset($authCode['time']) && $time - $authCode['time'] > 300) {
            return error( 'INVALID_VERIFICATION_CODE', '验证码过期或不正确' );
        }
        if( $code != $authCode['code'] ){
            return error( 'INVALID_VERIFICATION_CODE', '验证码过期或不正确' );
        }

        return true;
    }
}

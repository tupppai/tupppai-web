<?php namespace App\Http\Controllers\Api;

use Session;
use App\Facades\Sms;
use App\Services\User as sUser;
use App\Services\Device as sDevice;
use App\Services\UserDevice as sUserDevice;
use App\Services\UserLanding as sUserLanding;

use App\Models\Device as mDevice;

use Log;

class AccountController extends ControllerBase{

    public $_allow = array(
        'login',
        'register',
        'requestAuthCode',
        'resetPassword',
        'hasRegistered',
        'checkTokenValidity',
        'updateToken'
    );

    public function loginAction(){
        $username = $this->post( 'username', 'string' );
        $phone    = $this->post( 'phone'   , 'string' );
        $password = $this->post( 'password', 'string' );

        #todo: remove
        ///if(env('APP_DEBUG')) {
        ///    $phone      = "19000000001";
        ///    $password   = "123123";
        ///}

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
        //post param
        $mobile   = $this->post( 'mobile'   , 'string' );
        $password = $this->post( 'password' , 'string' );
        $nickname = $this->post( 'nickname' , 'string' );
        $avatar   = $this->post( 'avatar'   , 'string','http://7u2spr.com1.z0.glb.clouddn.com/20150326-1451205513ac68292ea.jpg' );
        $location = $this->post( 'location' , 'string', '' );
        $city     = $this->post( 'city'     , 'int' );
        $province = $this->post( 'province' , 'int' );
        $username = $nickname;
        //$location = $this->encode_location($province, $city, $location);

        $sex      = $this->post( 'sex'   , 'string' );
        $openid   = $this->post( 'openid', 'string', $mobile );
        $avatar_url = $this->post( 'avatar_url', 'string', $avatar );

        /*
        $data = json_decode('{"token":"1a261db3b8bf12d43f1ec36ee1db398e1f23498d","password":"123456","nickname":"一心扑在代码上","mobile":"13510227494","city":"10","avatar":"","avatar_url":"http://tp4.sinaimg.cn/1002533191/50/5699891739/1","province":"12","sex":"0","type":"weibo","openid":"1002533191"}');
        $nickname = $data->nickname;
        $password = $data->password;
        $mobile = $data->mobile;
        $city = $data->city;
        $provice = $data->province;
        $avatar_url = $data->avatar_url;
        $type = $data->type;
        $openid = $data->openid;
         */

        if( !$nickname ){
            return error( 'EMPTY_NICKNAME', '昵称不能为空');
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
        if( $type != 'mobile' && $user ){
            //sUser::updateProfile($user->uid, $nickname, $avatar_url, $sex, $location, $city, $province);
        }
        else {
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

        if($type != 'mobile')
            $landing = sUserLanding::bindUser($user->uid, $openid, $type);
        
        $user = sUser::loginUser( $mobile, $username, $password );
        session( [ 'uid' => $user['uid'] ] );

        return $this->output( $user, '注册成功');
    }

    public function requestAuthCodeAction(){
        $phone = $this->get( 'phone', 'mobile', 0 );
        if( !$phone ){
            return error( 'INVALID_PHONE_NUMBER', '手机号格式错误' );
        }

        $active_code = mt_rand( 100000, 9999999 );    // 六位验证码
        //todo:: remove
        $active_code  = '123456';
        session( [ 'code' => $active_code ] );

        //todo::capsulation
        Sms::make([
              'YunPian'    => '1',
              'SubMail'    => '123'
          ])
          ->to($phone)
          ->data( [ '皮埃斯网络科技', $active_code ] )
          ->content( '【皮埃斯网络科技】您的验证码是'.$active_code )
          ->send();

        return $this->output( [ 'code' => $active_code ], '发送成功' );
    }

    public function resetPasswordAction(){
        $phone   = $this->post( 'phone', 'int' );
        $code    = $this->post( 'code' , 'int', '------' );
        $new_pwd = $this->post( 'new_pwd' );

        if( !$new_pwd ){
            return error( 'EMPTY_PASSWORD', '密码不能为空' );
        }
        if( !$phone   ){
            return error( 'EMPTY_MOBILE', '手机号不能为空' );
        }
        if( !$code    ){
            return error( 'EMPTY_VERIFICATION_CODE', '短信验证码为空' );
        }
        //todo: 验证码有效期(通过session有效期控制？)
        if( $code != Session::pull('code') ){
            return error( 'INVALID_VERIFICATION_CODE', '验证码过期或不正确' );
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
        }

        return $this->output();
    }
}
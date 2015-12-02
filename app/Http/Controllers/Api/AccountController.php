<?php namespace App\Http\Controllers\Api;

use Session;
use App\Facades\Sms;
use App\Services\User as sUser;
use App\Services\Device as sDevice;
use App\Services\SysMsg as sSysMsg;
use App\Services\UserDevice as sUserDevice;
use App\Services\UserLanding as sUserLanding;

use App\Models\Device as mDevice;
use App\Models\Message as mMessage;

use App\Jobs\Push;

use Log, Queue;

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

    public function testAction() {
        Queue::push(new Push([
            'type' => 'new_to_app',
            'uid' => 253
        ]));
    }

    /**
     * @api {post} /account/login 用户登录
     * @apiName user_login
     * @apiGroup User
     *
     * @apiParam {String} [username] 登录时的用户名。
     * @apiParam {Number{13..13}} [phone] 登录时的手机号。
     * @apiParam {String} password 对应账号的密码。
     *
     * @apiSuccess {Number} ret 是否正常执行。正常为1，不正常为0。
     * @apiSuccess {Number} code 错误代码。0为无错。
     * @apiSuccess {String} info 错误信息。当code不为0时，错误信息在这儿。
     * @apiSuccess {Object} data  返回数据。
     * @apiSuccess {Number} data.uid  用户uid。
     * @apiSuccess {String} data.username  用户名（废弃）。
     * @apiSuccess {String} data.nickname  昵称。
     * @apiSuccess {Number} data.sex  性别。0为男，1为女。
     * @apiSuccess {String} data.avatar  头像。
     * @apiSuccess {Number} data.uped_count  获得多少个赞。
     * @apiSuccess {Number} data.current_score  当前积分（暂时不用）。
     * @apiSuccess {Number} data.paid_score  已获积分（暂时不用）。
     * @apiSuccess {Number} data.total_praise  点了多少次别人的赞。
     * @apiSuccess {String} data.location  地区。
     * @apiSuccess {String} data.province  省份。
     * @apiSuccess {String} data.city  城市。
     * @apiSuccess {String} data.bg_image  背景图片（暂时不用）。
     * @apiSuccess {String} data.status  用户状态。0为删除，1为正常。
     * @apiSuccess {Number} data.is_bound_weixin  是否绑定了微信。0为未绑定，1为绑定了。
     * @apiSuccess {Number} data.is_bound_qq  是否绑定了QQ。0为未绑定，1为绑定了。
     * @apiSuccess {Number} data.is_bound_weibo  是否绑定了微博。0为未绑定，1为绑定了。
     * @apiSuccess {String} data.weixin  微信号。
     * @apiSuccess {String} data.weibo  微博号。
     * @apiSuccess {String} data.qq  QQ号。
     * @apiSuccess {Number} data.fans_count  粉丝数量。
     * @apiSuccess {Number} data.fellow_count  关注数量。
     * @apiSuccess {Number} data.ask_count  求助数量。
     * @apiSuccess {Number} data.reply_count  作品数量。
     * @apiSuccess {Number} data.inporgress_count  进行中数量。
     * @apiSuccess {Number} data.collection_count  收藏数量。
     *
     * @apiSuccess {String} token 用户的登录Token。
     * @apiSuccess {Number} debug 是否为调试状态。0为否，1为是。
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *        "ret": 1,
     *        "code": 0,
     *        "info": "",
     *        "data": {
     *          "uid": 1,
     *          "username": "billqiang",
     *          "nickname": "jq",
     *          "phone": "19000000001",
     *          "sex": 0,
     *          "avatar": "http://7u2spr.com1.z0.glb.clouddn.com/20151102-19491756374dbd26b90.ico",
     *          "uped_count": 10,
     *          "current_score": 0,
     *          "paid_score": 0,
     *          "total_praise": 0,
     *          "location": "",
     *          "province": "",
     *          "city": "",
     *          "bg_image": null,
     *          "status": 1,
     *          "is_bound_weixin": 0,
     *          "is_bound_qq": 0,
     *          "is_bound_weibo": 0,
     *          "weixin": "",
     *          "weibo": "",
     *          "qq": "",
     *          "fans_count": 3,
     *          "fellow_count": 6,
     *          "ask_count": 29,
     *          "reply_count": 5,
     *          "inprogress_count": 3,
     *          "collection_count": 0
     *        },
     *        "token": "2f52b17881ecb9e84343d82cba1d62ea7a82a1c4",
     *        "debug": 1
     *      }
     *
     * @apiError 280 没有传参数
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 200 OK
     *     {
     *        "ret": 0,
     *        "code": 280,
     *        "info": "wrong arguments",
     *        "data": [],
     *        "token": "2f52b17881ecb9e84343d82cba1d62ea7a82a1c4",
     *        "debug": 1
     *      }
     */
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
        //todo: 验证码有效期(通过session有效期控制？)
        if( $code != session('code') ){
            return error( 'INVALID_VERIFICATION_CODE', '验证码过期或不正确' );
        }

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
        $code    = $this->post( 'code' , 'int', '------' );

        if( !$code ){
            return error( 'EMPTY_VERIFICATION_CODE', '短信验证码为空' );
        }
        //todo: 验证码有效期(通过session有效期控制？)
        if( $code != session('code') ){
            return error( 'INVALID_VERIFICATION_CODE', '验证码过期或不正确' );
        }

        return $this->output();
    }

    public function requestAuthCodeAction(){
        $phone = $this->get( 'phone', 'mobile', 0 );
        if( !$phone ){
            return error( 'INVALID_PHONE_NUMBER', '手机号格式错误' );
        }
        if( $phone>=17000000000 && $phone<=17999999999 ){
            $phone = 13410152273;
        }

        $active_code = mt_rand( 1000, 9999 );    // 六位验证码
        //todo:: remove
        session( [ 'code' => $active_code ] );
        //todo::capsulation
        Sms::make([
              'YunPian'    => '1115887',
              'SubMail'    => '123'
          ])
          ->to($phone)
          //->data( [ '皮埃斯网络科技', $active_code ] )
          ->data( ['【图派App】您的验证码是'.$active_code.'，一分钟内有效。来把奔跑的灵感关进图派。'])
          ->content( '【图派App】您的验证码是'.$active_code.'，一分钟内有效。来把奔跑的灵感关进图派。')
          //->content( '【图派App】验证码'.$active_code.'，一分钟内有效。把奔跑的灵感关进图派吧！')
          //->content( '【皮埃斯网络科技】您的验证码是'.$active_code )
          ->send();

        return $this->output( [ 'code' => $active_code ], '发送成功' );
        return $this->output();
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
        if( $code != session('code') ){
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
}

<?php
namespace App\Http\Controllers\Main;

use App\Services\User as sUser;
use App\Services\Download as sDownload;
use App\Services\Ask as sAsk;
use App\Services\Follow as sFollow;
use App\Services\Count as sCount;
use App\Services\Message as sMessage;
use App\Services\Reply as sReply;
use App\Services\UserLanding as sUserLanding;

use App\Counters\UserCounts as cUserCounts;

use App\Jobs\Push, App\Jobs\SendSms;

use Session, Queue;

class UserController extends ControllerBase {
    public $_allow = array('*');

    public function status() {
        $this->isLogin();

        $uid  = $this->_uid;
        $user = sUser::getUserByUid($uid);
        $user = sUser::detail($user);

        return $this->output($user);
    }

    public function code(){
        $phone = $this->get( 'phone', 'mobile', 0);
        if( !$phone ){
            return error( 'INVALID_PHONE_NUMBER', '手机号格式错误' );
        }
        //用于每次注册用
        if($phone > '19000000000' && $phone < 19999999999) {
            session( [ 'code' => '123456' ] );
            return $this->output( [ 'code' => '123456' ], '发送成功' );
        }

        $authCode = session('authCode');
        $time     = time();
        if( $authCode && isset($authCode['time']) && $time - $authCode['time'] < 60) {
            return error('ALREADY_SEND_SMS');
        }

        $code = mt_rand( 1000, 9999 );    // 六位验证码
        session( [ 'authCode' => [
            'code'=>$code,
            'time'=>$time,
            'phone'=>$phone
        ]] );

        Queue::push(new SendSms($phone, $code));

        return $this->output();
    }

    public function auth() {
        $openid = $this->get('openid', 'string');
        $type   = $this->get('type', 'string');
        $hasRegistered = false;
        if(!$openid) {
            return error('WRONG_ARGUMENTS', '登录失败');
        }

        $user = sUserLanding::loginUser( $type, $openid );
        if( $user ){
            session(['uid' => $user['uid']]);
            $hasRegistered = true;
        }

        return $this->output(array(
            'user_obj'=>$user,
            'is_register'=> (int)$hasRegistered
        ));
    }

    public function login() {
        $username = $this->get( 'username', 'string' );
        $phone    = $this->get( 'phone'   , 'string' );
        $password = $this->get( 'password', 'string' );

        if ( (is_null($phone) and is_null($username)) or is_null($password) ) {
            return error('WRONG_ARGUMENTS', '参数错误');
        }
        if(match_phone_format($username)){
            $phone = $username;
        }

        $user = sUser::loginUser( $phone, $username, $password );
        if( $user['status'] == 3 ){
            return error('USER_NOT_EXIST', '用户不存在');
        }
        else if( $user['status'] == 2){
            return error('PASSWORD_NOT_MATCH', '账号或者密码不对');
        }
        session( [ 'uid' => $user['uid'] ] );

        return $this->output( $user );
    }

    public function logout() {
        Session::flush();

        return $this->output();
    }

    public function view($uid) {
        $user = sUser::getUserByUid($uid);
        if(!$user) {
            return error('USER_NOT_EXIST', '用户不存在');
        }
        $user = sUser::detail($user);
        $user = sUser::addRelation( $this->_uid, $user );

        return $this->output($user);
    }

    public function message() {
        $this->isLogin();
        $uid = $this->_uid;
        $page = $this->get('page', 'integer', 1);
        $size = $this->get('size', 'integer', 15);
        $type = $this->get('type', 'string', 'normal');

        $msgs = sMessage::getMessages( $uid, $type, $page, $size );
        cUserCounts::reset($uid, 'badges');

        return $this->output( $msgs );
    }

    public function follow(){
        $this->isLogin();

        $friendUid = $this->post( 'uid', 'integer' );
        $status = $this->post( 'status', 'integer', 1 );
        if( !$friendUid ){
            return error( 'WRONG_ARGUMENTS', '请选择关注的账号' );
        }

        $followResult = sFollow::follow( $this->_uid, $friendUid, $status );
        return $this->output( $followResult );
    }

    /**
     * 用户注册接口
     */
    public function register(){
        $this->check_code();

        //get platform
        $type     = $this->post( 'type', 'string' );
        //post param
        $mobile   = $this->post( 'mobile'   , 'string' );
        $password = $this->post( 'password' , 'string' );
        $nickname = $this->post( 'nickname' , 'string' );
        $avatar   = $this->post( 'avatar'   , 'string','http://7u2spr.com1.z0.glb.clouddn.com/20151118-205001564c73f9ca9be.png' );
        $location = $this->post( 'location' , 'string', '' );
        $city     = $this->post( 'city'     , 'int' );
        $province = $this->post( 'province' , 'int' );
        $username = $nickname;
        //$location = $this->encode_location($province, $city, $location);

        $sex      = $this->post( 'sex'   , 'string' );
        $openid   = $this->post( 'openid', 'string', $mobile );
        $avatar_url = $this->post( 'avatar_url', 'string', $avatar );

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

        if( $type != 'mobile' && !$openid ) {
            return error( 'EMPTY_OPENID', '请重新授权！' );
        }
        if( sUser::checkHasRegistered( $type, $openid ) ){
            //turn to login
            return error('USER_EXISTS', '用户已存在');
        }
        if( sUser::checkHasRegistered( 'mobile', $mobile) ){
            //turn to login
            return error('USER_EXISTS', '该手机已经已注册');
        }

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
        $user = sUser::loginUser( $mobile, $username, $password );
        session( [ 'uid' => $user['uid'] ] );

        return $this->output( $user, '注册成功');
    }

    public function save(){
        $this->isLogin();
        $uid = $this->_uid;

        $nickname = $this->post( 'nickname', 'string' );
        $avatar   = $this->post( 'avatar'  , 'string' );
        $sex      = $this->post( 'sex'     , 'integer');
        $location = $this->post( 'location', 'string' );
        $city     = $this->post( 'city'    , 'string' );
        $province = $this->post( 'province', 'string' );

        $data = array( $uid, $nickname, $avatar, $sex, $location, $city, $province );
        if( count(array_filter( $data )) == 0 ){
            $ret = false;//Nothing changed.
        }
        else{
            $ret = sUser::updateProfile(
                $uid,
                $nickname,
                $avatar,
                $sex,
                $location,
                $city,
                $province
            );
        }

        return $this->output( ['result'=>(int)$ret] );
    }

    public function updatePassword(){
        $this->isLogin();
        $uid = $this->_uid;
        $oldPassword = $this->post( 'old_pwd', 'string' );
        $newPassword = $this->post( 'new_pwd', 'string' );

        if( empty( $oldPassword ) ){
            return error( 'OLD_PASSWORD_EMPTY', '原密码不能为空' );
            //return $this->output(0 , '原密码不能为空');
        }
        if( empty( $newPassword ) ){
            return error( 'NEW_PASSWORD_EMPTY', '新密码不能为空' );
            //return $this->output(0, '新密码不能为空');
        }
        if( $oldPassword == $newPassword ) {
            #todo: 不能偷懒，俺们要做多语言的  ←重点不是多语言，而是配置化提示语。方便后台人员直接修改。
            return error( 'WRONG_ARGUMENTS', '新密码不能与原密码相同' );
            //return $this->output(3, '新密码不能与原密码相同');
        }

        $ret = sUser::updatePassword( $uid, $oldPassword, $newPassword );
        if( $ret == 2 ){
            return error( 'WRONG_ARGUMENTS', '原密码错误' );
        }

        return $this->output( $ret );
    }


    public function forget(){
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


    public function fans() {
        $uid    = $this->get( 'uid', 'integer', $this->_uid );
        $page   = $this->get( 'page', 'int', 1 );
        $size   = $this->get( 'size', 'int', 17 );
        $lpd    = $this->get( 'last_updated', 'integer', time());
        if( !$uid ){
            $this->isLogin();
        }

        $fansList = sUser::getFans( $this->_uid, $uid, $page, $size );

        return $this->output( $fansList );
    }

    public function follows() {
        $uid    = $this->get( 'uid', 'integer', $this->_uid );
        $page   = $this->get( 'page', 'int', 1 );
        $size   = $this->get( 'size', 'int', 15 );
        $ask_id = $this->get( 'ask_id', 'interger');
        if( !$uid ){
            $this->isLogin();
        }

        $friendsList = sUser::getFriends( $this->_uid, $uid, $page, $size, $ask_id );

        return $this->output( $friendsList );
    }

    public function uped(){
        $uid  = $this->get( 'uid', 'int', $this->_uid );
        $page = $this->get( 'page', 'int', 1  );
        $size = $this->get( 'size', 'int', 15 );
        if( !$uid ){
            $this->isLogin();
        }

        $uped = sCount::getUpedCountsByUid( $uid, $page, $size );

        return $this->output( $uped );
    }

    public function collections(){
        $uid = $this->get('uid', 'int', $this->_uid);

        $page         = $this->get('page', 'int', 1);    // 页码
        $size         = $this->get('size', 'int', 15);   // 每页显示数量
        $width        = $this->get('width', 'int', 480);
        $last_updated = $this->post('last_updated', 'int', time());
        if( !$uid ){
            $this->isLogin();
        }

        // 我的收藏
        $collected_items  = sReply::getCollectionReplies($uid, $page, $size);

        return $this->output( $collected_items );
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
?>

<?php 
namespace App\Http\Controllers\Main; 

use App\Services\User as sUser;
use App\Services\Download as sDownload;
use App\Services\Ask as sAsk;
use App\Services\Follow as sFollow;
use App\Services\Message as sMessage;
use App\Services\Reply as sReply;
use Session;

class UserController extends ControllerBase {
    public $_allow = array('*');

    public function status() {
        $this->isLogin();

        $uid  = $this->_uid;
        $user = sUser::getUserByUid($uid);
        $user = sUser::detail($user);

        return $this->output($user);
    }
    
    public function login() {
        $username = $this->get( 'username', 'string' );
        $phone    = $this->get( 'phone'   , 'string' );
        $password = $this->get( 'password', 'string' );

        if ( (is_null($phone) and is_null($username)) or is_null($password) ) {
            return error('WRONG_ARGUMENTS');
        }
        if(match_phone_format($username)){
            $phone = $username;
        }

        $user = sUser::loginUser( $phone, $username, $password );
        session( [ 'uid' => $user['uid'] ] );

        return $this->output( $user );
    }
 
    public function logout() {
        Session::flush();

        return $this->output();
    }

    public function view($uid) {
        $user = sUser::getUserByUid($uid);
        $user = sUser::detail($user);
        $user = sUser::addRelation( $this->_uid, $user );

        return $this->output($user);
    }
    
    public function message() {
        $uid = $this->_uid;
        $page = $this->get('page', 'integer', 1);
        $size = $this->get('size', 'integer', 15);
        $type = $this->get('type', 'string', 'normal');

        $msgs = sMessage::getMessages( $uid, $type, $page, $size );

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
        //get platform
        $type     = $this->post( 'type', 'string' );
        //todo: 验证码
        $code     = $this->post( 'code' );
        //post param
        $mobile   = $this->post( 'mobile'   , 'string' );
        $password = $this->post( 'password' , 'string' );
        $nickname = $this->post( 'nickname' , 'string' );
        $avatar   = $this->post( 'avatar'   , 'string','http://7u2spr.com1.z0.glb.clouddn.com/20151028-0115065630219abd8f1.jpg' );
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

    public function fans() {
        $uid    = $this->get( 'uid', 'integer', $this->_uid );
        $page   = $this->get( 'page', 'int', 1 );
        $size   = $this->get( 'size', 'int', 17 );
        $lpd    = $this->get( 'last_updated', 'integer', time());

        $fansList = sUser::getFans( $this->_uid, $uid, $page, $size );

        return $this->output( $fansList );
    }

    public function follows() {
        $uid    = $this->get( 'uid', 'integer', $this->_uid );
        $page   = $this->get( 'page', 'int', 1 );
        $size   = $this->get( 'size', 'int', 15 );
        $ask_id = $this->get( 'ask_id', 'interger');

        $friendsList = sUser::getFriends( $this->_uid, $uid, $page, $size, $ask_id );

        return $this->output( $friendsList );
    }
}
?>

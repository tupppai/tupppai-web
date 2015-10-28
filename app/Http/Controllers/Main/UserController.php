<?php 
namespace App\Http\Controllers\Main; 

use App\Services\User as sUser;
use App\Services\Download as sDownload;
use App\Services\Ask as sAsk;
use App\Services\Follow as sFollow;
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

    public function follow(){
        $friendUid = $this->post( 'uid', 'integer' );
        $status = $this->post( 'status', 'integer', 1 );
        if( !$friendUid ){
            return error( 'WRONG_ARGUMENTS', '请选择关注的账号' );
        }

        $followResult = sFollow::follow( $this->_uid, $friendUid, $status );
        return $this->output( $followResult );
    }


    /**
     * 用户个人页面
     * @params $uid
     * @author brandwang
     */   
    public function homeAction($uid) {

        $user = sUser::getUserByUid($uid);
        $user = sUser::detail($user);

        return $this->output(array(
            'user'=>$user
        ));
    } 

    /**
     * 排行榜页面
     *
     * @author brandwang
     */
    public function rankingAction() {

        return $this->output();
    }
    
    /**
     * 用户登出接口
     * @author brandwang
     */
    public function logoutAction() {
        Session::flush();
        //TODO 登出操作

        //redirect
        return redirect('/index/index');
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
}
?>

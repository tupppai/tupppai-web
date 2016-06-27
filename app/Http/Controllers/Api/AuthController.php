<?php namespace App\Http\Controllers\Api;

use Session;
use App\Services\UserLanding as sUserLanding;
use App\Services\User as sUser;

use App\Models\User as mUser,
    App\Models\Collection as mCollection,
    App\Models\UserLanding as mUserLanding,
    App\Models\Usermeta as mUsermeta,
    App\Models\Reply as mReply,
    App\Models\Ask as mAsk,
    App\Models\Download as mDownload,
    App\Models\Follow as mFollow,
    App\Models\Comment as mComment,
    App\Models\Label as mLabel,
    App\Models\Master as mMaster,
    App\Models\Invitation as mInvitation,
    App\Models\Device as mDevice,
    App\Models\UserDevice as mUserDevice,
    App\Models\Focus as mFocus;

use App\Facades\EasyWeChat;

class AuthController extends ControllerBase {

    public $_allow = array(
        'bind',
        'weixin',
        'weibo',
        'qq',
        'login'
    );

    public function bindAction() {
        $openid = $this->post('openid', 'string');
        $type   = $this->post('type', 'string');

        $uid = $this->_uid;
        $user = sUser::getUserByUid( $uid );
        $landing = sUserLanding::bindUser($uid, $openid, $user->nickname, $type);

        return $this->output(true);
    }

    public function unbindAction() {
        $type   = $this->post('type', 'string', 'weibo');

        if(!$type) {
            return error('WRONG_ARGUMNETS', '请选择绑定类型');
        }

        $uid = $this->_uid;
        $landing = sUserLanding::unbindUser($uid, $type);

        return $this->output(true);
    }

    /**
     * 前期不需要手机直接登录
     */
    private function auth($type, $openid) {
        $user_landing = sUserLanding::getUserByOpenid($openid, $type);
        if($user_landing && sUser::getUserByUid($user_landing->uid)) {
            return true;
        }
        $avatar   = $this->post( 'avatar'   , 'string' );
        if(!$avatar) {
            return false;
        }

        $mobile   = $this->post( 'mobile'   , 'string', '');
        $password = $this->post( 'password' , 'string', '' );
        /*v1.0.5 允许不传昵称 默认为手机号码_随机字符串*/
        $nickname = $this->post( 'nickname' , 'string', '用户_'.hash('crc32b',$mobile.mt_rand()) ); 
        $location = $this->post( 'location' , 'string', '' );
        $city     = $this->post( 'city'     , 'int', '' );
        $province = $this->post( 'province' , 'int', '' );
        $username = $nickname;
        $sex      = $this->post( 'sex'   , 'string', '' );

        $user = sUser::addUser(
            $type,
            $username,
            $password,
            $nickname,
            $mobile,
            $location,
            $avatar,
            $sex,
            $openid
        );
        $landing = sUserLanding::bindUser($user->uid, $openid, $nickname ,$type);
        return true;
    }

    public function weixinAction(){
        $openid = $this->post('openid', 'string');
        $type   = mUserLanding::TYPE_WEIXIN;
        $hasRegistered = false;
        
        if(!$openid) {
            return error('WRONG_ARGUMENTS', '登录失败');
        }
        // 三方登录暂时不需要绑定手机
        $this->auth($type, $openid);

        $user = sUserLanding::loginUser( $type, $openid );
        $type_key = sUserLanding::getLandingType( $type );
        if( $user ){
            session(['uid' => $user['uid']]);
            $hasRegistered = true;
        }
        else if( in_array($type_key, [mUserLanding::TYPE_WEIXIN, mUserLanding::TYPE_WEIXIN_MP]) ){
            $app = EasyWeChat::getFacadeRoot();
            try{
                $userinfo = $app->user->get( $openid );
            }catch(\Exception $e ){
                // invalid openid hint
            }
            if( $userinfo ){
                $user_landing = sUserLanding::getUserLandingByUnionId( $userinfo->unionid, $type );
                if( $user_landing ){
                    $user = sUserLanding::loginUser( $user_landing->type, $user_landing->openid );
                }
            }
        }

        return $this->output(array(
            'user_obj'=>$user,
            'is_register'=> (int)$hasRegistered
        ));
    }

    public function weiboAction(){
        $openid = $this->post('openid', 'string');
        $type   = 'weibo';
        $hasRegistered = false;

        if(!$openid) {
            return error('WRONG_ARGUMENTS', '登录失败');
        }
        // 三方登录暂时不需要绑定手机
        $this->auth($type, $openid);
        
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

    public function qqAction(){
        $openid = $this->post('openid', 'string');
        $type   = 'qq';
        $hasRegistered = false;

        if(!$openid) {
            return error('WRONG_ARGUMENTS', '登录失败');
        }
        // 三方登录暂时不需要绑定手机
        $this->auth($type, $openid);
        
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
}

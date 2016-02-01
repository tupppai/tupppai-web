<?php namespace App\Http\Controllers\Api;

use Session;
use App\Services\UserLanding as sUserLanding;

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

class AuthController extends ControllerBase {

    public $_allow = array(
        'bind',
        'weixin',
        'weibo',
        'qq',
        'login'
    );

    public function http_get($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        ob_start();
        curl_exec($ch);
        $result = ob_get_contents() ;
        ob_end_clean();

        return $result;
    }

    public function bindAction() {
        $openid = $this->post('openid', 'string', "2692601623");
        $type   = $this->post('type', 'string', 'weibo');

        $uid = $this->_uid;
        $landing = sUserLanding::bindUser($uid, $openid, $type);

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

    public function weixinAction(){
        $openid = $this->post('openid', 'string');
        $type   = mUserLanding::TYPE_WEIXIN;
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

    public function weiboAction(){
        $openid = $this->post('openid', 'string');
        $type   = 'weibo';
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

    public function qqAction(){
        $openid = $this->post('openid', 'string');
        $type   = 'qq';
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
}

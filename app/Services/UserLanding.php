<?php
namespace App\Services;

use \App\Models\UserLanding as mUserLanding;

use \App\Services\User as sUser;
use \App\Services\ActionLog as sActionLog;

class UserLanding extends ServiceBase
{
    /**
     * 通过字符串确定平台类型
     */
    public static function getLandingType($type){
        if(is_numeric($type)){
            return $type;
        }
        $type_int = mUserLanding::TYPE_WEIXIN;
        $types = array(
            'weixin' => mUserLanding::TYPE_WEIXIN,
            'weibo'  => mUserLanding::TYPE_WEIBO,
            'qq'     => mUserLanding::TYPE_QQ
        );
        if( isset($types[$type]) ){
            $type_int = $types[$type];
        }
        return $type_int;
    }

    public static function loginUser( $type, $openid ){
        sActionLog::init( 'LOGIN' );

        $type = self::getLandingType($type);

        $userlanding = (new mUserLanding)->find_user_id_by_openid( $type, $openid );
        if( !$userlanding ){
            return false;
        }

        $user = sUser::getUserByUid( $userlanding->uid );
        if( !$user ){
            return error('USER_NOT_EXIST');
        }

        sActionLog::save( $user );
        return sUser::detail($user);

    }

    public static function findWeixinUser( $openid ){
        $user = mUserLanding::find_user_by_openid( $openid );
    
        return $user;
    }

    /**
     * 绑定用户
     */
    public static function addNewUserLanding($uid, $openid, $type = mUserLanding::TYPE_WEIXIN) {
        $landing = new mUserLanding;
        sActionLog::init( 'BIND_ACCOUNT' );
        $landing->assign(array(
            'uid'=>$uid,
            'openid'=>$openid,
            'type'=>self::getLandingType($type)
        ));

        $l = $landing->save();
        sActionLog::save( $l );
        #todo: action log

        return $landing;
    }

    /**
     * 添加微信用户
     *
     * @param string  $openid  openid
     * @param integer $type    平台类型
     * @param integer $phone   手机号码
     * @param string  $password 密码
     * @param integer $location 城市代码
     * @param string  $nick     昵称
     * @param string  $avatar   性别
     * @param integer $sex      头像
     * @param string  $auth     微信用户信息
     */
    public static function addAuthUser($openid, $type = mUserLanding::TYPE_WEIXIN, $phone, $password = '', $location, $nick, $avatar, $sex, $auth = array())
    {
        $user    = sUser::addNewUser('', $password, $nick, $phone, $location, '', $avatar, $sex, $auth);
        $landing = self::addNewUserLanding($user->uid, $openid, $type);

        return $user;
    }


    //deprecated?
    public static function updateAuthUser($uid, $openid, $type = mUserLanding::TYPE_WEIXIN, $phone, $password = '', $location, $nick, $avatar, $sex, $auth = array())
    {
        $user = sUser::getUserByUid($uid);
        $user->assign(array(
            'phone'=>$phone,
            'nickname'=>$nick,
            'avatar'=>$avatar,
            'location'=>$location,
            'sex'=>$sex
        ));
        $user->save();




        if ($user) {
            $uid            = $user->uid;

            $o = new mUserLanding();
            $o->uid         = $uid;
            $o->openid      = $openid;
            $o->type        = $type;
            $o->status      = mUserLanding::STATUS_NORMAL;

            $o->save_and_return($o, true);
            return $user;
        } else {
            return false;
        }
    }

    /**
     * 根据uid查找用户
     *
     * @param  integer $uid    用户uid
     * @param  integer $type   平台类型type
     * @return \App\Models\User
     */
    public static function findUserByUid($uid, $type = mUserLanding::TYPE_WEIXIN)
    {
        $type = self::getLandingType($type);
        $user_landing = mUserLanding::findFirst("uid='{$uid}' and type='{$type}'");

        return $user_landing;
    }

    /**
     * 根据openid查找用户
     *
     * @param  integer $openid 用户openid
     * @param  integer $type   平台类型type
     * @return \App\Models\User
     */
    public static function getUserByOpenid($openid, $type = mUserLanding::TYPE_WEIXIN)
    {
        $type = self::getLandingType($type);
        $user_landing = mUserLanding::where('openid',$openid)->where('type',$type)->first();

        return $user_landing;
    }

    public static function getUserLandings($uid, &$data) {
        $data['is_bound_weixin']  = 0;
        $data['is_bound_qq']      = 0;
        $data['is_bound_weibo']   = 0;

        $landings = mUserLanding::where("uid", "=", $uid);
        foreach($landings as $landing){
            switch($landing->type){
            case mUserLanding::TYPE_WEIXIN:
                $data['is_bound_weixin']  = 1;
                break;
            case mUserLanding::TYPE_WEIBO:
                $data['is_bound_weibo']   = 1;
                break;
            case mUserLanding::TYPE_QQ:
                $data['is_bound_qq']      = 1;
                break;
            }
        }
        return $data;
    }
}

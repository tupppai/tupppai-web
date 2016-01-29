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

    public static function bindUser($uid, $openid, $type = mUserLanding::TYPE_WEIXIN) {
        $type    = self::getLandingType($type);
        // $landing = mUserLanding::where('openid',$openid)
        //     ->where('type',$type)
        //     //->where('status',mUserLanding::STATUS_NORMAL)
        //     ->first();


        // if($landing && $landing->status && $uid != $landing->uid){
        //     return error('USER_EXISTS', '该账号已被绑定');
        // }
        // else if($landing && $landing->status != mUserLanding::STATUS_NORMAL) {
        //     $landing->status = mUserLanding::STATUS_NORMAL;
        //     $landing->save();
        //     return $landing;
        // }
        // else if(!$landing) {
        //     return self::addNewUserLanding($uid, $openid, $type);
        // }

        //防止一个帐号绑定多个微信号，或者多个微博号
        if( self::userHasBoundPlatform( $uid, $type, $openid ) ){
            return error('ALREADY_BOUND_PLATFORM', '您已绑定过该平台');
        }

        //该第三方帐号是否被人使用过
        $landing = self::getPreviousLanding( $uid, $type, $openid );
        if( !$landing ){ //没被人使用，则创建新记录
            return self::addNewUserLanding($uid, $openid, $type);
        }

        //被使用过，但不是自己的
        if( $landing->uid != $uid ){
            return error('USER_EXISTS', '该账号已被绑定');
        }

        //使用过该第三方帐号，不管正在生效或没生效，重新设置其生效状态
        $landing->status = mUserLanding::STATUS_NORMAL;
        $landing->save();

        return $landing;
    }

    //has user bounded this platform before?
    public static function userHasBoundPlatform( $uid, $type, $openid ){
        return mUserLanding::where('type', $type)
                    ->where('uid', $uid )
                    ->where('openid', '!=', $openid)
                    ->where('status', mUserLanding::STATUS_NORMAL )
                    ->exists();
    }

    //get previous binding record
    public static function getPreviousLanding( $uid, $type, $openid ){
        return mUserLanding::where('openid', $openid)
                    ->where('type', $type)
                    ->where('uid', $uid )
                    ->first();
    }

    public static function unbindUser($uid, $type = mUserLanding::TYPE_WEIXIN) {
        $landing = self::getUserLandingByUid($uid, $type);
        if(!$landing) {
            return error('BIND_NOT_EXIST');
        }
        if($landing->status == mUserLanding::STATUS_DELETED){
            return error('BIND_NOT_EXIST');
        }
        $landing->status = mUserLanding::STATUS_DELETED;
        //todo: action log
        $landing->save();
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

        $landing->save();
        sActionLog::save( $landing );

        return $landing;
    }

    /**
     * 根据uid查找用户
     *
     * @param  integer $uid    用户uid
     * @param  integer $type   平台类型type
     * @return \App\Models\User
     */
    public static function getUserLandingByUid($uid, $type = mUserLanding::TYPE_WEIXIN)
    {
        $type = self::getLandingType($type);
        return mUserLanding::where('uid', $uid)
            ->where('status', '>', 0)
            ->where('type', $type)
            ->first();
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
        $user_landing = mUserLanding::where('openid',$openid)->where('type',$type)->valid()->first();

        return $user_landing;
    }

    public static function getUserLandings($uid, &$data) {
        $data['is_bound_weixin']  = 0;
        $data['is_bound_qq']      = 0;
        $data['is_bound_weibo']   = 0;

        $data['weixin'] = '';
        $data['weibo']  = '';
        $data['qq']     = '';

        $landings = mUserLanding::where("uid", "=", $uid)->valid()->get();
        foreach($landings as $landing){
            switch($landing->type){
            case mUserLanding::TYPE_WEIXIN:
                $data['is_bound_weixin']  = 1;
                $data['weixin'] = $landing->openid;
                break;
            case mUserLanding::TYPE_WEIBO:
                $data['is_bound_weibo']   = 1;
                $data['weibo'] = $landing->openid;
                break;
            case mUserLanding::TYPE_QQ:
                $data['is_bound_qq']      = 1;
                $data['qq'] = $landing->openid;
                break;
            }
        }
        return $data;
    }
}

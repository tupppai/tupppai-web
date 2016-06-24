<?php
namespace App\Models;

class UserLanding extends ModelBase
{
    protected $tables='user_landings';


    public function initialize()
    {
        $this->useDynamicUpdate(true);
    }

    public function get_user_landing_by_uid( $uid, $type ){
        return $this->where('uid', $uid)
            ->valid()
            ->where('type', $type)
            ->first();
    }

    public function get_user_landing_by_openid( $openid, $type ){
        return $this->where( 'openid', $openid )
                    ->where( 'type', $type )
                    ->valid()
                    ->first();
    }

    public function check_user_has_bound_platform( $uid, $type, $openid ){
        return $this->where('type', $type)
                    ->where('uid', $uid )
                    ->where('openid', '!=', $openid)
                    ->valid()
                    ->exists();
    }

    public function get_user_previous_landing( $uid, $type, $openid ){
        return $this->where('openid', $openid)
                    ->where('type', $type)
                    ->where('uid', $uid )
                    ->first();
    }

    public function getUserLandingByUnionId( $unionid ){
        return $this->where('unionid', $unionid)
                    ->valid()
                    ->first();
    }
    //public static function addNewUser($username, $password, $nickname, $phone, $location='', $email='', $avatar='', $sex = self::SEX_MAN, $options=array())
    //public static function setUserLanding($uid, $openid, $type = self::TYPE_WEIXIN, $status = self::STATUS_NORMAL) {
    //public static function addAuthUser($openid, $type = self::TYPE_WEIXIN, $phone, $password = '', $location, $nick, $avatar, $sex, $auth = array())
    //public static function updateAuthUser($user, $openid, $type = self::TYPE_WEIXIN, $phone, $password = '', $location, $nick, $avatar, $sex, $auth = array())
    //public static function findUserByUid($uid, $type = self::TYPE_WEIXIN)
    //public static function findUserByOpenid($openid, $type = self::TYPE_WEIXIN)
    //private static function get_landing_type($type){
}

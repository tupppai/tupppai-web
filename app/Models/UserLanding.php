<?php
namespace App\Models;

class UserLanding extends ModelBase
{
    protected $tables='user_landings';


    public function initialize()
    {
        $this->useDynamicUpdate(true);
    }

    public function find_user_id_by_openid( $type, $openid ){
        return $this->where([
            'type' => $type,
            'openid' => $openid
        ])
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

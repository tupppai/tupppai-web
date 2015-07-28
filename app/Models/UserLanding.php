<?php
namespace Psgod\Models;
use Phalcon\Mvc\Model\Resultset\Simple as Resultset;

class UserLanding extends ModelBase
{
    const TYPE_WEIXIN = 1;
    const TYPE_WEIBO  = 2;
    const TYPE_QQ     = 3;

    public function initialize()
    {
        $this->useDynamicUpdate(true);
    }

    public function getSource()
    {
        return 'user_landings';
    }
    
    /**
     * 更新时间
     */
    public function beforeSave() {
        $this->update_time  = time();

        return $this;
    }

    /**
     * 设置默认值
     */
    public function beforeCreate () {
        $this->create_time  = time();
        $this->status       = self::STATUS_NORMAL;

        return $this;
    }

    //public static function addNewUser($username, $password, $nickname, $phone, $location='', $email='', $avatar='', $sex = self::SEX_MAN, $options=array())
    //public static function setUserLanding($uid, $openid, $type = self::TYPE_WEIXIN, $status = self::STATUS_NORMAL) {
    //public static function addAuthUser($openid, $type = self::TYPE_WEIXIN, $phone, $password = '', $location, $nick, $avatar, $sex, $auth = array())
    //public static function updateAuthUser($user, $openid, $type = self::TYPE_WEIXIN, $phone, $password = '', $location, $nick, $avatar, $sex, $auth = array())
    //public static function findUserByUid($uid, $type = self::TYPE_WEIXIN)
    //public static function findUserByOpenid($openid, $type = self::TYPE_WEIXIN)
    //private static function get_landing_type($type){
}

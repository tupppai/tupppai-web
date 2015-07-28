<?php

namespace Psgod\Models;
use Phalcon\Mvc\Model\Behavior\SoftDelete;

class UserDevice extends ModelBase
{
    const VALUE_OFF  = '0';
    const VALUE_ON   = '1';

    public function getSource()
    {
        return 'users_use_devices';
    }

    public function initialize(){
        parent::initialize();

    }

    /**
     * 更新时间
     */
    public function beforeSave() {
        $this->update_time  = time();
    }

    /**
     * 设置默认值
     */
    public function beforeCreate () {
        $this->create_time  = time();
        $this->status       = self::STATUS_NORMAL;

        return $this;
    }

    /**
     * 更新操作时间
     */
    public function refresh_update_time(){
        $this->update_time = time();
        return $this->save();
    }

    /**
     * 获取正在使用的设备信息
     */
    public function get_using_device ( $uid, $device_id ){
        return self::findFirst(
            "uid=$uid".
            " AND device_id='$device_id'".
            " AND status=".self::STATUS_NORMAL
        );
    }

    public function get_last_used_device( $uid ){
        return self::findFirst(array(
            "uid=$uid AND status=".self::STATUS_NORMAL, 
            "order" => "update_time DESC"
        ));
    }

    public function get_devices_by_device_id( $device_id ){
        //被其他人用过 需设置成删除状态
        return self::find(
            "device_id=$device_id ".
            " AND status=".self::STATUS_NORMAL
        ); 
    }
    
    const PUSH_TYPE_COMMENT = 'comment';
    const PUSH_TYPE_FOLLOW  = 'follow';
    const PUSH_TYPE_INVITE  = 'invite';
    const PUSH_TYPE_REPLY   = 'reply';
    const PUSH_TYPE_SYSTEM  = 'system';

    /**
     * 默认的设置
     */
    public function get_default_settings() {
        $settings = array( 
            'comment'=> true, 
            'follow' => true, 
            'invite' => true, 
            'reply'  => true,
            'system' => true
        );

        return $settings;
    }

    //public static function newToken( $uid, $device_id, $settings = array() ){
    //public static function getUsersDeviceToken($uids){
    //public static function get_push_stgs( $uid ){
    //public static function set_push_stgs( $uid , $type, $value ){
}

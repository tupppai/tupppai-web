<?php namespace App\Services;

use App\Services\ActionLog as sActionLog;
use App\Models\UserDevice as mUserDevice,
    App\Models\Device as mDevice;

class UserDevice extends ServiceBase
{

    public static function getPushSettingByType($uid, $type) {
        $settings = self::get_push_settings($uid);

        if(is_array($settings) && isset($settings[$type])) {
            return $settings[$type];
        }
        if(is_object($settings) && isset($settings->$type)) {
            return $settings->$type;
        }

        return false;
    }

    public static function get_push_settings( $uid ){
        $settings = array();
        $mUserDevice = new mUserDevice();

        $settings = $mUserDevice->get_settings( $uid );
        $default  = $mUserDevice->get_default_settings();
        if(!$settings) {
            return $default;
        }
        else {
            /*
            foreach($settings->settings as $key=>$val) {
                $default->$key = $val;
            }
            return $default;
             */
            return json_decode($settings->settings);
        }

    }

    public static function set_push_setting( $uid, $type, $value ){
        $mUserDevice = new mUserDevice();
        sActionLog::init( 'USER_MODIFY_PUSH_SETTING' );
        switch( $type ){
            case mUserDevice::PUSH_TYPE_COMMENT:
            case mUserDevice::PUSH_TYPE_FOLLOW:
            case mUserDevice::PUSH_TYPE_INVITE:
            case mUserDevice::PUSH_TYPE_REPLY:
            case mUserDevice::PUSH_TYPE_SYSTEM:
            case mUserDevice::PUSH_TYPE_LIKE:
                $ret = $mUserDevice->save_settings( $uid, $type, $value );
                sActionLog::save( $ret );
                break;
            default:
                $ret = false;
        }
        return (bool)$ret;
    }

    public static function getUserUsedDevices( $uid ){
        $devices = (new mUserDevice)->get_all_used_device( $uid );
        return $devices;
    }

    public static function getUserDeviceId($uid){
        $tokenLists = array('ios'=>array(), 'android'=>array());
        $mUserDevice= new mUserDevice;

        $user_device= $mUserDevice->get_last_used_device($uid);
        if($user_device) {
            return $user_device->device_id;
        }
        else {
            return 0;
        }
    }

    public static function offlineUserDevice( $uid ){
        $device_id = self::getUserDeviceId( $uid );
        $device = (new mUserDevice)->get_using_device( $uid , $device_id );
        if( $device ){
            $device->offline_device();
        }

        return true;
    }

    /**
     * 添加新的用户设备
     */
    public static function addNewToken( $uid, $device_id, $settings = array() ){
        $mUserDevice = new mUserDevice;
        if ( empty($settings) ){
            $settings = $mUserDevice->get_default_settings();
        }

        sActionLog::init('ADD_NEW_TOKEN', $settings);
        $mUserDevice->assign(array(
            'uid'=>$uid,
            'device_id'=>$device_id,
            'settings'=>json_encode($settings),
        ));

        //todo: action log
        $ret = $mUserDevice->save();
        sActionLog::save( $ret );
        return $ret;
    }

    /**
     * 绑定用户设备
     */
    public static function bindDevice( $uid, $device_id ){
        $mUserDevice = new mUserDevice;

        $crntUsingDevice = $mUserDevice->get_using_device( $uid, $device_id );
        if ( $crntUsingDevice ) {
            return $crntUsingDevice->refresh_update_time();
        }
        else {
            // 获取以前用过的device修改状态
            $user_device = $mUserDevice->get_used_device($uid, $device_id);
            if($user_device) {
                return $user_device->refresh_update_time();
            }
        }

        //删除用户最后用的设备，并复制最后设置的settings
        $lastDevice = $mUserDevice->get_last_used_device( $uid );
        $settings = array();
        if ( $lastDevice ) {
            //$settings = json_decode($lastDevice->settings);
            $settings = $lastDevice->get_default_settings();

            $lastDevice->offline_device();
        }

        //移除用过相同设备的用户
        $usedDevices = $mUserDevice->get_devices_by_device_id( $device_id );
        if ( $usedDevices ) {
            $usedDevices->offline_device();
        }

        sActionLog::init('USER_CHANGE_DEVICE');
        $ret = self::addNewToken( $uid, $device_id, $settings );
        sActionLog::save( $ret );
        return $ret;
    }

    # service for job push
    public static function getUserDeviceToken($uid, $type = null){
        $tokenLists = array('ios'=>array(), 'android'=>array());
        //如果设定了不推送，就直接返回空的tokenlist
        if($type && !self::getPushSettingByType($uid, $type)) {
            return $tokenLists;
        }

        $mUserDevice= new mUserDevice;
        $mDevice    = new mDevice;

        $user_device= $mUserDevice->get_last_used_device($uid);
        if(!$user_device) {
            #todo: log info
            #return error('USER_DEVICE_NOT_EXIST');
            return $tokenLists;
        }
        $device     = $mDevice->get_device_by_id($user_device->device_id);
        if(!$device) {
            #todo: log info
            #return error('DEVICE_NOT_EXIST');
            return $tokenLists;
        }

        if($device->platform == mDevice::TYPE_ANDROID) {
            $tokenLists['android'] = $device->token;
        }
        else {
            $tokenLists['ios'] = $device->token;
        }
        return $tokenLists;
    }

    public static function getUsersDeviceTokens($uids, $uid){
        $uids = self::removeOwnerUid($uids, $uid);
        //todo 过滤掉不接受系统消息的人

        $tokenLists = array('ios'=>array(), 'android'=>array());
        $mUserDevice= new mUserDevice;
        $mDevice    = new mDevice;

        $user_devices = $mUserDevice->get_using_devices($uids);
        if(!$user_devices) {
            #return error('USER_DEVICE_NOT_EXIST');
            return $tokenList;
        }
        $device_ids = array();
        foreach($user_devices as $row) {
            $device_ids[] = $row->device_id;
        }
        $devices    = $mDevice->get_devices_by_ids($device_ids);
        if(!$devices) {
            #return error('DEVICE_NOT_EXIST');
            return $tokenList;
        }

        foreach($devices as $device) {
            if($device->platform == mDevice::TYPE_ANDROID) {
                $tokenLists['android'][] = $device->token;
            }
            else {
                $tokenLists['ios'][] = $device->token;
            }
        }
        return $tokenLists;
    }

    public static function removeOwnerUid($uids, $uid) {
        //数组反转函数，将数组原来的键变为值，值变为键，
        $uids = array_flip($uids);

        unset($uids["$uid"]);
        $uids = array_flip($uids);   //再次反转
        return $uids;
    }

}

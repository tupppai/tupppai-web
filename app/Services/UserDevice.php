<?php

namespace Psgod\Services;
use \Psgod\Models\UserDevice as mUserDevice;

class UserDevice extends ServiceBase
{
    /**
     * 添加新的用户设备
     */
    public static function addNewToken( $uid, $device_id, $settings = array() ){
        $mUserDevice = new mUserDevice;
        if ( empty($settings) ){
            $settings = $mUserDevice->get_default_settings();
        }

        $mUserDevice->assign(array(
            'uid'=>$uid,
            'device_id'=>$device_id,
            'settings'=>json_encode($settings),
        ));

        //todo: action log
        $ret = $mUserDevice->save();
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

        //删除用户最后用的设备，并复制最后设置的settings
        $lastDevice = $mUserDevice->get_last_used_device( $uid );
        $settings = array();
        if ( $lastDevice ) {
            $settings = json_decode($lastDevice->settings);
            $lastDevice->delete();
        }

        //移除用过相同设备的用户
        $usedDevices = $mUserDevice->get_devices_by_device_id( $device_id );
        if ( $usedDevices ) {
            $usedDevices->delete();
        }

        $ret = self::addNewToken( $uid, $device_id, $settings );
        ActionLog::log(ActionLog::TYPE_USER_CHANGE_DEVICE, array(), $ret);
        return $ret;
    }

    

    //=========================
    public static function getUsersDeviceToken($uids){
        $tokenLists = array('ios'=>array(), 'android'=>array() );
        $uidList = array_filter(explode(',', $uids));
        $where = '';
        if( empty($uidList) ){
            $where = 'TRUE';
        }
        else{
            $where = 'ud.uid IN('.$uids.') AND ud.status='.self::STATUS_NORMAL;
        }

        $builder = mUserDevice::query_builder('ud')
                        ->where($where)
                        ->columns('d.platform, GROUP_CONCAT(d.token) as tokens')
                        ->join('\Psgod\Models\Device', 'd.id=ud.device_id','d','LEFT')
                        ->groupby('d.platform');
        $res = $builder->getQuery()->execute()->toArray();
        $res = array_combine(array_column($res, 'platform'), array_column($res, 'tokens') ) +array('','') ;

        $tokenLists['android']  = $res[Device::TYPE_ANDROID];
        $tokenLists['ios']      = $res[Device::TYPE_IOS];
        return $tokenLists;
    }

    public static function get_push_stgs( $uid ){
        $settings = array();
        if( empty( $uid ) ){
            return false;
        }

        $builder = mUserDevice::query_builder()
                       ->where('uid='.$uid.' AND status='.mUserDevice::STATUS_NORMAL);
        $res =  $builder->getQuery()
                        ->execute()
                        ->toArray();
        $settings = $res[0]['settings'];

        return json_decode($settings);
    }

    public static function set_push_stgs( $uid , $type, $value ){
        $settings = array();
        if( empty( $uid ) ){
            return false;
        }

        // 如果同一个用户在设备A登陆，断网，设备B登陆，在设备A修改推送设置，会修改到设备B的推送设置。
        // （前提：断网重连时，不会再验证token）
        $res = mUserDevice::findFirst('uid='.$uid.' AND status='.mUserDevice::STATUS_NORMAL);

        $settings = json_decode( $res->settings );
        $ret = false;
        switch( $type ){
            case mUserDevice::PUSH_TYPE_COMMENT:
            case mUserDevice::PUSH_TYPE_FOLLOW:
            case mUserDevice::PUSH_TYPE_INVITE:
            case mUserDevice::PUSH_TYPE_REPLY:
            case mUserDevice::PUSH_TYPE_SYSTEM:
                $settings->$type = (bool)$value;
                $res->settings = json_encode($settings);
                $res->update_time = time();
                $res = $res->save_and_return($res);
                if( $res ){
                    $ret = json_decode($res->settings);
                }
                break;
            default:
                break;
        }

        return $ret;
    }
}

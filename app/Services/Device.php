<?php

namespace App\Services;

use \App\Models\Device as mDevice;
use \App\Services\ActionLog as sActionLog;

class Device extends ServiceBase
{

    public static function addNewDevice( $device_name, $device_os, $platform, $device_mac, $token, $options = '' ){

        $device = new mDevice();
        sActionLog::init('NEW_DEVICE', $device);
        $device->assign(array(
            'name'=>$device_name,
            'mac'=>$device_mac,
            'os'=>$device_os,
            'token'=>$token,
            'options'=>$options
        ));
        ActionLog::save( $device );

        return $device->save();
    }

    public static function updateDevice( $uid, $name, $os, $platform, $mac, $token, $options ){
        $deviceInfo = mDevice::where( 'token', $token )->first();

        //现在有在用的设备，则更新时间，并返回
        if( $deviceInfo ){
            return $deviceInfo->refresh_update_time();
        }

        //否则注册新的设备
        switch( $platform ){
            case 0:  $platform = mDevice::TYPE_ANDROID; break;
            case 1:  $platform = mDevice::TYPE_IOS;     break;
            default: $platform = mDevice::TYPE_UNKNOWN; break;
        }

        $ret = self::addNewDevice( $name, $os, $platform, $mac, $token, $options );

        return $ret;
    }
}

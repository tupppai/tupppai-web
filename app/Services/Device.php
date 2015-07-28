<?php

namespace App\Services;

use \App\Models\Device as mDevice;

class Device extends ServiceBase
{

    public static function addNewDevice( $uid, $device_name, $device_os, $platform, $device_mac, $token, $options = '' ){
        $mDevice = new mDevice;
        $mDevice->assign(array(
            'uid'=>$uid,
            'name'=>$device_name,
            'mac'=>$device_mac,
            'os'=>$device_os,
            'token'=>$token,
            'options'=>$options
        ));

        //todo: action log
        return $mDevice->save();
    }

    public static function updateDevice( $uid, $name, $os, $platform, $mac, $token, $options ){
        $deviceInfo = mDevice::findFirst('token="'.$token.'"' );

        if( $deviceInfo ){
            return $deviceInfo->refresh_update_time();
        }

        switch( $platform ){
            case 0:  $platform = mDevice::TYPE_ANDROID; break;
            case 1:  $platform = mDevice::TYPE_IOS;     break;
            default: $platform = mDevice::TYPE_UNKNOWN; break;
        }

        $ret = self::addNewDevice(
            $uid,
            $name,
            $os,
            $platform,
            $mac,
            $token,
            $options
        );
        ActionLog::log(ActionLog::TYPE_NEW_DEVICE, array(), $ret);

        return $ret;
    }
}

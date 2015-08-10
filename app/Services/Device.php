<?php

namespace App\Services;

use \App\Models\Device as mDevice;
use \App\Services\ActionLog as sActionLog;

class Device extends ServiceBase
{

    public static function addNewDevice( $name, $os, $platform, $mac, $token, $options = '' ){
        $mDevice = new mDevice;
        $mDevice->assign(array(
            'name'=>$name,
            'mac'=>$mac,
            'os'=>$os,
            'token'=>$token,
            'options'=>$options
        ));
        ActionLog::save( $device );

        return $device->save();
    }

    public static function updateDevice( $name, $os, $platform, $mac, $token, $options ){
        $mDevice = new mDevice;
        $deviceInfo = $mDevice->get_device_by_token($token);

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

        $ret = self::addNewDevice(
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

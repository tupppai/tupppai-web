<?php

namespace App\Services;

use \App\Models\Device as mDevice;

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

        //todo: action log
        return $mDevice->save();
    }

    public static function updateDevice( $name, $os, $platform, $mac, $token, $options ){
        $mDevice = new mDevice;
        $deviceInfo = $mDevice->get_device_by_token($token);

        if( $deviceInfo ){
            return $deviceInfo->refresh_update_time();
        }

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

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

        $device = $mDevice->save();
        ActionLog::save( $device );
        return $device;
    }

    public static function updateDevice( $name, $os, $platform, $mac, $token, $options ){
        $mDevice = new mDevice;
        $deviceInfo = $mDevice->get_device_by_token($token);

        //现在有在用的设备，则更新时间，并返回
        if( $deviceInfo ){
            sActionLog::init( 'NEW_DEVICE', array() );
            $update_time = $deviceInfo->refresh_update_time();
            sActionLog::save( 'UPDATE_DEVICE' );
            return $update_time;
        }
        
        sActionLog::init( 'NEW_DEVICE', array() );
        //否则注册新的设备
        $ret = self::addNewDevice(
            $name,
            $os,
            $platform,
            $mac,
            $token,
            $options
        );
        ActionLog::save( $ret );

        return $ret;
    }
}

<?php

namespace App\Services;

use \App\Models\Device as mDevice;
use \App\Services\ActionLog as sActionLog;

class Device extends ServiceBase
{

    public static function addNewDevice( $name, $os, $platform, $mac, $token, $options = '' ){
        $mDevice = new mDevice;
        sActionLog::init('ADD_NEW_DEVICE' );
        $mDevice->assign(array(
            'name'=>$name,
            'mac'=>$mac,
            'platform'=>$platform,
            'os'=>$os,
            'token'=>$token,
            'options'=>json_encode($options)
        ));

        $device = $mDevice->save();
        sActionLog::save( $device );
        return $device;
    }

    public static function updateDevice( $name, $os, $platform, $mac, $token, $options ){
        $mDevice = new mDevice;
        $deviceInfo = $mDevice->get_device_by_token($token);

        //现在有在用的设备，则更新时间，并返回
        if( $deviceInfo ){
            sActionLog::init( 'UPDATE_DEVICE' );
            $update_time = $deviceInfo->touch();
            sActionLog::save( $update_time );
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
        sActionLog::save( $ret );

        return $ret;
    }

    public static function getDeviceById($id) {
        $device = (new mDevice)->get_device_by_id($id) ;
        return $device;
    }

    public static function humanReadableInfo( $device ){
        $str = [];
        switch ($device['platform']) {
            case mDevice::TYPE_ANDROID:
                $str['设备类型'] = 'Android';
                break;
            case mDevice::TYPE_IOS:
                $str['设备类型'] = 'iOS';
                break;
            default:
                $str['设备类型'] = '未知设备';
                break;
        }
        $str['设备名称'] = $device['os'];
        $str['其他'] = $device['options'];

        return $str;
    }
}

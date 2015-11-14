<?php

namespace App\Models;
use Phalcon\Mvc\Model\Behavior\SoftDelete;


class UserDevice extends ModelBase
{
    protected $table = 'users_use_devices';

    public function get_settings( $uid ){
        return $this->where([
            'uid' => $uid,
            'status' => self::STATUS_NORMAL
        ])->first();
    }

    public function save_settings( $uid, $type, $value ){
        $userDevice = $this->where([
            'uid' => $uid,
            'status' => self::STATUS_NORMAL
        ])->first();

        if(!$userDevice) return null;

        $settings = json_decode( $userDevice['settings'], true );
        $settings[$type] = (bool)$value;
        $settings = json_encode( $settings );
        $userDevice->assign( [ 'settings' => $settings ] );
        return $userDevice->save();
    }

    /**
     * 默认的设置
     */
    public function get_default_settings() {
        $settings = array(
            self::PUSH_TYPE_COMMENT => true,
            self::PUSH_TYPE_FOLLOW  => true,
            self::PUSH_TYPE_INVITE  => true,
            self::PUSH_TYPE_REPLY   => true,
            self::PUSH_TYPE_SYSTEM  => true,
            self::PUSH_TYPE_LIKE    => true
        );

        return $settings;
    }

    /**
     * 设置默认值
     */
    public function beforeCreate () {
        $this->status       = self::STATUS_NORMAL;

        return $this;
    }

    public function offline_device(){
        $this->update_time = time();
        $this->status = self::STATUS_DELETED;
        return $this->save();
    }

    /**
     * 更新操作时间
     */
    public function refresh_update_time(){
        $this->status      = self::STATUS_NORMAL;
        $this->update_time = time();

        return $this->save();
    }

    /**
     * 获取正在使用的设备信息
     */
    public function get_using_device ( $uid, $device_id ){
        return $this->where( array(
                'uid'       => $uid,
                'device_id' => $device_id,
                'status'    => self::STATUS_NORMAL
            ))
            ->first();
    }

    public function get_used_device ( $uid, $device_id ){
        return $this->where( array(
                'uid'       => $uid,
                'device_id' => $device_id,
            ))
            ->orderBy('update_time', 'DESC')
            ->first();
    }
    public function get_all_used_device( $uid ){
        return $this->where( 'uid', $uid )
            ->orderBy('update_time', 'DESC')
            ->get();
    }

    public function get_last_used_device( $uid ){
        return self::where( array(
                'uid'    => $uid,
                'status' => self::STATUS_NORMAL
            ))
            ->orderBy('update_time', 'DESC')
            ->first();

    }

    public function get_devices_by_device_id( $device_id ){
        //被其他人用过 需设置成删除状态
        return self::where('device_id', $device_id)
            ->where('status', self::STATUS_NORMAL)
            ->first();
    }

    /**
     * 批量获取正在使用的设备信息
     */
    public function get_using_devices( $uids ){
        return self::where('status', self::STATUS_NORMAL)
            ->whereIn('uid', $uids)
            ->get();
    }
}

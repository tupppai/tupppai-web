<?php

namespace App\Models;
use Phalcon\Mvc\Model\Behavior\SoftDelete;


class UserDevice extends ModelBase
{
    protected $table = 'users_use_devices';
    const VALUE_OFF  = '0';
    const VALUE_ON   = '1';

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
            self::PUSH_TYPE_COMMENT => true,
            self::PUSH_TYPE_FOLLO   => true,
            self::PUSH_TYPE_INVITE  => true,
            self::PUSH_TYPE_REPLY   => true,
            self::PUSH_TYPE_SYSTEM  => true
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

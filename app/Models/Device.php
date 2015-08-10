<?php

namespace App\Models;

class Device extends ModelBase
{
    protected $table = 'devices';
    const TYPE_UNKNOWN = -1;
    const TYPE_ANDROID = 0;
    const TYPE_IOS     = 1;

    /**
     * 设置默认值
     */
    public function beforeCreate () {
        #$this->status       = self::STATUS_NORMAL;

        return $this;
    }

    public function refresh_update_time(){
		$this->update_time = time();
        return $this->save();

    }

    public function get_device_by_id($id) {
        return self::find($id);
    }

    public function get_device_by_token($token) {
        return self::where('token', $token)
            ->first();
    }
}

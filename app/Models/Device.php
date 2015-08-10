<?php

namespace App\Models;

class Device extends ModelBase
{
    const TYPE_UNKNOWN = -1;
    const TYPE_ANDROID = 0;
    const TYPE_IOS     = 1;


    public function getSource()
    {
        return 'devices';
    }

    /**
     * 更新时间
     */
    public function beforeSave() {
        $this->update_time  = time();
    }

    /**
     * 设置默认值
     */
    public function beforeCreate () {
        $this->type         = 0;//Unknown
        $this->create_time  = time();
        //$this->status       = self::STATUS_NORMAL;

        return $this;
    }

    public function refresh_update_time(){
		$this->update_time = time();
        return $this->save();
    }
}

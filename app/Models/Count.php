<?php

namespace App\Models;

class Count extends ModelBase
{
    protected $table = 'counts';
    
    /**
     * 设置默认值
     */
    public function beforeCreate () {
        $this->create_time  = time();
        //$this->status       = self::STATUS_NORMAL;

        return $this;
    }

    public function has_counted($uid, $type, $target_id, $action) {
        $count = self::where('uid', $uid)
            ->where('target_id', $target_id)
            ->where('type', $type)
            ->where('status', self::STATUS_NORMAL)
            ->where('action', $action)
            ->first();
        
        return $count; 
    }
}

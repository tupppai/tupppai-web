<?php

namespace App\Models;

class Count extends ModelBase
{
    protected $table = 'counts';
    protected $guarded = ['id'];
    
    /**
     * 设置默认值
     */
    public function beforeCreate () {
        $this->create_time  = time();
        //$this->status       = self::STATUS_NORMAL;

        return $this;
    }

    public function has_counted($uid, $type, $target_id, $action) {
        $count = self::where([
                'uid' =>  $uid,
                'target_id' =>  $target_id,
                'type' =>  $type,
                'status' =>  self::STATUS_NORMAL,
                'action' =>  $action
        ])
        ->first();

        return $count; 
    }
}

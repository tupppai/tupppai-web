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

    public function get_counts_by_uid( $uid, $action, $page, $size ){
        return $this->valid()
                         ->where( 'uid', $uid )
                         ->where( 'action', $action )
                         ->forPage( $page, $size )
                         ->get();
    }
}

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

    public function sum_count_by_uid( $uid, $action) {
        if(!is_array($action)){
            $action = array($action);
        }
        return $this->valid()
            ->where('uid', $uid)
            ->whereIn('action', $action)
            ->count();
    }

    public function get_counts_by_uid( $uid, $action, $page, $size ){
        return $this->valid()
                         ->where( 'uid', $uid )
                         ->where( 'action', $action )
                         ->forPage( $page, $size )
                         ->get();
    }

    public function sum_get_counts_by_uid( $uid, $action ){
        return $this->leftjoin('asks', function( $join ) use ( $uid ){
                        $join->where( 'counts.type', '=', self::TYPE_ASK )
                            ->on('counts.target_id','=', 'asks.id');
                    })
                    ->leftjoin('replies', function( $join ) use ( $uid ){
                        $join->where( 'counts.type', '=', self::TYPE_REPLY )
                            ->on('counts.target_id', '=', 'replies.id');
                    })
                    ->where( 'counts.status', self::STATUS_NORMAL )
                    ->where( 'counts.action', $action )
                    ->count('counts.id');
    }
}

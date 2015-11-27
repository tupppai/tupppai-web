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
                     ->orderBy( 'create_time', 'DESC')
                     ->forPage( $page, $size )
                     ->get();
    }

    public function get_counts_by_replyids($replyids, $update_time, $action) {
        return $this->valid()
                     ->whereIn( 'target_id', $replyids )
                     ->where( 'type', self::TYPE_REPLY)
                     ->where( 'action', $action)
                     ->where( 'create_time', '>', $update_time)
                     ->orderBy( 'create_time', 'DESC')
                     ->get();
    }

    public function sum_get_counts_by_uid( $uid, $action ){
        if( !is_array( $action ) ){
            $action = [$action];
        }
        return $this->leftjoin('asks', function( $join ){
                        $join->where( 'counts.type', '=', self::TYPE_ASK )
                            ->on('counts.target_id','=', 'asks.id');
                    })
                    ->leftjoin('replies', function( $join ){
                        $join->where( 'counts.type', '=', self::TYPE_REPLY )
                            ->on('counts.target_id', '=', 'replies.id');
                    })
                    ->where( 'counts.status', self::STATUS_NORMAL )
                    ->whereIn( 'counts.action', $action )
                    ->where( 'asks.uid', $uid )
                    ->orwhere( 'replies.uid', $uid )
                    ->count('counts.id');
    }
}

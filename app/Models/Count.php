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

        return $this;
    }

    /**
     * 判断是否有操作数据
     */
    public function has_counted($uid, $type, $target_id, $action) {
        $count = self::where([
                'action' =>  $action,
                'uid' =>  $uid,
                'type' =>  $type,
                'target_id' =>  $target_id,
                'status' =>  self::STATUS_NORMAL
        ])
        ->first();

        return $count;
    }

    /**
     * 通过uid获取counts数据
     */
    public function get_counts_by_uid( $uid, $action, $page, $size ){
        return $this->valid()
                     ->where( 'action', $action )
                     ->where( 'uid', $uid )
                     ->orderBy( 'create_time', 'DESC')
                     ->forPage( $page, $size )
                     ->get();
    }

    /**
     * 通过replyids获取操作数据
     */
    public function get_counts_by_replyids($replyids, $update_time, $action) {
        return $this->valid()
                     ->where( 'action', $action)
                     ->where( 'type', self::TYPE_REPLY)
                     ->whereIn( 'target_id', $replyids )
                     ->where( 'create_time', '>', $update_time)
                     ->orderBy( 'create_time', 'DESC')
                     ->get();
    }

    /**
     * 通过ask_id统计类型
     */
    public function count_by_cond($cond) {
        $builder = $this->valid();
        if(isset($cond['uid'])) $builder = $builder->where('uid', $cond['uid']);
        if(isset($cond['type'])) $builder = $builder->where('type', $cond['type']);
        if(isset($cond['target_id'])) $builder = $builder->where('target_id', $cond['target_id']);
        if(isset($cond['action'])) $builder = $builder->where('action', $cond['action']);

        return $builder->count();
    }

    /**
     * 通过ask_id 统计 love num
     */
    public function sum_by_cond($cond) {
        $builder = $this->valid();
        if(isset($cond['uid'])) $builder = $builder->where('uid', $cond['uid']);
        if(isset($cond['type'])) $builder = $builder->where('type', $cond['type']);
        if(isset($cond['target_id'])) $builder = $builder->where('target_id', $cond['target_id']);
        if(isset($cond['action'])) $builder = $builder->where('action', $cond['action']);

        return $builder->sum('num');
    }
}

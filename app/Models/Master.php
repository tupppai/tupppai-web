<?php
namespace App\Models;

class Master extends ModelBase{
    const STATUS_PENDING = 5;

    protected $table = 'masters';
    const CREATED_AT = 'set_time';

    public function scopeValid( $query, $table = null ){
        $query->where( 'status', '=', self::STATUS_NORMAL )
              ->where( 'start_time', '<=', time() ) //已经开始的
              ->where( 'end_time', '>=', time() ); //还未结束的
    }

    public function scopePending( $query, $table = null ){
        $query->where('status', '!=', self::STATUS_PENDING)
              ->where('status','!=', self::STATUS_DELETED)
                ->where('start_time', '>', time() ); //未开始的
    }

    public function user(){
        return $this->belongsTo( 'App\Models\User', 'uid', 'uid' );
    }

    /**
     * 设置默认值
     */
    public function beforeCreate () {
        $this->set_time = time();
        $this->del_time = 0;
        $this->del_by = 0;
        $this->status = self::STATUS_PENDING;
        return $this;
    }

    /**
    * 分页方法
    *
    * @param int 加数
    * @param int 被加数
    * @return integer
    */
    public function page($keys = array(), $page=1, $limit=10) {
        $builder = self::query_builder();
        foreach ($keys as $k => $v) {
            $builder = $builder->where($k, '=', $v);
        }
        //$builder = $builder->where('start_time', time());
        //$builder = $builder->where('end_time', time());
        $builder = $builder->where('status', self::STATUS_NORMAL);
        $builder = $builder->orderBy('start_time', 'ASC');

        return self::query_page($builder, $page, $limit);
    }

    /**
     * 更新大神状态
     */
    public function update_master_status() {
        return self::where( 'start_time', '<', time() )
            ->where( 'end_time', '>', time() )
            ->where( 'status', self::STATUS_PENDING )
            ->update( [ 'status' => self::STATUS_NORMAL ] );
    }

    /**
     * 获取大神列表
     */
    public function get_valid_master_list($page, $size) {
        $masters = $this->validMasters()
            ->forPage( $page, $size )
            ->lists( 'uid' );
        return $masters;
    }

    /**
     * 通过id获取master
     */
    public function get_master_by_id($id) {
        return $this->where('id', $id)->first();
    }
}

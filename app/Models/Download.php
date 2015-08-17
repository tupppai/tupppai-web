<?php

namespace App\Models;

class Download extends ModelBase
{
    protected $table = 'downloads';

    public function get_download_record_by_id( $id ){
       return $this->where( [ 'id' => $id ] )->first(); 
    }
    public function get_downloaded( $uid, $last_updated, $page, $size ){
        return $this->where( 'uid', $uid )->where( 'update_time', '<', $last_updated )->forPage( $page, $size )->get();
    }


    /**
    * 分页方法
    */
    public function page($keys = array(), $page, $limit)
    {
        $builder = self::query_builder();
        foreach ($keys as $k => $v) {
            $builder = $builder->where($k, $v);
        }
        return self::query_page($builder, $page, $limit);
    }

    /**
     * 计算用户发的下载数量
     */
    public function count_user_download($uid) {
        $count = self::where('uid', $uid)
            ->where('status', self::STATUS_NORMAL)
            ->count();
        return $count;
    }
    
    /**
     * 计算作品的下载数量
     */
    public function count_reply_download($reply_id) {
        $count = self::where('target_id', $reply_id)
            ->where('type', self::TYPE_REPLY)
            ->where('status', self::STATUS_NORMAL)
            ->count();
        return $count;
    }

    /**
     * 判断用户是否下载过
     */
    public function has_downloaded($uid, $type, $target_id) {
        return self::where('type', $type)
            ->where('uid', $uid)
            ->where('target_id', $target_id);
    }
}

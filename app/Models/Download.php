<?php

namespace App\Models;

class Download extends ModelBase
{
    protected $table = 'users';

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
     * 判断用户是否下载过
     */
    public function has_downloaded($uid, $type, $target_id) {
        return self::where('type', $type)
            ->where('uid', $uid)
            ->where('target_id', $target_id);
    }
}

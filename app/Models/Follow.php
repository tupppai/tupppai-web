<?php

namespace App\Models;
use Phalcon\Mvc\Model\Resultset\Simple as Resultset;

class Follow extends ModelBase
{
    protected $table = 'follows';

    public function get_follower_users ( $uid, $page, $limit ) {
        $builder = self::query_builder();
        $builder->where('uid', $uid);
        $builder->orderBy('update_time DESC');
        return self::query_page($builder, $page, $limit);
    }

    public function get_fans_users ( $uid, $page, $limit ) {
        $builder = self::query_builder();
        $builder->where('follow_who', $uid);
        $builder->orderBy('update_time DESC');
        return self::query_page($builder, $page, $limit);
    }

    /**
     * 获取粉丝数量
     */
    public function count_user_fans($uid) {
        $count = self::where('follow_who', '=', $uid)
            ->where('status', '=', self::STATUS_NORMAL)
            ->count();
        return $count;
    }

    /**
     * 获取关注的人数量
     */
    public function count_user_followers($uid) {
        $count = self::where('uid', '=', $uid)
            ->where('status', '=', self::STATUS_NORMAL)
            ->count();
        return $count;
    }

    /**
     * 获取粉丝
     */
    public function get_user_fans($uid) {
        $users = self::where('follow_who', '=', $uid)
            ->where('status', '=', self::STATUS_NORMAL)
            ->select('uid')
            ->get();
        return $users;
    }

    /**
     * 获取关注的人
     */
    public function get_user_friends($uid) {
        $users = self::where('uid', '=', $uid)
            ->where('status', '=', self::STATUS_NORMAL)
            ->select('follow_who')
            ->get();
        return $users;
    }

}

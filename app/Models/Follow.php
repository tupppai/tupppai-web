<?php

namespace App\Models;
use Phalcon\Mvc\Model\Resultset\Simple as Resultset;

class Follow extends ModelBase
{
    public function getSource()
    {
        return 'follows';
    }

    public function initialize()
    {
        parent::initialize();
        $this->useDynamicUpdate(true);
    }

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
}

<?php

namespace App\Models;

class Label extends ModelBase
{
    protected $table = 'labels';

    const DIRE_LEFT = 1;
    const DIRE_RIGHT= 3;

    /**
    * 分页方法
    *
    * @param int 加数
    * @param int 被加数
    * @return integer
    */
    public function page($keys = array(), $page=1, $limit=10, $type='new')
    {
        $builder = self::query_builder();
        foreach ($keys as $k => $v) {
            $builder = $builder->where($k, '=', $v);
        }
        return self::query_page($builder, $page, $limit);
    }

    //public static function addNewLabel($content, $x, $y, $uid, $direction, $upload_id, $target_id, $type=self::TYPE_ASK)
    //public function to_simple_array()
}

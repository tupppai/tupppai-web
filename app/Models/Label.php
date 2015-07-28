<?php

namespace Psgod\Models;

class Label extends ModelBase
{
    const TYPE_ASK  = 1;
    const TYPE_REPLY= 2;
    const DIRE_LEFT = 1;
    const DIRE_RIGHT= 3;

    public function getSource()
    {
        return 'labels';
    }

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
        $conditions = 'TRUE';
        //$conditions .= " type = {$type} AND target_id  = {$target_id} ";
        foreach ($keys as $k => $v) {
            $conditions .= " AND $k = :$k:";
        }

        $builder->where($conditions, $keys);
        $builder->andWhere('status = :status:', array('status' => self::STATUS_NORMAL));
        return self::query_page($builder, $page, $limit);
    }

    //public static function addNewLabel($content, $x, $y, $uid, $direction, $upload_id, $target_id, $type=self::TYPE_ASK)
    //public function to_simple_array()
}

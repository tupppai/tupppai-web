<?php
namespace App\Models;

class Master extends ModelBase{
    const STATUS_DELETE = -1;
    const STATUS_PENDING = 0;
    const STATUS_VALID = 1;

    public function getSource(){
        return 'masters';
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
        foreach ($keys as $k => $v) {
            $conditions .= " AND $k = :$k:";
        }

        $builder->where($conditions, $keys);
        $builder->andWhere('start_time = :start_time:', array('start_time' => time()));
        $builder->andWhere('end_time = :end_time:', array('end_time' => time()));
        $builder->andWhere('status = :status:', array('status' => self::STATUS_VALID));
        $builder->orderBy('start_time ASC');
        return self::query_page($builder, $page, $limit);
    }

    //public static function update_masters(){
    //public static function get_master_list($page = 1, $size = 15){
}

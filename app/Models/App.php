<?php
namespace Psgod\Models;
use Psgod\Models\Upload;

class App extends ModelBase{

    public function getSource(){
        return 'recommend_apps';
    }

    public function beforeCreate(){
        $this->order_by=9999;
        $this->create_time = time();
        return $this;
    }

    /**
     * app分页方法
     */
    public function page($keys = array(), $page=1, $limit=3)
    {
        $builder = self::query_builder();
        $builder->columns('id, app_name, jumpurl');
        $conditions = 'TRUE';
        foreach ($keys as $k => $v) {
            $conditions .= " AND $k = :$k:";
        }

        $builder->where($conditions, $keys);
        $builder->andWhere('del_time IS NULL');
        $builder->orderBy('order_by ASC');
        return self::query_page($builder, $page, $limit);
    }

    public function get_image_url(){
    }

    public function get_apps(){
        // todo: configurize
        define( 'APP_LIST_NUM', 3 );
        $apps = $this->find(array(
            'columns' => 'id, app_name, jumpurl',
            'conditions' => 'del_time IS NULL',
            'order' => 'order_by ASC',
            'limit' => APP_LIST_NUM
        ));

        return $apps;
    }
}

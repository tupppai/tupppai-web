<?php namespace App\Models;

class Tag extends ModelBase{
    protected $table = 'tags';
    protected $guarded = ['id'];

    /**
     * 设置默认值
     */
    public function beforeCreate () {
        $this->status       = self::STATUS_NORMAL;

        return $this;
    }

    public function get_tags($page, $size, $cond = []){
        return $this->where($cond)
                    ->forPage( $page, $size )
                    ->get();
    }

    public function get_tag_by_id($id) {
        return $this->find($id);
    }
}

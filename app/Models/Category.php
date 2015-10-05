<?php namespace App\Models;

class Category extends ModelBase{
    protected $table = 'categories';
    protected $guarded = ['id'];

    /**
     * 设置默认值
     */
    public function beforeCreate () {
        $this->status       = self::STATUS_NORMAL;
        $this->create_by    = 0;
        $this->update_by    = 0;

        return $this;
    }

    public function get_categories(){
        return $this->valid()->get();
    }

    public function get_category_byid($id) {
        return $this->find($id);
    }
}

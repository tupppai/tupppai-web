<?php namespace App\Models;

class Category extends ModelBase{
    protected $table = 'categories';
    protected $guarded = ['id'];

    const STATUS_NORMAL = 1;
    const STATUS_DELETED = 0;
    
    /**
     * 设置默认值
     */
    public function beforeCreate () {
        $this->status       = self::STATUS_NORMAL;
        $this->create_by    = 0;
        $this->update_by    = 0;

        return $this;
    }

    public function scopeValid( $query ){
    	return $query->where( 'status', self::STATUS_NORMAL );
    }

    public function scopeInvalid( $query ){
    	return $query->where( 'status', self::STATUS_DELETED );
    }

    public function get_categories(){
        return $this->valid()->get();
    }

    public function get_category_byid($id) {
        return $this->find($id);
    }
}

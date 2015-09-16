<?php
namespace App\Models;

class Category extends ModelBase{
    protected $table = 'categories';
    protected $guarded = ['id'];

    const STATUS_NORMAL = 1;
    const STATUS_DELETED = 0;

    public function scopeValid( $query ){
    	return $query->where( 'status', self::STATUS_NORMAL );
    }

    public function scopeInvalid( $query ){
    	return $query->where( 'status', self::STATUS_DELETED );
    }

    public function get_categories(){
        return $this->valid()->get();
    }

}

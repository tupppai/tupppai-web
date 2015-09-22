<?php namespace App\Models;

class CommentStock extends ModelBase{
    protected $table = 'comments_stock';
    protected $guarded = ['id','used_times'];

    public function scopeValid( $query ){
    	return $query->where( 'status', self::STATUS_NORMAL );
    }

    public function list_comments( $cond ){
    	if( !isset( $cond['content'] ) ){
    		$cond['content'] = '';
    	}
    	$data = $this->valid()
                  ->where( 'content', 'like', '%'. $cond['content'].'%' )
    			  ->orderBy( 'sort', 'DESC' )
    			  ->paginate( config( 'global.app.DEFAULT_PAGE_SIZE' ) );
   		return $data;
    }
}

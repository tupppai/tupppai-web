<?php namespace App\Models;

class CommentStock extends ModelBase{
    protected $table = 'comments_stock';
    protected $guarded = ['id','used_times'];

    public function list_comments( $uid, $cond ){
    	if( !isset( $cond['content'] ) ){
    		$cond['content'] = '';
    	}
    	$data = $this->valid()
                  ->where( 'owner_uid', $uid )
                  ->where( 'content', 'like', '%'. $cond['content'].'%' )
                  ->orderBy( 'used_times','DESC' )
    			  ->paginate( config( 'global.app.DEFAULT_PAGE_SIZE' ) );
   		return $data;
    }

    public function get_all_comments( $uid ){
        $data = $this->valid()
                  ->where( 'owner_uid', $uid )
                  ->orderBy( 'used_times','DESC' )
                  ->get();
        return $data;
    }
}

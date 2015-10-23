<?php namespace App\Services;

use App\Services\ActionLog as sActionLog;

use App\Models\CommentStock as mCommentStock;

class CommentStock extends ServiceBase{
	public static function getCommentStock( $uid, $cond ){
		$mCommentStock = new mCommentStock();

        $data = $mCommentStock->list_comments( $uid, $cond );
        return $data;
	}
	public static function getComments( $uid ){
		return ( new mCommentStock )->get_all_comments( $uid );
	}

	public static function addComments( $uid, $comments ){
		$mCommentStock = new mCommentStock();

		foreach( $comments as $comment ){
			$cond = ['owner_uid'=> $uid, 'content'=>$comment];
			$data = $cond;
            sActionLog::init( 'ADD_COMMENT_STOCK' );
            #sky 修改成save?
			$c = $mCommentStock->updateOrCreate( $cond, $data );
			sActionLog::save( $c );
		}

		return $c;
	}

	public static function deleteComments( $uid, $comment_ids ){
		$mCommentStock = new mCommentStock();
        sActionLog::init( 'DELETE_COMMENT_STOCK' );
        #sky 尽量少用whereIn ？在service里面
        $comments = $mCommentStock
        		->whereIn( 'id', $comment_ids )
        		->update( [ 'status' => $mCommentStock::STATUS_DELETED ] );
        sActionLog::save( $comments );
	}

	public static function getCommentByStockId( $uid, $cmntId ){
		$mCommentStock = new mCommentStock();

        #sky firstOrFail 从来没用过
		$cmnt = $mCommentStock->where(['owner_uid'=>$uid, 'id'=>$cmntId])->firstOrFail();

		return $cmnt;
	}

	public static function usedComment( $cid ){
        $mCommentStock = new mCommentStock();
        #sky db操作都写在model里面，通过一个函数暴露出来
		$mCommentStock->where( ['id'=>$cid] )->increment( 'used_times' );
	}
}

<?php

namespace App\Models;

class Inform extends ModelBase{

	const CONTENT_MIN_LENGTH = 15;
	const CONTENT_MAX_LENGTH = 5000;

	protected $tables = 'informs';

	public function beforeCreate(){
		$this->create_time = time();
		$this->update_time = time();
		$this->status = self::INFORM_STATUS_PENDING;

		return $this;
    }

	public function beforeSave(){
		$this->update_time = time();

		return $this;
	}

    public function get_pending_inform_by( $uid, $target_type, $target_id ){
        $result = $this->where([
			'uid'=>$uid,
			'target_type'=>$target_type,
			'target_id'=>$target_id,
			'status'=>self::INFORM_STATUS_PENDING
		])->first();

        return $result;
    }

	public function get_inform_by_id( $id ){
		return $this->where('id', $id)->first( );
	}

	public function deal_report( $id, $uid, $result, $status = mInform::INFORM_STATUS_SOLVED ){
		if( $this->status != $this::INFORM_STATUS_PENDING ){
			return false;
		}
		$this ->assign(array(
			'status'      => $status,
			'oper_time'   => time(),
			'oper_by'     => $uid,
			'oper_result' => $result
		));

		return $this->save();
    }

    public function sum_reported_times_by_uid( $uid ){
        return $this->leftjoin('asks', function( $join ) use ( $uid ){
                        $join->where( 'informs.target_type', '=', self::TYPE_ASK )
                             ->on('informs.target_id','=', 'asks.id');
                    })
                    ->leftjoin('replies', function( $join ) use ( $uid ){
                        $join->where( 'informs.target_type', '=', self::TYPE_REPLY )
                             ->on('informs.target_id', '=', 'replies.id');
                    })
                    ->leftjoin('comments', function( $join ) use ( $uid ){
                        $join->where('informs.target_type','=', self::TYPE_COMMENT )
                             ->on( 'informs.target_id', '=', 'comments.id');
                    })
                    ->where(function($query) use ( $uid ) {
                        $query =$query->where( 'asks.uid','=', $uid)
                              ->orwhere( 'replies.uid','=', $uid)
                              ->orwhere( 'comments.uid','=', $uid);
                    })
                    ->whereIn( 'informs.status', [ self::INFORM_STATUS_PENDING, self::INFORM_STATUS_SOLVED ] )
                    ->count( 'informs.id' );
    }
    public function sum_report_times_by_uid( $uid ){
        return $this->where( 'uid', $uid )
                    ->whereIn( 'status', [ self::INFORM_STATUS_PENDING, self::INFORM_STATUS_SOLVED ] )
                    ->count();
    }
	//public static function report( $uid, $target_type, $target_id, $content ){
	//public function deal_report( $id, $uid, $result, $status = Inform::INFORM_STATUS_SOLVED ){
}

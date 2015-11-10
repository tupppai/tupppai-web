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

	//public static function report( $uid, $target_type, $target_id, $content ){
	//public function deal_report( $id, $uid, $result, $status = Inform::INFORM_STATUS_SOLVED ){
}

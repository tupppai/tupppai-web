<?php

namespace App\Models;

class Inform extends ModelBase{
	const TARGET_TYPE_ASK = 1;
	const TARGET_TYPE_REPLY = 2;
	const TARGET_TYPE_COMMENT = 3;
	const TARGET_TYPE_USER = 4;

	const CONTENT_MIN_LENGTH = 15;
	const CONTENT_MAX_LENGTH = 5000;

	const INFORM_STATUS_IGNORED  = 0; //删除
	const INFORM_STATUS_PENDING  = 1; //已举报，待处理
	const INFORM_STATUS_SOLVED   = 2; //已处理
	const INFORM_STATUS_REPLACED = 3; //重复举报

	public function getSource(){
		return 'informs';
	}

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
        $result = self::findFirst(array(
			'uid='.$uid,
			' AND target_type='.$target_type,
			' AND target_id='.$target_id,
			' AND status='.self::INFORM_STATUS_PENDING
		));

        return $result;
    }

	public function get_inform_by_id( $id ){
		return self::findFirst( 'id='.$id );
	}

	//public static function report( $uid, $target_type, $target_id, $content ){
	//public function deal_report( $id, $uid, $result, $status = Inform::INFORM_STATUS_SOLVED ){
}

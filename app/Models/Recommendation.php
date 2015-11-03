<?php
namespace App\Models;

class Recommendation extends ModelBase
{
	public function scopePassed( $query ){
		$query->where( 'status', self::STATUS_NORMAL );
	}

	public function introducer(){
		return $this->belongsTo('App\Models\User', 'introducer_uid', 'uid');
	}

	public function user(){
		return $this->belongsTo('App\Models\User', 'uid', 'uid' );
	}

	public function add( $introducer_uid, $uid, $role_id, $reason ){
		return $this->assign([
			'introducer_uid' => $introducer_uid,
			'uid' => $uid,
			'reason' => $reason,
			'role_id' => $role_id,
			'status' => $this::STATUS_CHECKED
		])->save();
	}

	public function update_status( $uid, $status, $result ){
		return $this->assign([
			// 'update_by': $uid,
			'status' => $status,
			'result' => $result
		])->save();
	}
}

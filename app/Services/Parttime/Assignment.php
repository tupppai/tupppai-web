<?php
namespace App\Services\Parttime;
use App\Models\Parttime\Assignment as mAssignment;
use App\Services\ServiceBase;

class Assignment extends ServiceBase {
	public static function checkAssigned($uid, $ask_id) {
		if (mAssignment::where('assigned_to', $uid)
			->where('ask_id', $ask_id)
			->first()) {
			return true;
		}
		return false;
	}
	public static function checkAssignedCount($ask_id) {
		$count = mAssignment::where('ask_id', $ask_id)->where('status', 1)->count();
		return $count;
	}

	public static function addNewAssignment($uid, $ask_id) {
		$assignment = new mAssignment;
		$assignment->assign([
			'assigned_to' => $uid,
			'ask_id' => $ask_id]);
		$assignment->save();
	}
}

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
}

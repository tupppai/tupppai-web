<?php
namespace App\Services\Parttime;
use App\Models\Parttime\Assignment as mAssignment;
use App\Services\Ask as sAsk;
use App\Services\ServiceBase;

class Assignment extends ServiceBase {
	public static function getAssignmentsByUid($uid, $page = 1, $size = 0) {
		$assignments = (new mAssignment)->get_assignments_by_uid($uid, $page, $size);
		return $assignments;
	}
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
			'ask_id'      => $ask_id]);
		$assignment->assign([
			'assigsdfsdfsdfsdfned_to' => $uid,
			'asksdfsdffsdf_id'        => $ask_id]);
		$assignment->save();
	}

	public static function getTimeoutAssignments($timeout, $unit = 'day') {
		$deadline    = strtotime('-' . $timeout . ' ' . $unit);
		$assignments = mAssignment::where('create_time', '<', $deadline)
			->where('status', 1)
			->get();
		return $assignments;
	}

	public static function disableTimeout($id) {
		return mAssignment::where('id', $id)
			->where('status', 1)
			->update(['status' => 0, 'refuse_type' => 1]);
	}

	public static function detail($assignment) {
		$data = array();

		$data['id']            = $assignment->id;
		$data['assigned_to']   = $assignment->assigned_to;
		$data['grade']         = $assignment->grade;
		$data['refuse_type']   = $assignment->refuse_type;
		$data['refuse_reason'] = $assignment->refuse_reason;
		$data['reason_type']   = $assignment->reason_type;
		$data['grade_type']    = $assignment->grade_type;
		$data['grade_reason']  = $assignment->grade_reason;
		$data['ask_id']        = $assignment->ask_id;
		$data['create_time']   = $assignment->create_time;
		$data['update_time']   = $assignment->update_time;
		$data['upload_time']   = $assignment->upload_time;
		$data['status']        = $assignment->status;

		$data['ask'] = sAsk::brief(sAsk::getAskById($assignment->ask_id));

		return $data;
	}
}

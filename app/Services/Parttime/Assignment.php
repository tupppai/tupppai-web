<?php
namespace App\Services\Parttime;
use App\Models\Parttime\Assignment as mAssignment;
use App\Services\Ask as sAsk;
use App\Services\ServiceBase;

class Assignment extends ServiceBase {
	public static function getAssignmentsByUid($uid, $page = 1, $size = 0, $status = NULL ) {
		$assignments = (new mAssignment)->get_assignments_by_uid($uid, $page, $size, $status);
		return $assignments;
	}
	public static function userRefuse(mAssignment $assignment, $reason_type, $refuse_reason) {
		$assignment->status        = mAssignment::ASSIGNMENT_STATUS_REFUSE;
		$assignment->refuse_type   = mAssignment::ASSIGNMENT_REFUSE_TYPE_USER;
		$assignment->reason_type   = $reason_type;
		$assignment->refuse_reason = $refuse_reason;
		$assignment->save();
	}
	public static function checkAssigned($uid, $ask_id) {
		if (mAssignment::where('assigned_to', $uid)
			->where('ask_id', $ask_id)
			->first()) {
			return true;
		}
		return false;
	}
	public static function getAssignmentById($id, $status = null, $uid = null) {
		$assignments = (new mAssignment)->get_assignment_by_id($id, $status, $uid);
		return $assignments;
	}
	public static function recordStatus(mAssignment $assignment, $status = mAssignment::ASSIGNMENT_STATUS_RECEIVE) {
		if ($assignment->status != $status) {
			$assignment->status = $status;
			$assignment->save();
		}
	}
	public static function checkAssignedCount($ask_id) {
		$count = mAssignment::where('ask_id', $ask_id)->whereIn('status', [mAssignment::ASSIGNMENT_STATUS_DISPATCH, mAssignment::ASSIGNMENT_STATUS_RECEIVE])->count();
		return $count;
	}

	public static function addNewAssignment($uid, $ask_id) {
		$assignment = new mAssignment;
		$assignment->assign([
			'assigned_to' => $uid,
			'ask_id' => $ask_id]);
		$assignment->save();
	}

	public static function getTimeoutAssignments($timeout, $unit = 'day') {
		$deadline = strtotime('-' . $timeout . ' ' . $unit);
		$assignments = mAssignment::where('create_time', '<', $deadline)
			->whereIn('status', [mAssignment::ASSIGNMENT_STATUS_DISPATCH, mAssignment::ASSIGNMENT_STATUS_RECEIVE])
			->get();
		return $assignments;
	}

	public static function disableTimeout($id) {
		return mAssignment::where('id', $id)
			->whereIn('status', [mAssignment::ASSIGNMENT_STATUS_DISPATCH, mAssignment::ASSIGNMENT_STATUS_RECEIVE])
			->update(['status' => mAssignment::ASSIGNMENT_STATUS_REFUSE, 'refuse_type' => mAssignment::ASSIGNMENT_REFUSE_TYPE_TIMEOUT]);
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

		switch( $assignment->status ){
			case mAssignment::ASSIGNMENT_STATUS_DISPATCH:
				$status_text = '已分配';
				break;
			case mAssignment::ASSIGNMENT_STATUS_RECEIVE:
				$status_text = '已接收';
				break;
			case mAssignment::ASSIGNMENT_STATUS_FINISHED:
				$status_text = '已完成';
				break;
			case mAssignment::ASSIGNMENT_STATUS_REFUSE:
				$status_text = '已拒绝';
				break;
			case mAssignment::ASSIGNMENT_STATUS_GRADED:
				if( $assignment->grade ){
					$status_text = '审核通过(奖励'.$assignment->grade.'元)';
				}
				else{
					$status_text = '审核拒绝(拒绝理由：'.$assignment->grade_reason.')';
				}
		}
		$data['status_text']   = $status_text;

		$data['ask'] = sAsk::brief(sAsk::getAskById($assignment->ask_id));

		return $data;
	}
}

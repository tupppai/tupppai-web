<?php
namespace App\Models\Parttime;
use App\Models\ModelBase;

class Assignment extends ModelBase {
	protected $connection = 'db_parttime';

	public function get_assignments_by_type( $type, $page = 1, $size = 15, $uids = [] ){
		$asgnmnt = $this;
		switch( $type ){
			case 'done':
				$asgnmnt = $asgnmnt->where( 'status', self::ASSIGNMENT_STATUS_FINISHED);
				break;
			case 'checked':
				$asgnmnt = $asgnmnt->where( 'status', self::ASSIGNMENT_STATUS_GRADED )
					->where('grade', '!=', 0 );
				break;
			case 'rejected':
				$asgnmnt = $asgnmnt->where( 'status', self::ASSIGNMENT_STATUS_GRADED )
					->where('grade', 0 );
				break;
			case 'refused':
				$asgnmnt = $asgnmnt->where( 'status', self::ASSIGNMENT_STATUS_REFUSE );
				break;
			default:
				return false;
		}

		if( $uids ){
			if( is_array( $uid ) ){
				$asgnmnt = $asgnmnt->whereIn( 'assigned_to', $uid );
			}
			else{
				$asgnmnt = $asgnmnt->where('assigned_to', $uid);
			}
		}

		return $asgnmnt->forPage( $page, $size )
					->get();
	}

	/**
	 * 通过用户id获取任务集合
	 */

	public function get_assignments_by_uid($uid, $page = 1, $size = 0, $status = null) {
		$asgnmnt = $this;
		if( is_array( $uid ) ){
			$asgnmnt = $asgnmnt->whereIn( 'assigned_to', $uid );
		}
		else{
			$asgnmnt = $asgnmnt->where('assigned_to', $uid);
		}

		if (is_array($status)) {
			$asgnmnt = $asgnmnt->whereIn('status', $status);
		}
		else{
			$asgnmnt = $asgnmnt->where('status', $status);
		}
		return $asgnmnt->forPage( $page, $size )
					->get();
	}
	/**
	 * 通过id获取任务
	 */
	public function get_assignment_by_id($assignment_id, $status = null, $uid = null) {
		$builder = self::where('id', $assignment_id);
		if (is_array($status)) {
			$builder->whereIn('status', $status);
		}
		if (is_int($uid)) {
			$builder->where('assigned_to', $uid);
		}
		$assignment = $builder->first();

		return $assignment;
	}

	/**
	 * 通过id集合获取任务
	 */
	public function get_assignment_by_ids($assignment_ids, $page = 1, $size = 0) {
		#$assignments = self::whereRaw(" FIND_IN_SET (id ,$assignment_ids)")
		#->get();
		$builder = self::whereIn('id', $assignment_ids);

		return self::query_page($builder, $page, $size);
	}

	public function verify_task( $oper_by, $aid, $grade, $reason ){
		$asgnmnt = $this->get_assignment_by_id( $aid );
		$asgnmnt->assign([
			'grade' => $grade,
			'grade_reason' => $reason,
			'status' => self::ASSIGNMENT_STATUS_GRADED,
			'oper_by' => $oper_by
		])
		->save();

		return $asgnmnt;
	}
}

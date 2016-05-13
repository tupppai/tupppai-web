<?php
namespace App\Models\Parttime;
use App\Models\ModelBase;

class Assignment extends ModelBase {
	protected $connection = 'db_parttime';
	/**
	 * 通过用户id获取任务集合
	 */
	public function get_assignments_by_uid($uid, $page = 1, $limit = 0, $status = null) {
		$builder = self::where('assigned_to', $uid);
		if (is_array($status)) {
			$builder->whereIn('status', $status);
		}
		return self::query_page($builder, $page, $limit);
	}
	/**
	 * 通过id获取任务
	 */
	public function get_assignment_by_id($assignment_id, $status = null, $uid = null) {
		$builder = self::where('id', $assignment_id);
		if (is_array($status)) {
			$builder->whereIn('status', $status);
		}
		if (!is_int($uid)) {
			$builder->where('assigned_to', $uid);
		}
		$assignment = $builder->first();

		return $assignment;
	}

	/**
	 * 通过id集合获取任务
	 */
	public function get_assignment_by_ids($assignment_ids, $page = 1, $limit = 0) {
		#$assignments = self::whereRaw(" FIND_IN_SET (id ,$assignment_ids)")
		#->get();
		$builder = self::whereIn('id', $assignment_ids);
		return self::query_page($builder, $page, $limit);
	}
}
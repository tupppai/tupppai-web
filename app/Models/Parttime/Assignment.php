<?php
namespace App\Models\Parttime;
use App\Models\ModelBase;

class Assignment extends ModelBase {
	protected $connection = 'db_parttime';
	/**
	 * 通过id获取任务
	 */
	public function getAssignmentById($assignment_id) {
		$assignment = self::find($assignment_id);

		return $assignment;
	}

	/**
	 * 通过id集合获取任务
	 */
	public function getAssignmentByIds($assignment_ids) {
		#$assignments = self::whereRaw(" FIND_IN_SET (id ,$assignment_ids)")
		#->get();
		$assignments = self::whereIn('id', $assignment_ids)
			->get();

		return $assignments;
	}
}

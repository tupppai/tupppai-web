<?php
namespace App\Models\Parttime;

use App\Models\ModelBase;

class Designer extends ModelBase {
	protected $connection = 'db_parttime';
	public function get_all_designers_uid( ){
		return $this->where('status', self::STATUS_NORMAL)
					->select('uid')
					->get();
	}

	public function add_designer( $uid, $max_tasks = 1, $ability = 0 ){
		return $this->insert([
			'uid' => $uid,
			'max_tasks' => $max_tasks,
			'ability' => $ability
		]);
	}

	public function del_designer( $uid ){
		return $this->where( 'uid', $uid )
				->assign(['status'=>self::STATUS_DELETED])
				->save();
	}
	/**
	 * 通过id获取设计师
	 */
	public function get_designer_by_id($designer_id) {
		$designer = self::find($designer_id);

		return $designer;
	}

	/**
	 * 通过id集合获取设计师
	 */
	public function get_designer_by_ids($designer_ids) {
		#$designers = self::whereRaw(" FIND_IN_SET (id ,$designer_ids)")
		#->get();
		$designers = self::whereIn('id', $designer_ids)
			->get();

		return $designers;
	}
}

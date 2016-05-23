<?php
namespace App\Models\Parttime;
<<<<<<< HEAD

class Designer extends ModelBase
{

}
=======
use App\Models\ModelBase;

class Designer extends ModelBase {
	protected $connection = 'db_parttime';
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
>>>>>>> 6fe44af392fb91a6c0396916ac3cc82fb8d3194d

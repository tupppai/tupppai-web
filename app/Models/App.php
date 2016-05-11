<?php
namespace App\Models;

class App extends ModelBase {

	protected $table = 'recommend_apps';

	public function beforeCreate() {
		$this->order_by = 9999;
		$this->create_time = time();
		return $this;
	}
	public function scopeType($query, $status) {
		if ($status == self::STATUS_DELETED) {
			return $query->where('del_time', '!=', 0);
		} else {
			return $query->where('del_time', '=', 0);
		}
	}

	/**
	 * app分页方法
	 */
	public function page($name, $page = 1, $limit = 3) {
		$builder = self::query_builder()->select('id, app_name, jumpurl');

		$builder->where('app_name', 'LIKE', '%' . $name . '%')
			->Where('del_timexxxxx IS NULL')
			->orderBy('order_by ASC');
		return self::query_page($builder, $page, $limit);
	}

	public function getImageUrl() {
	}

	public function getApps($name, $status, $page = 1, $size = 15) {
		// todo: configurize
		define('APP_LIST_NUM', 3);
		$apps = $this->leftjoin('uploads', 'recommend_apps.id', '=', 'logo_upload_id')
			->where('app_name', 'LIKE', '%' . $name . '%')
			->type($status)
			->select(['recommend_apps.*', 'uploads.savename'])
			->orderBy('order_by', 'ASC')
			->forPage($page, $size);
		$data = $apps->get();
		$total = $apps->count();

		return $apps = ['data' => $data->toArray(), 'recordsTotal' => $total, 'recordsFiltered' => $total];
	}

	public function deleteApps($uid) {
		$this->assign(array(
			'del_by' => $uid,
			'del_time' => time(),
		));
		return $this->save();
	}

	public function getAppById($id) {
		return self::find($id);
	}
}

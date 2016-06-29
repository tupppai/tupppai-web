<?php
namespace App\Http\Controllers\Admin;

use App\Services\Parttime\Task as sTask;

class TaskController extends ControllerBase {
	public function indexAction() {
		return sTask::assign();
	}
}

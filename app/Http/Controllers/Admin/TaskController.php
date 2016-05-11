<?php
namespace App\Http\Controllers\Admin;

use App\Services\Ask as sAsk;
use App\Services\Parttime\Designer as sDesigner;

class TaskController extends ControllerBase {
	public function indexAction() {
		$waitingQueue = sAsk::waitingQueue();
		$designersQueue = sDesigner::abilityQueue();
		dd($designersQueue);
		echo count($waitingQueue);
	}
}

<?php
namespace App\Http\Controllers\Admin;

use App\Services\Ask as sAsk;
use App\Services\Parttime\Designer as sDesigner;

class TaskController extends ControllerBase {
	public function indexAction() {
		//需要分配给多少个兼职（这个和需求值相关）的计算阈值
		$threshold = 400;
		$waitingQueue = sAsk::waitingQueue();
		$designersQueue = sDesigner::abilityQueue();
		foreach ($waitingQueue as $waiting) {
			//若设计师队列已空，则无设计师可用，跳出循环
			if (empty($designersQueue)) {
				break;
			}
			var_dump($waiting['id']);
			//计算当前任务应当分配给几位设计师
			$needDesigners = (int) ceil($waiting['priority'] / $threshold);
			foreach ($designersQueue as $key => &$designer) {
				//TODO:检查该设计师是否已接过当前任务，若接过并取消就不要再分配给他
				//开始分配任务
				echo '给设计师' . $designer['uid'] . '分配了任务' . $waiting['id'] . ',该设计狮剩余可接任务数为' . $designer['avaliable_tasks'] . '<br>';
				//减少当前设计师可接任务数，若已为0，则从队列中删除当前设计师
				$designer['avaliable_tasks']--;
				if ($designer['avaliable_tasks'] <= 0) {
					unset($designersQueue[$key]);
				}
				//减少当前任务所需分配的设计师数，若当前任务需要分配的设计师数以达成，则跳出循环
				$needDesigners--;
				if ($needDesigners <= 0) {
					break;
				}
			}
		}
		var_dump($designersQueue);
	}
}

<?php
namespace App\Services\Parttime;
use App\Services\ServiceBase;
use App\Models\Parttime\Designer as mDesigner;
use App\Models\Parttime\Assignment as mAssignment;
use DB;

class Designer extends ServiceBase
{
	public static function abilityQueue()
	{
		$aDesigners = [];
		//取出可用的设计师
		mDesigner::where('status', 1)
			->chunk(1000, function ($designers) use (&$aDesigners) {
				foreach ($designers as $designer) {
					$aDesigners[$designer->uid] = $designer->toArray();
					$aDesigners[$designer->uid]['assigned_tasks'] = 0;
					$aDesigners[$designer->uid]['task_30_finished'] = 0;
					$aDesigners[$designer->uid]['task_7_finished'] = 0;
					$aDesigners[$designer->uid]['task_3_finished'] = 0;
				}
			});
		//根据可用设计师id抓出其当前对应的任务数
		$designerIds = array_column($aDesigners, 'uid');
		mAssignment::select('assigned_to as uid', DB::raw('count(id) as count'))
			->whereIn('assigned_to', $designerIds)
			->where('status', 1)
			->groupBy('assigned_to')
			->chunk(10000, function ($assignments) use (&$aDesigners) {
				foreach ($assignments as $assignment) {
					$aDesigners[$assignment->uid]['assigned_tasks'] = $assignment->count;
				}
			});
		//抓出近30、7、3天的任务处理情况
		$designerIds = array_column($aDesigners, 'uid');
		$Deadline30 = strtotime('-30 day');
		$Deadline7 = strtotime('-7 day');
		$Deadline3 = strtotime('-3 day');
		mAssignment::select('assigned_to as uid', 'status', 'create_time')
			->whereIn('assigned_to', $designerIds)
			->where('create_time', '>', $Deadline30)
			->whereIn('status', [2, 3])
		// ->groupBy('assigned_to')
			->chunk(10000, function ($assignments) use (&$aDesigners, $Deadline7, $Deadline3) {
				foreach ($assignments as $assignment) {
					//避免数据库的复杂处理，采用php来对任务数计数
					$aDesigners[$assignment->uid]['task_30_finished']++;
					if ($assignment->create_time > $Deadline7) {
						$aDesigners[$assignment->uid]['task_7_finished']++;
						if ($assignment->create_time > $Deadline3) {
							$aDesigners[$assignment->uid]['task_3_finished']++;
						}
					}
					// TODO:计算处理质量
				}
			});
		//加权计算近30、7、3天的任务率得出能力值(PS:当前未计入处理质量因素)
		//并计算可接任务数
		foreach ($aDesigners as &$designer) {
			$designer['ability'] =
				$designer['task_30_finished'] / 30 * 1 +
				$designer['task_7_finished'] / 7 * 2 +
				$designer['task_3_finished'] / 3 * 5;
			//暂时按近期平均每天接的任务数加权计算当前可接的任务数,至少可接1件
			$designer['max_tasks'] = (int) ceil($designer['ability'] / 8);
			if ($designer['max_tasks'] < 1) {
				$designer['max_tasks'] = 1;
			}
		}
		//统计设计狮当前可接的任务数
		foreach ($aDesigners as &$designer) {
			$designer['avaliable_tasks'] = $designer['max_tasks'] - $designer['assigned_tasks'];
			unset($designer['assigned_tasks']);
		}
		//过滤已接满任务的设计狮
		$aDesigners = array_filter($aDesigners, function ($designer) {
			if ($designer['avaliable_tasks'] > 0) {
				return true;
			}
		});
		usort($aDesigners, function ($a, $b) {
			return $a['ability'] > $b['ability'] ? -1 : 1;
		});
		return $aDesigners;
	}
}

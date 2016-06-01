<?php
namespace App\Http\Controllers\Main;
use App\Models\Ask as mAsk;
use App\Models\Parttime\Assignment as mAssignment;
use App\Services\Ask as sAsk;
use App\Services\Download as sDownload;
use App\Services\Parttime\Assignment as sAssignment;
use App\Services\Reply as sReply;

class TaskController extends ControllerBase {
	public function index($type) {
		switch ($type) {
			case 'doing':
				$status = [mAssignment::ASSIGNMENT_STATUS_DISPATCH, mAssignment::ASSIGNMENT_STATUS_RECEIVE];
				break;
			case 'finished':
				$status = [mAssignment::ASSIGNMENT_STATUS_FINISHED, mAssignment::ASSIGNMENT_STATUS_GRADED];
				break;
			default:
				error('TYPE_NOT_EXIST', '类型不存在');
		}
		$uid         = _uid();
		$page        = $this->post('page', 'int', 1);
		$size        = $this->post('size', 'int', 15);
		$assignments = sAssignment::getAssignmentsByUid($uid, $page, $size, $status);
		$data        = [];
		foreach ($assignments as $assignment) {
			$data[] = sAssignment::detail($assignment);
		}
		return $this->output($data);
	}

	public function record($id) {
		$this->isLogin();
		$uid        = $this->_uid;
		$assignment = sAssignment::getAssignmentById($id, [mAssignment::ASSIGNMENT_STATUS_DISPATCH, mAssignment::ASSIGNMENT_STATUS_RECEIVE], $uid);
		if (!$assignment) {
			error('ASSIGNMENT_NOT_EXIST', '任务不存在');
		}

		$type        = mAsk::TYPE_ASK;
		$target_id   = $assignment->ask_id;
		$category_id = 'undefined';
		$width       = 480;

		$url = sDownload::getFile($type, $target_id);

		if (!sDownload::hasDownloaded($uid, $type, $target_id)) {
			sDownload::saveDownloadRecord($uid, $type, $target_id, $url[0], $category_id);
		}
		//此处记录完成操作
		$result = sAssignment::recordStatus($assignment);

		return $this->output_json(array(
			'type'      => $type,
			'target_id' => $target_id,
			'url'       => $url,
		));
	}

	public function upload($id) {
		$this->isLogin();
		$uid        = $this->_uid;
		$assignment = sAssignment::getAssignmentById($id, [mAssignment::ASSIGNMENT_STATUS_DISPATCH, mAssignment::ASSIGNMENT_STATUS_RECEIVE], $uid);
		if (!$assignment) {
			error('ASSIGNMENT_NOT_EXIST', '任务不存在');
		}

		$ask_id      = $assignment->ask_id;
		$upload_id   = $this->post('upload_id', 'int');
		$desc        = $this->post('desc', 'string', '');
		$category_id = $this->post('category_id', 'int', 0);

		//$reply = sReply::addNewReply($uid, $ask_id, $upload_id, $desc);
		$reply = sReply::addNewReply($uid, $ask_id, $upload_id, $desc, $category_id);
		//$upload = sUpload::updateImages( array($upload_id), $scales, $ratios );

		fire('TRADE_HANDLE_REPLY_SAVE', ['reply' => $reply]);
		//此处记录完成操作
		$result = sAssignment::recordStatus($assignment, mAssignment::ASSIGNMENT_STATUS_FINISHED, $reply->id);
		return $this->output([
			'id'          => $reply->id,
			'ask_id'      => $ask_id,
			'category_id' => $category_id,
		]);
	}
	public function refuse($id) {
		$this->isLogin();
		$uid        = $this->_uid;
		$assignment = sAssignment::getAssignmentById($id, [mAssignment::ASSIGNMENT_STATUS_DISPATCH, mAssignment::ASSIGNMENT_STATUS_RECEIVE], $uid);
		if (!$assignment) {
			error('ASSIGNMENT_NOT_EXIST', '任务不存在');
		}
		$reason_type = $this->post('reason_type', 'int');
		$refuse_reason = $this->post('refuse_reason', 'string', '');
		if( !$refuse_reason ){
			switch ($reason_type) {
				case mAssignment::ASSIGNMENT_REASON_TYPE_IMPOSSIBLE:
					$refuse_reason = '原图质量过低';
					break;
				case mAssignment::ASSIGNMENT_REASON_TYPE_TOOHARD:
					$refuse_reason = '求P要求过高，无法完成';
					break;
				case mAssignment::ASSIGNMENT_REASON_TYPE_UNCLEAR:
					$refuse_reason = '求P描述不明确';
					break;
				case mAssignment::ASSIGNMENT_REASON_TYPE_NOINTEREST:
					$refuse_reason = '对此求P不敢兴趣';
					break;
				default:
					error('WRONG_REASON_TYPE', '类型错误');
					break;
			}
		}
		$result        = sAssignment::userRefuse($assignment, $reason_type, $refuse_reason);
		return $this->output([
			'result' => 'ok']);
	}

}

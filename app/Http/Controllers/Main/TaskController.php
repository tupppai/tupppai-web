<?php
namespace App\Http\Controllers\Main;
use App\Models\Ask as mAsk;
use App\Services\Ask as sAsk;
use App\Services\Download as sDownload;
use App\Services\Parttime\Assignment as sAssignment;
use App\Services\Reply as sReply;
use \DB;

class TaskController extends ControllerBase {
	public function index($type) {
		switch ($type) {
			case 'doing':
				$status = [1, 2];
				break;
			case 'finished':
				$status = [3, 4];
				break;
			default:
				abort(404);
				die;
		}
		$uid         = _uid();
		$page        = $this->post('page', 'int', 1);
		$size        = $this->post('size', 'int', 15);
		$assignments = sAssignment::getAssignmentsByUid($uid, $page, $size, $status);
		$data        = [];
		foreach ($assignments as $assignment) {
			$data[] = sAssignment::brief($assignment);
		}
		return $this->output($data);
	}
	public function record($id) {
		$this->isLogin();
		$uid        = $this->_uid;
		$assignment = sAssignment::getAssignmentById($id, [1, 2], $uid);
		if (!$assignment) {
			abort(404);
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
		$assignment = sAssignment::getAssignmentById($id, [1, 2], $uid);
		if (!$assignment) {
			abort(404);
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
		$result = sAssignment::recordStatus($assignment, 3);
		return $this->output([
			'id'          => $reply->id,
			'ask_id'      => $ask_id,
			'category_id' => $category_id,
		]);
	}
	public function refuse($id) {
		$this->isLogin();
		$uid        = $this->_uid;
		$assignment = sAssignment::getAssignmentById($id, [1, 2], $uid);
		if (!$assignment) {
			abort(404);
		}
		$reason_type   = $this->post('reason_type', 'int');
		$refuse_reason = $this->post('refuse_reason', 'string', '');
		$result        = sAssignment::userRefuse($assignment, $reason_type, $refuse_reason);
		return $this->output([
			'result' => 'ok']);
	}
}
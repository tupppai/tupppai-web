<?php
namespace App\Http\Controllers\Api;

use App\Counters\ReplyCounts as cReplyCounts;
use App\Jobs\Push;
use App\Models\Label as mLabel;
use App\Models\Message as mMessage;
use App\Models\Reply as mReply;
use App\Models\ThreadCategory as mThreadCategory;
use App\Services\Ask as sAsk;
use App\Services\Collection as sCollection;
use App\Services\Count as sCount;
use App\Services\Label as sLabel;
use App\Services\Message as sMessage;
use App\Services\Reply as sReply;
use App\Services\ThreadCategory as sThreadCategory;
use App\Services\Upload as sUpload;
use App\Services\User as sUser;

class ReplyController extends ControllerBase {
	public $_allow = array('index', 'show');
	/**
	 * 首页数据
	 */
	public function indexAction() {
		//todo: type后续改成数字
		$page = $this->get('page', 'int', 1);
		$size = $this->get('size', 'int', 15);

		$ask_id  = $this->get('ask_id', 'int', null);
		$replies = sReply::getReplies([], $page, $size, $ask_id);

		return $this->output($replies);
	}

	public function showAction($id) {
		$replies = sReply::getReplyById($id);

		cReplyCounts::inc($id, 'click');
		return $this->output(sReply::detail($replies));
	}

	/**
	 * 回复作品
	 */
	public function saveAction() {
		$ask_id      = $this->post('ask_id', 'int');
		$category_id = $this->post('category_id', 'int');
		$upload_id   = $this->post('upload_id', 'int');
		$ratio       = $this->post("ratio", "float", 0);
		$scale       = $this->post("scale", "float", 0);
		$label_str   = $this->post('labels', 'json');
		$desc        = $this->post('desc', 'json');
		$uid         = $this->_uid;

		if (!$upload_id) {
			return error('EMPTY_UPLOAD_ID');
		}
		if (!$ask_id && !$category_id) {
			return error('EMPTY_ASK_ID');
		}

		$upload = sUpload::updateImage($upload_id, $scale, $ratio);
		if ($category_id) {
			$reply = sReply::addNewReplyForActivity($uid, $category_id, $upload_id, $desc);
		} else {
			$reply = sReply::addNewReply($uid, $ask_id, $upload_id, $desc);
		}
		//$user   = sUser::addUserReplyCount($uid);

		$labels     = json_decode($label_str, true);
		$ret_labels = array();
		if (is_array($labels)) {
			foreach ($labels as $label) {
				$lbl = sLabel::addNewLabel(
					$label['content'],
					$label['x'],
					$label['y'],
					$uid,
					$label['direction'],
					$upload_id,
					$reply->id,
					mLabel::TYPE_REPLY
				);
				$ret_labels[$label['vid']] = array('id' => $lbl->id);
			}
		}
		//触发7天付款交易Jobs
		fire('TRADE_HANDLE_REPLY_SAVE', ['reply' => $reply]);
		return $this->output(array(
			'id'       => $reply->id,
			'reply_id' => $reply->id,
			'labels'   => $ret_labels,
		));
	}

	/**
	 * 保存多图作品
	 */
	public function multiAction() {
		$uid         = $this->_uid;
		$ask_id      = $this->post('ask_id', 'int', 0);
		$category_id = $this->post('category_id', 'int');
		$upload_ids  = $this->post('upload_ids', 'json_array');
		$ratios      = $this->post(
			'ratios',
			'json_array',
			config('global.app.DEFAULT_RATIO')
		);
		$scales = $this->post(
			'scale',
			'json_array',
			config('global.app.DEFAULT_SCALE')
		);
		$desc = $this->post('desc', 'string', '');

		if (!$upload_ids || empty($upload_ids)) {
			return error('EMPTY_UPLOAD_ID');
		}
		if (!$category_id) {
			$is_tutorial = sThreadCategory::checkedThreadAsCategoryType(mLabel::TYPE_ASK, $ask_id, mThreadCategory::CATEGORY_TYPE_TUTORIAL);
			if ($is_tutorial) {
				$category_id = mThreadCategory::CATEGORY_TYPE_TUTORIAL;
			}
		}

		//还是单张图片的求助
		$reply = sReply::addNewReply($uid, $ask_id, $upload_ids[0], $desc, $category_id);

		$upload = sUpload::updateImages($upload_ids, $scales, $ratios);

		fire('TRADE_HANDLE_REPLY_SAVE', ['reply' => $reply]);
		return $this->output([
			'id'          => $reply->id,
			'ask_id'      => $ask_id,
			'category_id' => $category_id,
		]);
	}

	public function deleteAction($id) {
		$status = mReply::STATUS_DELETED;

		$reply = sReply::getReplyById($id);
		sReply::updateReplyStatus($reply, $status, $this->_uid, "");

		return $this->output();
	}

	public function upReplyAction($id) {
		$status = $this->get('status', 'int', 1);
		$uid    = $this->_uid;

		sReply::upReply($id, $status);
		return $this->output(['result' => 'ok']);
	}

	public function collectReplyAction($id) {
		$status = $this->get('status', 'int', 1);
		$uid    = $this->_uid;

		$ret = sCollection::collectReply($uid, $id, $status);
		return $this->output(['collection' => $ret]);
	}

	public function informReplyAction($id) {
		$status = $this->get('status', 'int', 1);
		$uid    = $this->_uid;

		sReply::informReply($id, $status);
		return $this->output();
	}

	public function loveReplyAction($id) {
		$num    = $this->get('num', 'int', 1);
		$status = $this->get('status', 'int', mReply::STATUS_NORMAL);
		$uid    = $this->_uid;

		sReply::loveReply($id, $num, $status);
		return $this->output();
	}
}

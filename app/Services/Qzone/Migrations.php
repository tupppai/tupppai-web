<?php namespace App\Services\Qzone;

use App\Models\Ask as tmAsk;
use App\Models\Qzone\Praise;
use App\Models\Qzone\Qcomment as mQcomment;
use App\Models\Qzone\Qcomment;
use App\Models\Qzone\Question;
use App\Models\Reply as tmReply;
use App\Services\Comment as tsComment;
use App\Models\Qzone\Reply as mReply;
use App\Models\Qzone\User as mUser;
use App\Services\Ask as tsAsk;
use App\Services\Count as tsCount;
use App\Services\Follow as sFollow;
use App\Services\Reply as tsReply;
use App\Services\ServiceBase;
use App\Services\Upload as tsUpload;
use App\Models\User as tmUser;
use App\Services\User as tsUser;

class Migrations extends ServiceBase
{
	use \App\Traits\UploadImage;

	public static function inUsers()
	{
		//dd(self::getImage('http://thirdapp2.qlogo.cn/qzopenapp/21df6777a4fed8c937f590a88951c0dda274ced994af00b657c7fc93f4024690/50'));
		$page = 1;
		$users_count = mUser::count();
		$limit = ceil($users_count / 1000);
		$phone = 19000100001;
		for ($page; $page <= 2; $page++) {
			$old_users = mUser::forPage($page, $limit)->get();
			foreach ($old_users as $old_user) {
				$count = tmUser::where('nickname', $old_user->nickname)->count();

				if (0 == $count) {
					$new_user = new tmUser;
					$new_user->assign([
						'username' => emoji_to_shortname('qzone'),
						'password' => password_hash(123456, PASSWORD_DEFAULT),
						'nickname' => emoji_to_shortname($old_user->nickname),
						'phone'    => $phone,
						'location' => '',
						'avatar'   => $old_user->figureurl,
						'sex'      => 1,
						'email'    => '',
					]);
					$ret = $new_user->save();

					// 自己关注自己
					sFollow::follow($ret->uid, $ret->uid, tmUser::STATUS_NORMAL);
					$phone++;
				}
			}
		}
	}

	public static function inAsks()
	{
		$page = 1;
		$users_count = Question::count();
		$limit = ceil($users_count / 10);
		for ($page; $page <= 10; $page++) {
			//获取准备导入Ask
			$old_asks = Question::forPage($page, $limit)->get();
			foreach ($old_asks as $old_ask) {
				$new_uploads_id = [];
				//获取用户信息
				$old_user = mUser::where('openid', $old_ask->openid)->first();
				$nickname = $old_user->nickname;
				//获取用户uid
				$new_user = tmUser::where('nickname', $nickname)->first();
				//写入Ask
				if ($new_user) {
					if (empty($old_ask->question_details)) {
						continue;
					}

					if (empty($old_ask->ps_url)) {
						continue;
					}
					//写入uploads
					$new_upload = tsUpload::addNewUpload('file.jpg', $old_ask->ps_url, $old_ask->ps_url, 1, 1, 1, null);
					//获取图片id
					$new_uploads_id[] = $new_upload->id;
					$new_ask = tsAsk::addNewAsk($new_user->uid, $new_uploads_id, $old_ask->question_details, null);
				}
			}
		}
	}

	public static function inReplies()
	{
		$page = 1;
		$users_count = mReply::count();
		$limit = ceil($users_count / 50);
		for ($page; $page <= 50; $page++) {
			$old_replies = mReply::forPage($page, $limit)->get();
			foreach ($old_replies as $old_reply) {
				if (empty($old_reply->question_id)) {
					continue;
				}
				$old_ask = Question::where('question_id', $old_reply->question_id)->first();
				if (empty($old_ask)) {
					continue;
				}
				$new_ask = tmAsk::where('desc', $old_ask->question_details)->first();
				if ($new_ask) {
					$new_upload = tsUpload::addNewUpload($old_reply->reply_url, $old_reply->reply_url, $old_reply->reply_url, 1, 1, 1, null);
					$new_reply = tsReply::addNewReply($new_ask->uid, $new_ask->id, $new_upload->id, '这家伙很懒,没留下任何只言片语', null);
				}
			}
		}
	}

	public static function inComment()
	{
		//qzone 只能导入ask的评论
		$ask_type = 1;
		//获取评论
		$old_comments = Qcomment::get();
		foreach ($old_comments as $old_comment) {
			if (empty($old_comment->qcomment_content)) {
				continue;
			}
			//获取用户信息
			$old_user = mUser::where('openid', $old_comment->openid)->first();
			$nickname = $old_user->nickname;
			//获取new用户uid
			$new_user = tmUser::where('nickname', $nickname)->first();
			if (!$new_user) {
				continue;
			}
			//求P
			$old_ask = Question::where('question_id', $old_comment->question_id)->first();
			if (empty($old_ask)) {
				continue;
			}
			$new_ask = tmAsk::where('desc', $old_ask->question_details)->first();
			if (empty($new_ask)) {
				continue;
			}
			$new_comment = tsComment::addNewComment($new_user->uid, $old_comment->question_details, $ask_type, $new_ask->id, 0);

		}
	}

	//导入点赞
	public static function praisesInCount()
	{
		$page = 1;
		$users_count = Praise::count();
		$limit = ceil($users_count / 50);
		for ($page; $page <= 50; $page++) {
//			$ask_type = 1;
			$reply_type = 2;
			//获取counts
			$old_counts = Praise::forPage($page, $limit)->get();
			foreach ($old_counts as $old_count) {
				//获取用户信息
				$old_user = mUser::where('openid', $old_count->openid)->first();
				$nickname = $old_user->nickname;
				//获取new用户uid
				$new_user = tmUser::where('nickname', $nickname)->first();
				if (!$new_user) {
					continue;
				}
				//作品点赞

//				(用图片地址来匹配也是不错的办法)
				$old_reply = mReply::where('reply_id', $old_count->reply_id)->first();
				$new_reply = mReply::where('pathname', $old_reply->reply_url)->first();
				$new_count = tsCount::addNewCount($new_user->uid, $new_reply->id, $reply_type, mReply::ACTION_UP, 1);

//				OR (根据ask 对应 reply取第一条)
//				$old_ask   = Question::where('question_id',$old_reply->question_id)->first();
//				$new_ask   = tmAsk::where('desc',$old_ask->question_details)->first();
//				if(empty($new_ask)){
//					continue;
//				}
//				$new_reply = tmReply::where('ask_id', $new_ask->id)->first();
//				$new_count = tsCount::addNewCount($new_user->uid, $new_reply->id, $reply_type, mReply::ACTION_UP, 1);
			}
		}
	}


}

<?php namespace App\Services\Dashen;

use App\Models\Ask as tmAsk;
use App\Models\Dashen\Ask as mAsk;
use App\Models\Dashen\Comment as mComment;
use App\Models\Comment as tmComment;
use App\Models\Dashen\Count as mCount;
use App\Models\Follow as tmFollow;
use App\Models\Reply as tmReply;
use App\Services\Comment as tsComment;
use App\Models\Dashen\Reply as mReply;
use App\Models\Dashen\Upload as mUpload;
use App\Models\Dashen\User as mUser;
use App\Services\Ask as tsAsk;
use App\Services\Count as tsCount;
use App\Services\Reply as tsReply;
use App\Services\ServiceBase;
use App\Services\Upload as tsUpload;
use App\Models\User as tmUser;
use App\Services\User as tsUser;

class Migrations extends ServiceBase
{

	public static function inUsers()
	{
		$old_users = mUser::all();
		foreach ($old_users as $old_user) {
			$count = tmUser::where('username', $old_user->username)->where('nickname', $old_user->nickname)->count();
			if (0 == $count) {
				$new_user = new tmUser;
				$new_user->assign(array(
					'username'=>emoji_to_shortname($old_user->username),
					'password'=>password_hash(123456,PASSWORD_DEFAULT),
					'nickname'=>emoji_to_shortname($old_user->nickname),
					'phone'=>$old_user->phone,
					'location'=>$old_user->location,
					'avatar'=>$old_user->avatar,
					'sex'=>$old_user->sex,
					'email'=>'',
				));
				$ret = $new_user->save();

				// 自己关注自己
				$mUser = new tmUser();
				$mFollow = new tmFollow();

				$friend = $mUser->get_user_by_uid($ret->uid);
				if (!$friend) {
					return false;
				}
				$mFollow->update_friendship($ret->uid, $ret->uid, tmUser::STATUS_NORMAL);
				//END
			}
		}
	}

	public static function inAsks()
	{
		//获取准备导入Ask
		$old_asks = mAsk::where('status', 1)->get();
		foreach ($old_asks as $old_ask) {
			if (empty($old_ask->desc)) {
				continue;
			}
			$new_uploads_ids = [];
			//导入图片
			$old_uploads_ids = $old_ask->upload_id;
			$old_uploads_ids = explode(',', $old_uploads_ids);
			foreach ($old_uploads_ids as $old_uploads_id) {
				//判断原库upload url 是否存在,存在在导入
				$old_upload = mUpload::where('id', $old_uploads_id)->first();
				if ($old_upload->savename) {
					//写入tupppai数据库
					$new_upload = tsUpload::addNewUpload($old_upload->filename, $old_upload->savename, $old_upload->url, $old_upload->ratio, $old_upload->scale, $old_upload->size, $old_upload->type);
					$new_uploads_ids[] = $new_upload->id;
				}
			}
			//获取图片id
			$new_uploads_ids = is_array($new_uploads_ids) ? $new_uploads_ids : [];
			//获取用户信息
			$old_user = mUser::where('uid', $old_ask->uid)->first();
			$old_upload = mUpload::find($old_ask->upload_id);
			//图片判断暂时没加上
			$username = $old_user->username;
			$nickname = $old_user->nickname;
			//获取用户uid
			$new_user = tmUser::where('username', $username)->where('nickname', $nickname)->first();
			//写入Ask
			if ($new_user) {
				$new_ask = tsAsk::addNewAsk($new_user->uid, $new_uploads_ids, $old_ask->desc, null);
			}
		}
	}

	public static function inReplies()
	{
		$old_replies = mReply::where('status', 1)->get();
		foreach ($old_replies as $old_reply) {
			if (empty($old_reply->desc)) {
				continue;
			}
			$old_user = mUser::where('uid', $old_reply->uid)->first();
			$old_upload = mUpload::find($old_reply->upload_id);
			$old_ask = mAsk::find($old_reply->ask_id);
			//图片判断暂时没加上
			$username = $old_user->username;
			$nickname = $old_user->nickname;
			$new_user = tmUser::where('username', $username)->where('nickname', $nickname)->first();
			if ($new_user) {
				$new_ask = tmAsk::where('desc', $old_ask->desc)->first();
				if ($new_ask) {
					$new_upload = tsUpload::addNewUpload($old_upload->filename, $old_upload->savename, $old_upload->url, $old_upload->ratio, $old_upload->scale, $old_upload->size, $old_upload->type);
					$new_reply = tsReply::addNewReply($new_user->uid, $new_ask->id, $new_upload->id, $old_reply->desc, null);
				}
			}

		}
	}

	public static function inComment()
	{
		$ask_type = 1;
		$reply_type = 2;
		//获取评论
		$old_comments = mComment::where('status', 1)->where('for_comment', 0)->get();
		foreach ($old_comments as $old_comment) {
			if (empty($old_comment->content)) {
				continue;
			}
			//获取用户信息
			$old_user = mUser::where('uid', $old_comment->uid)->first();
			$username = $old_user->username;
			$nickname = $old_user->nickname;
			//获取new用户uid
			$new_user = tmUser::where('username', $username)->where('nickname', $nickname)->first();
			if (!$new_user) {
				continue;
			}
			//求P
			if ($old_comment->type == $ask_type) {
				$old_ask = mAsk::find($old_comment->target_id);
				$new_ask = tmAsk::where('desc', $old_ask->desc)->first();
				$new_comment = tsComment::addNewComment($new_user->uid, $old_comment->content, $old_comment->type, $new_ask->id, $old_comment->reply_to);

			} elseif ($old_comment->type == $reply_type) {
				//作品
				$old_reply = mReply::find($old_comment->target_id);
				$new_reply = tmReply::where('desc', $old_reply->desc)->first();
				$new_comment = tsComment::addNewComment($new_user->uid, $old_comment->content, $old_comment->type, $new_reply->id, $old_comment->reply_to);

			}
		}
		//暂时不迁移回复
	}

	public static function inCount()
	{
		$ask_type = 1;
		$reply_type = 2;
		//获取counts
		$old_counts = mCount::where('status', 1)->get();
		foreach ($old_counts as $old_count) {
			//获取用户信息
			$old_user = mUser::where('uid', $old_count->uid)->first();
			$username = $old_user->username;
			$nickname = $old_user->nickname;
			//获取new用户uid
			$new_user = tmUser::where('username', $username)->where('nickname', $nickname)->first();
			if (!$new_user) {
				continue;
			}


			//求P
			if ($old_count->type == $ask_type) {
				$old_ask = mAsk::find($old_count->target_id);
				$new_ask = tmAsk::where('desc', $old_ask->desc)->first();
				$new_count = tsCount::addNewCount($new_user->uid, $new_ask->id, $old_count->type, $old_count->action, $old_count->status);

			} elseif ($old_count->type == $reply_type) {
				//作品
				$old_reply = mReply::find($old_count->target_id);
				$new_reply = tmReply::where('desc', $old_reply->desc)->first();
				$new_count = tsCount::addNewCount($new_user->uid, $new_reply->id, $old_count->type, $old_count->action, $old_count->status);
			}

		}
	}


}

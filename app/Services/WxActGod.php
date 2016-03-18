<?php
	namespace App\Services;

	use App\Models\ThreadCategory as mThreadCategory;
	use App\Services\ThreadCategory as sThreadCategory;
	use App\Services\Category as sCategory;
	use App\Services\User as sUser;
	use App\Services\Askmeta as sAskmeta;
	use App\Services\Ask as sAsk;
	use App\Services\Reply as sReply;
	use App\Services\Upload as sUpload;

	use App\Models\Askmeta as mAskmeta;

	class WxActGod extends ServiceBase
	{

		public static function actGod()
		{
			$arg = self::getActGodByPeoPleAndCategory();
			if ($arg === null) {
				return [
					'code' => 0,
					'data' => null,
				];//活动不存在
			}
			$category = $arg['category'];
			$ask = self::getActGodByAsk($category);
			if (!empty($ask)) {
				//被拒绝
				if ($ask->status == mThreadCategory::STATUS_REJECT) {
					return [
						'code' => -1,
						'data' => self::reject($ask),
					];
				} else {
					//成功
					if ($ask->status == mThreadCategory::STATUS_DONE) {
						$reply = sReply::getFirstReply($ask->id);

						//求P成功且有作品
						return [
							'code' => 2,
							'data' => [
								'image' => self::result($reply),
							],
						];
					}
					//
					if ($ask->status == mThreadCategory::STATUS_NORMAL || $ask->status == mThreadCategory::STATUS_HIDDEN) {
						//求P成功且没有作品
						return [
							'code' => 1,
							'data' => [
								'total_amount' => $arg['total_amount'],
								'left_amount'  => $arg['left_amount'],
							],
						];
					}
				}
			} else {
				//todo 做个跳转
				return [
					'code' => -2,
					'data' => null,
				];//没有发过求助
			}
		}

		public static function getActGodByAsk($category)
		{
			$ask = null;
			$uid = _uid();

			$thcat = sThreadCategory::getAsksByCategoryId($category->id, [mThreadCategory::STATUS_NORMAL], 1, 1, [mThreadCategory::STATUS_REJECT, mThreadCategory::STATUS_DONE,mThreadCategory::STATUS_HIDDEN, mThreadCategory::STATUS_NORMAL], $uid);

			if (!$thcat->isEmpty()) {
				$thcat = $thcat[0];
				$ask = sAsk::getAskById($thcat->target_id);
			}

			return $ask;
		}

		public static function getActGodByPeoPleAndCategory()
		{
			$min_requested_people = 5; //首页显示五位(虚拟人数)
			$category = sCategory::getCategoryByName('WXActGod');
			if (!$category) {
				return null;
			}
			$total_amount = ThreadCategory::countTotalRequests($category->id);//今天有多少位
			$today_amount = sThreadCategory::countTodaysRequest($category->id);//活动开始以来总攻多少位
			$left_amount = sThreadCategory::countLeftRequests($category->id);//前面还有多少位

			return [
				'total_amount' => ($total_amount + $min_requested_people),
				'today_amount' => $today_amount,
				'left_amount'  => $left_amount,
				'category'     => $category,
			];
		}

		//被拒绝
		public static function reject($ask)
		{

			$meta = sAskmeta::get($ask->id, mAskmeta::ASSIGN_RECORD_META_NAME);
			$records = json_decode($meta);

			$reject = json_decode(array_shift($records), true);
			$reject_user = sUser::getUserByUid($reject['oper_by']);
			$reject['username'] = $reject_user->username;

			return ['result' => $reject, 'request' => $ask->desc];
		}

		//成功且有作品
		public static function result($reply)
		{
			$upload_id = $reply->upload_id;
			$image_path = sUpload::getImageUrlById($upload_id);

			return $image_path;
		}
	}

<?php

	namespace App\Services;
	use App\Models\ThreadCategory as mThreadCategory;

	class ThreadCategory extends ServiceBase{
		public static function addCategoryToThread( $uid, $target_type, $target_id, $category_ids ){
			$threadCategory = new mThreadCategory();
			$threadCategory->fill([
				'create_by' => $uid,
				'target_type' => $target_type,
				'target_id' => $target_id,
				'category_ids' => $category_ids,
				'status' => mThreadCategory::STATUS_CHECKED
			])
			->save();
			return  $threadCategory;
		}

		public static function setCategory( $uid, $cond, $category_ids ){
			$mThreadCategory = new mThreadCategory();
			$thrdCat = $mThreadCategory->firstOrNew( $cond );
			$data = $cond;
			//todo:multi categories
			$data['status'] = mThreadCategory::STATUS_CHECKED;
			$data['category_ids'] = $category_ids;
			$data['update_by'] = $uid;
			$thrdCat = $thrdCat->fill( $data )->save();

			return $thrdCat;
		}

		public static function setCategoryOfThread( $uid, $id, $category_ids ){
			$cond = ['id'=>$id];
			return self::setCategory( $uid, $cond, $category_ids );
		}
		public static function setCategoryOfAsk( $uid, $target_id, $category_ids ){
			$cond = [
				'target_type' => mThreadCategory::TYPE_ASK,
				'target_id' => $target_id
			];
			return self::setCategory( $uid, $cond, $category_ids );
		}

		public static function setCategoryOfReply( $uid, $target_id, $category_ids ){
			$cond = [
				'target_type' => mThreadCategory::TYPE_REPLY,
				'target_id' => $target_id
			];
			return self::setCategory( $uid, $cond, $category_ids );
		}

		public static function getCategoryIdsByTarget( $target_type, $target_id ){
			$mThreadCategory = new mThreadCategory();
			$cond = [
				'target_id' => $target_id,
				'target_type' => $target_type
			];

			$catIds = $mThreadCategory->where( $cond )->pluck('category_ids');
			if( !$catIds ){
				$catIds = [0];
			}
			else{
				$catIds = explode( ',', $catIds );
			}
			return $catIds;
		}

		public static function setThreadStatus( $uid, $target_type, $target_id, $status, $reason = '' ){
			$mThreadCategory = new mThreadCategory();
			$cond = [
				'target_id' => $target_id,
				'target_type' => $target_type
			];

			$thrdCat = $mThreadCategory->where( $cond )->firstOrFail( );
			$data = $cond;
			$data['status'] = $status;
			$data['reason'] = $reason;
			$data['update_by'] = $uid;

			$thrdCat = $thrdCat->fill( $data )->save();

			return $thrdCat;
		}

	}

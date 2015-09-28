<?php

	namespace App\Services;
	use App\Models\ThreadCategory as mThreadCategory;

	class ThreadCategory extends ServiceBase{
		public static function addCategoryToThread( $uid, $target_type, $target_id, $category_id ){
			$threadCategory = new mThreadCategory();
			$threadCategory->assign([
				'create_by' => $uid,
				'target_type' => $target_type,
				'target_id' => $target_id,
				'category_id' => $category_id,
				'status' => mThreadCategory::STATUS_CHECKED
			])
			->save();
			return  $threadCategory;
		}

		public static function setCategory( $uid, $target_type, $target_id, $category_from, $category_id ){
			$mThreadCategory = new mThreadCategory();
			$cond = [
				'target_type' => $target_type,
				'target_id' => $target_id,
				'category_id' => $category_from
			];
			$thrdCat = $mThreadCategory->firstOrNew( $cond );
			$data = $cond;

			$data['status'] = mThreadCategory::STATUS_CHECKED;
			$data['category_id'] = $category_id;
			//todo::create_by
			$data['update_by'] = $uid;
			$thrdCat = $thrdCat->assign( $data )->save();

			return $thrdCat;
		}

		public static function setStatus( $uid, $cond, $status ){
			$mThreadCategory = new mThreadCategory();
			$thrdCat = $mThreadCategory->firstOrNew( $cond );
			$data = $cond;

			$data['update_by'] = $uid;
			$thrdCat = $thrdCat->assign( $data )->save();

			return $thrdCat;
		}

		public static function getCategoryIdsByTarget( $target_type, $target_id ){
			$mThreadCategory = new mThreadCategory();
			$cond = [
				'target_id' => $target_id,
				'target_type' => $target_type
			];

			$results = $mThreadCategory->where( $cond )->checked()->select('category_id')->get();
			$catIds = [];
			foreach( $results as $row ){
				$catIds[] = $row->category_id;
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

			$thrdCat = $thrdCat->assign( $data )->save();

			return $thrdCat;
		}

		public static function getValidThreadsByCategoryId( $category_id, $page = '1' , $size = '15' ){
			$mThreadCategory = new mThreadCategory();
			return $mThreadCategory->where( 'category_id', $category_id )
									->valid()
									->orderBy('update_time', 'DESC')
									->forPage( $page, $size )
									->get();
		}

		public static function getCheckedThreads( $category_id, $page = '1' , $size = '15' ){
			$mThreadCategory = new mThreadCategory();
			return $mThreadCategory->checked()
									->orderBy('update_time', 'DESC')
									->forPage( $page, $size )
									->get();
		}
	}

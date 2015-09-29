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
			$thrdCat = $mThreadCategory->set_category( $uid, $target_type, $target_id, $category_from, $category_id );
			return $thrdCat;
		}

		public static function getCategoryIdsByTarget( $target_type, $target_id ){
			$mThreadCategory = new mThreadCategory();

			$results = $mThreadCategory->get_category_ids_of_thread( $target_type, $target_id );
			$catIds = [];
			foreach( $results as $row ){
				$catIds[] = $row->category_id;
			}

			return $catIds;
		}

		public static function setThreadStatus( $uid, $target_type, $target_id, $status, $reason = ''  ){
			$mThreadCategory = new mThreadCategory();
			$thrdCat = $mThreadCategory->set_thread_status( $uid, $target_type, $target_id, $status, $reason );
			return $thrdCat;
		}

		public static function getValidThreadsByCategoryId( $category_id, $page = '1' , $size = '15' ){
            $mThreadCategory = new mThreadCategory();
			return $mThreadCategory->get_valid_threads_by_category( $category_id, $page, $size );
		}

		public static function getCheckedThreads( $category_id, $page = '1' , $size = '15' ){
			$mThreadCategory = new mThreadCategory();
			return $mThreadCategory->get_checked_threads( $category_id, $page , $size );
        }

        public static function getCheckedPopularThreads( $page = '1' , $size = '15' ){
            $mThreadCategory = new mThreadCategory();
            $category_id     = mThreadCategory::CATEGORY_TYPE_POPULAR;
			return $mThreadCategory->get_checked_threads( $category_id, $page , $size );
		}
	}

<?php
	namespace App\Models;

	class ThreadCategory extends ModelBase{
		protected $table = 'thread_categories';
		protected $guarded = ['id'];

		public function scopeValid( $query ){
			return $query->where('status', self::STATUS_NORMAL);
		}

		public function scopeChecked( $query ){
			return $query->where('status', self::STATUS_CHECKED);
		}

		public function scopeHot( $query ){
			$hotCategoryId = self::CATEGORY_TYPE_POPULAR;
			return $query->where( 'category_id', $hotCategoryId );
		}

		public function set_category( $uid, $target_type, $target_id, $category_from, $category_id ){
			$cond = [
				'target_type' => $target_type,
				'target_id' => $target_id,
				'category_id' => $category_from
			];
			$thrdCat = $this->firstOrNew( $cond );
			$data = $cond;

			$data['status'] = self::STATUS_CHECKED;
			$data['category_id'] = $category_id;
			//todo::create_by
			$data['update_by'] = $uid;
			return $thrdCat->assign( $data )->save();
		}
		public function get_category_ids_of_thread( $target_type, $target_id ){
			$cond = [
				'target_id' => $target_id,
				'target_type' => $target_type
			];

			$results = $this->where( $cond )->checked()->select('category_id')->get();

			return $results;
		}

		public function set_thread_status( $uid, $target_type, $target_id, $status, $reason ){
			$cond = [
				'target_id' => $target_id,
				'target_type' => $target_type
			];

			$thrdCat = $this->where( $cond )->firstOrFail( );
			$data = $cond;
			$data['status'] = $status;
			$data['reason'] = $reason;
			$data['update_by'] = $uid;

			$thrdCat = $thrdCat->assign( $data )->save();

			return $thrdCat;
		}

		public function get_valid_threads_by_category( $category_id, $page , $size ){
			return $this->where( 'category_id', $category_id )
						->valid()
						->orderBy('update_time', 'DESC')
						->forPage( $page, $size )
						->get();
		}

		public function get_checked_threads( $category_id, $page , $size ){
			return $this->where( 'category_id', $category_id )
			            ->checked()
						->orderBy('update_time', 'DESC')
						->forPage( $page, $size )
						->get();
		}

	}

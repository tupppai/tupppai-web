<?php
	namespace App\Models;

	class ThreadCategory extends ModelBase{
		protected $table = 'thread_categories';
		protected $guarded = ['id'];

		public function scopeChecked( $query ){
			return $query->where('status', self::STATUS_CHECKED);
		}

		public function scopeHot( $query ){
			$hotCategoryId = self::CATEGORY_TYPE_POPULAR;
			return $query->where( 'category_id', $hotCategoryId );
		}

		public function set_category( $uid, $target_type, $target_id, $category_id, $status ){
			$cond = [
				'target_type' => $target_type,
				'target_id' => $target_id,
				'category_id' => $category_id
			];
			$thrdCat = $this->firstOrNew( $cond );
			$data = $cond;
			if( !$thrdCat->id ){
				$data['create_by'] = $uid;
			}
			$data['status'] = $status;
			$data['category_id'] = $category_id;
			$data['update_by'] = $uid;
			return $thrdCat->assign( $data )->save();
		}
		public function get_category_ids_of_thread( $target_type, $target_id, $category_id ){
			$cond = [
				'target_id' => $target_id,
				'target_type' => $target_type
			];
			if( $category_id || intval($category_id) === 0){
				$cond['category_id'] = $category_id;
            }

			$results = $this->where( $cond )->get();

			return $results;
		}

		/**
		 * @param $uid
		 * @param $target_type
		 * @param $target_id
		 * @param $status
		 * @param $reason
         * @return mixed
         */
		public function set_thread_status( $uid, $target_type, $target_id, $status, $reason, $category_id = null ){
			$cond = [
				'target_id' => $target_id,
				'target_type' => $target_type
			];
			if( $category_id ){
				$cond['category_id'] = $category_id;
			}

			$thrdCat = $this->firstOrNew( $cond );
			$data = $cond;
			if( !$thrdCat->id ){
				$data['create_by'] = $uid;
				$data['category_id'] = 0;
			}
			$data['reason'] = $reason;
			$data['status'] = $status;
			$data['update_by'] = $uid;

			$thrdCat = $thrdCat->fill( $data )->save();
			return $thrdCat;
		}

		public function get_valid_threads_by_category( $category_id, $page , $size ){
			$tcTable = $this->table;
			return $this->leftjoin('asks', function($join) use ( $tcTable ){
							$join->on( $tcTable.'.target_id', '=', 'asks.id')
								->where($tcTable.'.target_type', '=', 1);
						})
						->leftjoin('replies', function($join) use ( $tcTable ){
							$join->on( $tcTable.'.target_id', '=', 'replies.id')
								->where($tcTable.'.target_type', '=', 2);
						})
						->where(function($query){
							$query->where(function($query){
					            $uid = _uid();
					            //加上自己的广告贴
					            if( $uid ){
					                $query = $query->where('replies.status','>', self::STATUS_DELETED )
												   ->orWhere([ 'replies.uid'=>$uid, 'replies.status'=> self::STATUS_BLOCKED ]);
					            }
					        })
					        ->orWhere(function($query){
					            $uid = _uid();
					            //加上自己的广告贴
					            if( $uid ){
					                $query = $query->where('asks.status','>', self::STATUS_DELETED )
												   ->orWhere([ 'asks.uid'=>$uid, 'asks.status'=> self::STATUS_BLOCKED ]);
					            }
					        });
						})
						->where( $tcTable.'.category_id', $category_id )
						->valid()
                        //跟后台管理系统的时间保持一致
                        ->orderBy( $tcTable.'.update_time', 'DESC')
						->forPage( $page, $size )
						->select( $tcTable.'.*' )
						->get();
		}

		public function get_checked_threads( $category_id, $page , $size ){
			return $this->where( 'category_id', $category_id )
			            ->checked()
						->orderBy('update_time', 'DESC')
						->forPage( $page, $size )
						->get();
		}

		public function delete_thread( $uid, $target_type, $target_id, $status, $reason, $category_id ){
			$cond = [
				'target_type' => $target_type,
				'target_id' => $target_id
			];
			if( $category_id ){
				$cond['category_id'] = $category_id;
			}
			$data = [
				'delete_by' => $uid,
				'status' => $status,
				'reason' => $reason
			];

			return $this->where( $cond )->update( $data );
		}
	}

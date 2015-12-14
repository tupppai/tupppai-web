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

        /**
         * 通过求助或者作品类型获取目录集合
         */
		public function get_category_ids_of_thread( $target_type, $target_id, $category_id = NULL, $status = NULL ){
			$cond = [
				'target_id' => $target_id,
				'target_type' => $target_type
			];
			if( !is_null( $category_id ) ){
				$cond['category_id'] = $category_id;
            }
            if( !is_null( $status ) ){
				$cond['status'] = $status;
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
                            //$query->where(self::_blocking('replies'))
                                //->orWhere(self::_blocking('asks'));
							$query->where(function($query){
					            $uid = _uid();
					            //加上自己的广告贴
					            $query = $query->where('replies.status','>', self::STATUS_DELETED );
					            if( $uid ){
									$query = $query->orWhere([ 'replies.uid'=>$uid, 'replies.status'=> self::STATUS_BLOCKED ]);
					            }
					        })
					        ->orWhere(function($query){
					            $uid = _uid();
					            //加上自己的广告贴
					            $query = $query->where('asks.status','>', self::STATUS_DELETED );
					            if( $uid ){
									$query = $query->orWhere([ 'asks.uid'=>$uid, 'asks.status'=> self::STATUS_BLOCKED ]);
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

		public function get_asks_by_category( $category_id, $status, $page, $size ){
			$tcTable = $this->table;
			if( !is_array( $status ) ){
				$status = [$status];
			}
			return $this->leftjoin('asks', function($join) use ( $tcTable ){
							$join->on( $tcTable.'.target_id', '=', 'asks.id')
								->where($tcTable.'.target_type', '=', self::TYPE_ASK);
						})
						->where(function($query){
				            $uid = _uid();
				            //加上自己的广告贴
				            $query = $query->where('asks.status','>', self::STATUS_DELETED );
				            if( $uid ){
								$query = $query->orWhere([ 'asks.uid'=>$uid, 'asks.status'=> self::STATUS_BLOCKED ]);
				            }
						})
						->where( $tcTable.'.category_id', $category_id )
						->whereIn( $tcTable.'.status', $status )
                        //跟后台管理系统的时间保持一致
                        ->orderBy( $tcTable.'.update_time', 'DESC')
						->forPage( $page, $size )
						->select( $tcTable.'.*' )
						->get();
		}

		public function get_valid_replies_by_category( $category_id, $page, $size ){
			$tcTable = $this->table;
			return $this->leftjoin('replies', function($join) use ( $tcTable ){
							$join->on( $tcTable.'.target_id', '=', 'replies.id')
								->where($tcTable.'.target_type', '=', self::TYPE_REPLY);
						})
						->where(function($query){
				            $uid = _uid();
				            //加上自己的广告贴
				            $query = $query->where('replies.status','>', self::STATUS_DELETED );
				            if( $uid ){
								$query = $query->orWhere([ 'replies.uid'=>$uid, 'replies.status'=> self::STATUS_BLOCKED ]);
				            }
						})
						->where( $tcTable.'.category_id', $category_id )
						->valid()
                        //跟后台管理系统的时间保持一致
                        ->orderBy( $tcTable.'.update_time', 'DESC')
						->forPage( $page, $size )
						->select( $tcTable.'.*' )
						->get();
		}

		public function get_checked_threads( $category_ids, $page , $size ){
			return $this->where( function($query) use ($category_ids) {
							if( $category_ids ){
								if( !is_array( $category_ids ) ){
									$category_ids = [ $category_ids ];
								}
								$query->whereIn( 'category_id', $category_ids );
							}
						})
			            ->checked()
						->orderBy('create_time', 'DESC')
						->forPage( $page, $size )
						->get();
        }

		public function get_threads_by_category_id( $category_id, $page = 1, $size = 15 ){
			$query = $this->where( 'category_id', $category_id )
						->orderBy('id', 'DESC');
			if( $page && $size ){
				return $query->forPage( $page, $size )
							 ->get();
			}
			else{
				return $query->count();
			}

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

		public function thread_has_parent_category_of( $target_type , $target_id, $parent_category_id ){
			return $this->leftjoin('categories', 'categories.id', '=', 'thread_categories.category_id')
						->where( 'target_type', $target_type )
						->where( 'target_id', $target_id )
						->where( 'pid', $parent_category_id )
						->exists();
		}
	}

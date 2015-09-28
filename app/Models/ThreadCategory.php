<?php
	namespace App\Models;

	class ThreadCategory extends ModelBase{
		protected $table = 'thread_categories';
		protected $guarded = ['id'];
		const STATUS_CHECKED = 2;
		const STATUS_NORMAL  = 1;
		const STATUS_DELETED = 0;
		const TYPE_ASK = 1;
		const TYPE_REPLY = 2;

		const CATEGORY_TYPE_POPULAR = 1;

		public function scopeValid( $query ){
			return $query->where('status', self::STATUS_NORMAL);
		}

		public function scopeChecked( $query ){
			return $query->where('status', self::STATUS_CHECKED);
		}

		public function scopeHot( $query ){
			$hotCategoryId = 4;//config('global');
			return $query->where( 'category_id', $hotCategoryId );
		}

	}

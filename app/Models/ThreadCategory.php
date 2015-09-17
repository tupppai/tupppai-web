<?php
	namespace App\Models;

	class ThreadCategory extends ModelBase{
		protected $table = 'thread_categories';
		protected $guarded = ['id'];
		const STATUS_NORMAL = 1;
		const STATUS_DELETED = 0;
		const TYPE_ASK = 1;
		const TYPE_REPLY = 2;
	}

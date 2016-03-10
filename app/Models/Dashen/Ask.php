<?php

namespace App\Models\Dashen;

use App\Models\ModelBase;

class Ask extends ModelBase
{
	public $connection = 'db_ds';

	//对应点赞
	public function like()
	{
		return $this->hasMany('App\Models\Dashen\Count','target_id','id');
	}


}

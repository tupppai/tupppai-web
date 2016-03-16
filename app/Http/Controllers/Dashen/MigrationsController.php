<?php namespace App\Http\Controllers\Dashen;

use App\Http\Controllers\Controller;
use App\Models\Dashen\User as mUser;
use App\Services\Dashen\Migrations as sMigrations;

class MigrationsController extends Controller
{
	public function show()
	{
//		set_time_limit(0);
//		sMigrations::inUsers();
//		echo 'users添加完毕';
//		sMigrations::inAsks();
//		echo 'Asks添加完毕';
//		sMigrations::inReplies();
//		echo 'Reply添加完毕';
//		sMigrations::inComment();
		sMigrations::inCount();
		echo '成功';
	}


}

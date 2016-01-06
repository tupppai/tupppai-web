<?php

use Illuminate\Database\Seeder;
use App\Models\Role as mRole;

class default_roles extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$roles = [
			[
				'id' => 1,
				'name' => 'help',
				'display_name' => '求助帐号'
			],[
				'id' => 2,
				'name' => 'work',
				'display_name' => '大神帐号'
			],[
				'id' => 3,
				'name' => 'parttime',
				'display_name' => '兼职帐号'
			],[
				'id' => 4,
				'name' => 'staff',
				'display_name' => '后台帐号'
			],[
				'id' => 5,
				'name' => 'newbie',
				'display_name' => '新用户'
			],[
				'id' => 6,
				'name' => 'general',
				'display_name' => '一般用户'
			],[
				'id' => 7,
				'name' => 'star',
				'display_name' => '明星用户'
			],[
				'id' => 8,
				'name' => 'blocked',
				'display_name' => '屏蔽用户'
			],[
				'id' => 9,
				'name' => 'blacklist',
				'display_name' => '黑名单'
			],[
				'id' => 10,
				'name' => 'critic',
				'display_name' => '评论帐号'
			],[
				'id' => 11,
				'name' => 'trustable',
				'display_name' => '信任用户'
			]
		];
		mRole::truncate();
		foreach( $roles as $role ){
			$role['create_time'] = time();
			$role['update_time'] = time();
			mRole::insert( $role );
		}
	}
}

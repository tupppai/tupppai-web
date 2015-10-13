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
	        	'name' => 'help',
	        	'display_name' => '求助帐号'
	        ],

	        [
	        	'name' => 'work',
	        	'display_name' => '大神帐号'
	        ],
	        [
	        	'name' => 'newbie',
	        	'display_name' => '新用户'
	        ],
	        [
	        	'name' => 'general',
	        	'display_name' => '一般用户'
	        ],
	        [
	        	'name' => 'blocked',
	        	'display_name' => '屏蔽用户'
	        ],
	        [
	        	'name' => 'blacklist',
	        	'display_name' => '黑名单'
	        ]
    	];
    	foreach( $roles as $role ){
    		$role['create_time'] = time();
    		$role['update_time'] = time();
        	mRole::insert( $role );
    	}
    }
}

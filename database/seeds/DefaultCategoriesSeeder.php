<?php

use Illuminate\Database\Seeder;
use App\Models\Category as mCategory;

class DefaultCategoriesSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$categories = [
			[
				'id' => 0,
				'name' => 'normal',
				'display_name' => '默认分类'
			],[
				'id' => 1,
				'name' => 'popular',
				'display_name' => '热门'
			],[
				'id' => 2,
				'name' => 'pc_popular',
				'display_name' => 'PC热门'
			],[
				'id' => 3,
				'name' => 'app_popoular',
				'display_name' => 'APP热门'
			]
		];
		mCategory::truncate();
		foreach( $categories as $category ){
			$category['create_time'] = time();
			$category['update_time'] = time();
			$category['create_by'] = 1;
			$category['pid'] = 0;
			$category['status'] = mCategory::STATUS_NORMAL;
			$id = mCategory::insertGetId( $category );
			mCategory::where('id', $id)->first()->update(['id'=>$category['id']]);
		}
	}
}

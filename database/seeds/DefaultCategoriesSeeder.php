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
				'display_name' => 'PC热门',
				'pid' => 1
			],[
				'id' => 3,
				'name' => 'app_popoular',
				'display_name' => 'APP热门',
				'pid' => 1
            ],[
                'id' => 4,
                'name' => 'activity',
                'display_name' => '活动',
                'pid' => 0
            ],[
				'id' => 5,
				'name' => 'channel',
				'display_name' => '频道',
				'pid' => 0
            ]
		];
		// mCategory::truncate();
		foreach( $categories as $category ){
			$data = $category;
			$data['create_time'] = time();
			$data['update_time'] = time();
			$data['create_by'] = 1;
			$data['status'] = mCategory::STATUS_NORMAL;
			$cat = mCategory::updateOrCreate( $category, $data );
			mCategory::where('id', $cat->id)->first()->update(['id'=>$category['id']]);
		}
	}
}

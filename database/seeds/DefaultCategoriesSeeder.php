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
            ],[
				'id' => 6,
				'name' => 'tutorial',
				'display_name' => '教程',
				'pid' => 0,
				'app_pic' => 'http://7u2spr.com1.z0.glb.clouddn.com/20160222-16252356cac5f31cecf.png'
            ],[
				'id' => 7,
				'name' => 'timeline',
				'display_name' => '动态',
				'pid' => 0
            ],[
				'id' => 8,
				'name' => 'wx_activity',
				'display_name' => '公众号活动',
				'pid' => 0
            ]
		];
		$category_base = config('global.CATEGORY_BASE');
		$category_table = (new mCategory)->getTable();
		mCategory::where('id','<=', $category_base )->delete();
		foreach( $categories as $category ){
			$category['create_time'] = time();
			$category['update_time'] = time();
			$category['create_by'] = 1;
			$category['status'] = mCategory::STATUS_NORMAL;
			$id = mCategory::insertGetId( $category );
			$sql = DB::raw('UPDATE '.$category_table.' SET id='.$category['id'].' WHERE id='.$id.';');
			DB::statement( $sql );
		}
		$sql = DB::raw('ALTER TABLE '.$category_table.' AUTO_INCREMENT = '. ($category_base+1) .';');
		DB::statement( $sql );
		//set auto increment 1000
	}
}

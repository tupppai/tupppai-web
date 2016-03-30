<?php namespace App\Counters;

use App\Models\Category as mCategory;
use App\Services\ThreadCategory as sThreadCategory;
use App\Services\Category as sCategory;
use DB;

class CategoryUpeds extends CounterBase {

    public static $key  = 'category_upeds_';
    
    public static function _key($category_id) {
        return $key = self::$key . $category_id;
    }

    /**
     * 获取计数数据
     */ 
    public static function get($category_id) {
        $key = self::_key($category_id);

        return self::query($key, function() use ($key, $category_id) {

            $mCategory  = new mCategory;
            $count      = $mCategory->where('id', $category_id)
                ->select('uped_count')
                ->pluck('uped_count');
            //todo: write back

            return self::put($key, $count);
        });
    }
 
    public static function inc($type, $id, $val = 1) {
        //if($type == TYPE_CATEGORY)
        $count = 0;

        $categories = sThreadCategory::getCategoriesByTarget( $type, $id);
        foreach($categories as $category) {
            $category = sCategory::getCategoryById($category->category_id);
            $count = self::get($category->id);

            
            if($count % 50 == 0) {
                $category->uped_count = $count;
                $category->save();
            }
            $count += self::increment(self::_key($category->id), $val);
        }

        return $count;
    }
}

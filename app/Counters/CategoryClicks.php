<?php namespace App\Counters;

use App\Models\Category as mCategory;
use App\Services\ThreadCategory as sThreadCategory;
use DB;

class CategoryClicks extends CounterBase {

    public static $key  = 'category_clicks_';
    
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
                ->select('click_count')
                ->pluck('click_count');
            //todo: write back

            return self::put($key, $count);
        });
    }

    
    public static function inc($category_id, $val = 1) {
        $count = self::get($category_id);
            
        if($count % 50 == 0) {
            $category = mCategory::find($category_id);
            $category->click_count = $count;
            $category->save();
        }

        return self::increment(self::_key($category_id), $val);
    }
}
